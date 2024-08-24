<?php

namespace Themeco\Cornerstone\Services;
use Themeco\Cornerstone\Util\Filesystem;
use Themeco\Cornerstone\Util\Networking;

class RemoteAssets implements Service {

  protected $env;
  protected $filesystem;
  protected $cacheKeyPrefix;

  static public $legacyTypes = ['site', 'preset'];

  public function __construct(Env $env, Filesystem $filesystem) {
    $this->env = $env;
    $this->filesystem = $filesystem;
  }

  public function setup() {
    add_action( 'cs_purge_tmp', [$this, 'clearCache'] );
    add_filter( 'cs_remote_asset_path', [$this, 'expandPackUrl'] );
  }

  public function _fetch( $path, $query = [], $args = [] ) {
    $env = $this->env->envData();
    if ( ! isset( $env['templates'] ) || empty( $env['templates']['url'] ) ) {
      throw new \Exception('No base URL available');
    }

    $url = strpos($path, "http") === 0
      ? $path
      : $env['templates']['url'] . $path;

    if ( ! empty( $query ) ) {
      $url = add_query_arg( $query, $url );
    }

    Networking::set_curl_timeout_begin( 30 );

    $request = wp_remote_get( $url, $args );

    if ( is_wp_error( $request ) ) {
      throw new \Exception('Failed to fetch remote assets | ' . $request->get_error_message());
    }

    return $request;
  }

  public function fetchFile( $path, $options = [] ) {

    $filename = get_temp_dir() . 'cs-' . wp_generate_password( 12, false, false ) . '.zip';

    $this->_fetch( $path, isset($options['query']) ? $options['query'] : [], [
      'stream' => true,
      'filename' => $filename
    ]);

    return $filename;

  }

  public function proxyFile( $path ) {
    $this->filesystem->sendFile($this->fetchFile($path));
  }

  public function fetch( $path, $options = [] ) {
    return json_decode( wp_remote_retrieve_body( $this->_fetch( $path, isset($options['query']) ? $options['query'] : []) ), true );
  }

  public function fetchSafe( $path, $options = [] ) {
    try {
      return $this->fetch( $path, $options );
    } catch (\Exception $e) {
      if (defined('WP_DEBUG') && WP_DEBUG) {
        trigger_error($e->getMessage(), E_USER_WARNING);
      }
    }
    return [];
  }

  public function getCacheKey($group) {
    if (! isset($this->cacheKeyPrefix) ) {
      $env = $this->env->envData();
      $this->cacheKeyPrefix = 'cs_remote_asset_data_' . md5($env['product'] . $env['templates']['url']);
    }

    return $this->cacheKeyPrefix . '_' . $group;
  }

  public function clearCache() {
    delete_site_transient( $this->getCacheKey('manifest') );
    delete_site_transient( $this->getCacheKey('legacyManifest') );
  }

  public function cleanManifestItems($groups) {
    $result = [];

    $env = $this->env->envData();

    foreach ($groups as $name => $items) {
      $result[$name] = [];
      foreach ($items as $item) {
        if (isset( $item['minCsVersion'] ) && ! version_compare( $item['minCsVersion'], CS_VERSION, '<=') && CS_VERSION !== 'dev' ) {
          continue;
        }
        if ( isset($item['gate']) && $item['gate'] !== $env['product'] ) {
          continue;
        }
        $item['isRemote'] = true;
        $result[$name][] = $item;
      }
    }

    return $result;
  }

  public function defaultGroups() {
    return [
      'page'   =>  "Pages",
      'post'   =>  "Posts",
      'header' =>  "Headers",
      'footer' =>  "Footers",
      'blog'   =>  "Blogs",
      'shop'   =>  "Shops",
      'misc'   =>  "Misc",
    ];
  }

  public function getAppData() {

    $env = $this->env->envData();
    if ( ! isset( $env['templates'] ) || empty( $env['templates']['url'] ) ) {
      return [
        'groups' => $this->defaultGroups()
      ];
    }

    $key = $this->getCacheKey('manifest');
    $cached = get_site_transient( $key );

    if ($cached === false) {
      $manifest = $this->fetchSafe('/manifest');
      $manifest['templates'] = $this->cleanManifestItems( $manifest['templates'] );
      $cached = $manifest;
      set_site_transient( $key, $cached, HOUR_IN_SECONDS );
    }

    $cached['groups'] = array_merge( $this->defaultGroups(), empty($cached['groups'] ) ? [] : $cached['groups']);

    if ( empty( $cached['templates']['sites'] ) ) {
      $cached['templates']['sites'] = [];
    }

    // legacy template merging
    $legacyTemplates = $this->getLegacySitesCached();
    $legacyTemplates = cs_array_group_by($legacyTemplates, 'groupKey');

    foreach ($legacyTemplates as $type => $templates) {
      // Plural
      $type .= 's';

      $typeTemplates = cs_get_array_value($cached['templates'], $type, []);
      $cached['templates'][$type] = array_merge($typeTemplates, $templates);
    }

    $cached = apply_filters('cs_app_remote_assets', $cached);

    return $cached;

  }

  public function getLegacySites() {
    $env = $this->env->envData();
    if ( ! isset( $env['templates'] ) || empty( $env['templates']['legacyUrl'] ) ) {
      return [];
    }

    $request = wp_remote_get( $env['templates']['legacyUrl'] . '/index' );

    if ( is_wp_error( $request ) ) {
      return [];
    }

    try {
      $items = json_decode( wp_remote_retrieve_body( $request ), true );

      $items = array_filter( $items, function( $item ) {
        if ( ! $this->validLegacyType($item) ) return false;

        if (empty ($item['status'])) return false;
        if ( $item['status'] !== 'publish' ) return false;
        return true;
      } );

      return array_values( array_map( function($item) use ($env) {
        $type = $item['asset_type'][0];

        $remapped = [
          'id' => 'legacy:' . $item['id'],
          'title' => $item['title'],
          'type' => $type,
          'groupKey' => $type,
          'preview' => $item['thumbnail_url'],
          'demo_url' => $item['demo_url'],
          'isRemote' => true,
          'legacyInstallUrl' => $env['templates']['legacyUrl'] . '/asset/' . $item['id']
        ];

        // Preset remap
        if ($type === 'preset') {
          $remapped['type'] = 'pack';
          $remapped['subType'] = '__multi__';
          $remapped['groupKey'] = 'pack';

          $remapped['id'] = 'cs-pack:' . $remapped['legacyInstallUrl'];
        }


        if ( isset( $item['pro_only'] ) && $item['pro_only'] ) {
          $remapped['gate'] = 'pro';
        }
        return $remapped;
      }, $items ) );
    } catch( \Exception $e) {
      return [];
    }
  }

  public function getLegacySitesCached() {
    $key = $this->getCacheKey('legacyManifest');
    $cached = false;//get_site_transient( $key );

    if ($cached === false) {
      $cached = $this->getLegacySites();
      set_site_transient( $key, $cached, HOUR_IN_SECONDS );
    }

    return $cached;
  }

  /**
   * Check legacy item if valid type
   */
  public function validLegacyType($item) {
    if (empty($item['asset_type'])) {
      return false;
    }

    foreach (static::$legacyTypes as $type) {
      if (!in_array($type, $item['asset_type'])) {
        continue;
      }

      return true;
    }

    return false;
  }

  /**
   * Expands an id cs-pack to the full url
   * of the package to grab
   */
  public function expandPackUrl($url) {
    if (strpos($url, 'cs-pack:') !== 0) {
      return $url;
    }

    $packURL = str_replace('cs-pack:', '', $url);

    // Grab pack manifest to get the package.url
    $presetData = wp_remote_get($packURL);
    $presetData = json_decode(wp_remote_retrieve_body($presetData), true);

    if (empty($presetData)) {
      trigger_error("Could not find preset data for ID " . $packURL);
      return [];
    }

    $packageUrl = cs_get_path($presetData, 'package.url', '');

    return $packageUrl;
  }
}
