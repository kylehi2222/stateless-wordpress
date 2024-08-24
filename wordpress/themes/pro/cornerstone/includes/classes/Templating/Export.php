<?php

namespace Themeco\Cornerstone\Templating;

use Themeco\Cornerstone\Services\GlobalColors;
use Themeco\Cornerstone\Services\GlobalFonts;
use Themeco\Cornerstone\Services\Components;
use Themeco\Cornerstone\Documents\DocumentCache;
use Themeco\Cornerstone\Util\Factory;

class Export {

  public $options = [];
  public $items = [];
  public $images = [];
  public $tasks = [];
  public $docOrder = [];
  public $cache = [];
  public $posts = [];
  public $dependencies = [];
  public $organized = false;
  public $salt;

  public static $colors;
  public static $fonts;
  public static $attachment_ids;
  public static $component_document_ids;
  public $archiveZip;

  public $globalColors;
  public $globalFonts;
  public $components;
  public $documentCache;

  public function __construct(DocumentCache $documentCache, GlobalColors $globalColors, GlobalFonts $globalFonts, Components $components) {
    $this->globalColors = $globalColors;
    $this->globalFonts = $globalFonts;
    $this->components = $components;
    $this->documentCache = $documentCache;
  }

  public function setOption($key, $value) {
    $this->options[$key] = $value;
    return $this;
  }

  public function getOption($key) {
    return isset( $this->options[$key] ) ? $this->options[$key] : null;
  }

  public function isFullSite() {
    return $this->getOption('fullSite') === true;
  }

  public function getColors() {
    if ( ! isset( self::$colors) ) {
      self::$colors = $this->globalColors->getStoredColorItems();
    }
    return self::$colors;
  }

  public function getFonts() {
    if ( ! isset( self::$fonts) ) {
      self::$fonts = $this->globalFonts->get_font_items();
    }
    return self::$fonts;
  }

  public function getAttachmentIds() {
    if ( ! isset( self::$attachment_ids) ) {
      global $wpdb;
      $attachment_ids = $wpdb->get_results("SELECT ID FROM {$wpdb->prefix}posts WHERE post_type = \"attachment\"", ARRAY_N);
      self::$attachment_ids = array_map(function($a) {
        return (int) $a[0];
      }, $attachment_ids);
    }
    return self::$attachment_ids;
  }

  public function getComponentDocumentIds() {
    if ( ! isset( self::$component_document_ids) ) {
      global $wpdb;
      $component_document_ids = $wpdb->get_results("SELECT ID FROM {$wpdb->prefix}posts WHERE post_type = \"cs_global_block\"", ARRAY_N);
      self::$component_document_ids = array_map(function($a) {
        return (int) $a[0];
      }, $component_document_ids);
    }
    return self::$component_document_ids;
  }

  public function hashSalt() {
    if ( !isset( $this->salt ) ) {
      $salt = '';
      if ( is_multisite() ) {
        $salt .= get_current_blog_id();
      }
      return apply_filters( 'cs_export_hash_salt', $salt );
    }
    return $this->salt;
  }

  public function hash($group, $input) {
    return md5($this->hashSalt() . ':' . $group . ':' . $input);
  }

  public function add( $postId, $group = 'template', $hash = null ) {

    if ( is_array( $postId ) ) {

      foreach ( $postId as $id ) {
        $this->add($id, $group);
      }
      return $this;
    }

    $id = "t$postId";

    list($post, $dependencies) = $this->getCachedPost( $postId );

    $firstTime = false;
    if ( ! isset( $this->posts[ $id ] ) ) {
      $firstTime = true;
      $_hash = $hash ? $hash : $this->hash('doc', $postId);
      $this->posts[$id] = [ 'type' => $group, 'key' => $_hash, 'data' => $post ];
    }

    $this->docOrder[] = $id;

    foreach ($dependencies as $key => $value) {
      if ($value[0] === 'component') {
        $this->add($value[1], 'component', $key);
        continue;
      }
      if ($value[0] === 'doc') {
        $this->add($value[1], 'doc', $key);
        continue;
      }
      if ($firstTime) {
        $this->dependencies[$key] = $value;
      }

    }

    return $this;
  }

  /**
   * Add menus as task
   */
  public function addMenus() {
    $menus = wp_get_nav_menus();

    foreach ($menus as $menu) {
      $this->addMenu($menu);
    }
  }

  /**
   * Add single menu as menu task
   */
  public function addMenu($menu) {
    // Grab items from menu
    $items = wp_get_nav_menu_items($menu);

    $itemsFormatted = [];
    $itemChildren = [];

    foreach ($items as $item) {
      // Get signature ID
      // for use in import
      $cachedID = $this->hash('sig', $item->object_id);
      $postTitle = $item->post_title;

      // Custom links already have a post tile
      if (empty($postTitle)) {
        $navPost = get_post($item->object_id);
        $postTitle = $navPost->post_title;
      }

      // add item we use the update keys
      // for easier importing
      $navItem = [
        'menu-item-title' => $postTitle,
        'menu-item-object-id' => $cachedID,
        'menu-item-object' => $item->object,
        'menu-item-status' => 'publish',
        'menu-item-type' => $item->type,
      ];

      // Top level
      if (empty($item->menu_item_parent)) {
        $itemsFormatted[$item->ID] = $navItem;
      } else {
        // Item children setup
        if (empty($itemChildren[$item->menu_item_parent])) {
          $itemChildren[$item->menu_item_parent] = [ 'children' => [] ];
        }

        // @TODO this is not recursive
        // we just needed to finish the Personify Project
        // Child menu item
        $itemChildren[$item->menu_item_parent]['children'][] = $navItem;
      }
    }

    // Loop children and add to parents
    // this is done later so the order of the top level
    // is not affected
    foreach ($itemChildren as $parentID => $group) {
      if (empty($itemsFormatted[$parentID]['children'])) {
        $itemsFormatted[$parentID]['children'] = [];
      }

      foreach ($group['children'] as $child) {
        $itemsFormatted[$parentID]['children'][] = $child;
      }
    }

    // Add task
    $task = ['menu', [
      'name' => $menu->name,
      'items' => $itemsFormatted,
    ]];

    $this->tasks[] = $task;
  }

  public function getCachedPost( $postId ) {
    $id = "t$postId";
    if ( ! isset( $this->cache[ $id ] ) ) {
      list($template, $post, $isTemplate) = $this->getPost( $postId );
      $this->cache[$id] = Factory::create(DependencyMapper::class)
        ->setup($this, $template, $post, $isTemplate)
        ->enumerate();
    }
    return $this->cache[ $id];
  }

  public function getPost( $postId ) {
    $post = get_post($postId);

    if ($post->post_type !== 'cs_template') {
      add_filter( 'cs_is_template_export', '__return_true' );


      $document = $this->documentCache->get( $postId );
      $template = apply_filters( '_cs_supply_export_template', null, $document );

      if (is_null( $template ) ) {
        $template = Template::create( 'document', $document->getDocType(), true );
        $data = $document->data();
        $template->setMeta( [
          'elements' => $data['elements'],
          'settings' => $data['settings']
        ], false);
      }

      $template->setTitle( $document->title() );

      remove_filter( 'cs_is_template_export', '__return_true' );
      return [$template, $post, false];

    }

    $template = Template::locate($post);
    $template->loadMeta();
    return [$template, $post, true];

  }


  public function getDependantColorsOrFonts( $ids, $stored ) {
    $fullSite = $this->isFullSite();
    $used = [];
    $groupMap = [];
    $groups = [];

    foreach ($stored as $color) {
      if (isset($color['children'])) {
        foreach ($color['children'] as $child ) {
          $groupMap[$child] = $color['_id'];
          $groups[$color['_id']] = $color['children'];
        }
      }
    }
    foreach ($stored as $color) {

      if ( $fullSite || in_array( $color['_id'], $ids, true ) ) {
        if ( isset( $groupMap[ $color['_id']] ) ) {
          $group = $groupMap[ $color['_id']];
          $used[] = $group;
          $used = array_merge($used, $groups[$group]);
        }
        $used[] = $color['_id'];
      }
    }

    $used = array_unique($used);

    $result = [];

    foreach ($stored as $color) {
      if ( in_array( $color['_id'], $used, true ) ) {
        $result[] = $color;
      }
    }

    return $result;

  }

  public function parseDependencies() {
    $font_ids = [];
    $color_ids = [];
    $images = [];
    $options = [];

    if ($this->isFullSite()) {
      $options['customCSS'] = cornerstone('ThemeOptions')->get_global_css();
    }

    foreach ($this->dependencies as $key => $value) {
      list ($type, $content) = $value;
      if ($type === 'global-font') {
        $font_ids[] = $content;
        continue;
      }

      if ($type === 'global-color') {
        $color_ids[] = $content;
        continue;
      }

      if ($type === 'image-uri') {
        $images[$key] = ['uri' => $content];
        continue;
      }

      if ($type === 'image-attachment') {
        $images[$key] = [ 'id' => $content[0], 'uri' => $content[1] ];
        continue;
      }

    }

    $colors = $this->getDependantColorsOrFonts( $color_ids, $this->getColors() );

    if ( ! empty($colors) )  {
      $options['colors'] = $colors;
    }

    $fonts = $this->getDependantColorsOrFonts( $font_ids, $this->getFonts() );

    if ( ! empty($fonts) )  {
      $options['fonts'] = $fonts;
    }

    if ( ! empty($options) )  {
      $this->tasks[] = ['options', $options];
    }

    if (count($images) > 0) {
      $this->tasks[] = ['images', $images];
    }

  }

  public function addDocuments() {
    // $this->docOrder includes all docs in the order they are requested.
    // By reversing this, and only adding unique entries, we can ensure
    // documents are created in the order they are needed.
    // A document that depends on another will always be able to look that up by it's hash/key
    $addedDocs = [];
    $reversed = array_reverse($this->docOrder);
    foreach ($reversed as $doc) {
      if ( in_array( $doc, $addedDocs, true) ) {
        continue;
      }
      $addedDocs[] = $doc;

      $this->tasks[] = ['doc',$this->posts[$doc]];
    }
  }

  public function includeTerms() {
    $terms = [];

    $addTerm = function($id, $key) use (&$terms, &$addTerm) {
      $term = get_term($id);
      $terms[$key] = [
        'name' => $term->name,
        'slug' => $term->slug,
        'description' => $term->description,
        'taxonomy' => $term->taxonomy
      ];

      if ( $term->parent ) {
        $addTerm($term->parent, $this->hash('term', $term->parent ));
      }
    };
    foreach ($this->dependencies as $key => $value) {
      list ($type, $content) = $value;
      if ($type === 'term') {
        $addTerm($content, $key);
      }
    }

    return $terms;

  }

  public function organize() {

    if ( $this->organized ) {
      return $this;
    }

    $terms = $this->includeTerms();
    $this->parseDependencies();
    $this->addDocuments();

    $this->tasks[] = ['terms', $terms];

    // $this->tasks is a list of things to import
    // Tasks must be imported in the order they are found to ensure dependencies are created first

    $this->organized = true;
    return $this;

  }

  public function archive() {

    if ( ! class_exists( 'ZipArchive' ) ) {
      return new \WP_Error( 'missing_zip_package', __( 'Export not supported please install php-zip.', 'cornerstone' ) );
    }

    $this->archiveZip = get_temp_dir() . 'cs-' . wp_generate_password( 12, false, false ) . '.zip';

    $zip = new \ZipArchive();
    if ( true !== $zip->open( $this->archiveZip, \ZipArchive::CREATE | \ZipArchive::OVERWRITE ) ) {
      return new \WP_Error( 'unable_to_create_zip', __( 'Unable to open export file (archive) for writing.', 'cornerstone' ) );
    }

    $manifest = [];
    foreach ($this->tasks as $task) {
      list($type, $content) = $task;
      if ($type === 'doc') {
        $file = 'doc-' . $content['key'] . '.json';
        $zip->addFromString( $file, json_encode($content['data']) );
        $manifest[] = ['doc', [
          'type' => $content['type'],
          'strategy' => apply_filters('cs_export_doc_strategy', 'original', $content ),
          'key' => $content['key'],
          'file' => $file
          ] ];
      } else if ($type === 'images') {
        $images = [];
        foreach ($content as $key => $value) {
          $images[$key] = $this->addImage($zip, $key, $value);
        }
        $manifest[] = ['images', $images];
      } else {
        $manifest[] = $task;
      }
    }

    $zip->addFromString( 'manifest.json', json_encode([ 'version' => 3, 'tasks' => $manifest ], JSON_PRETTY_PRINT) );
    $zip->close();
    return $this->archiveZip;

  }

  public function addImage($zip, $key, $value) {

    $parts = explode('/', $value['uri']);
    $original = array_pop($parts);
    $fname = 'img-' . $key . '-' . $original;

    if ( isset( $value['id'] ) ) {
      $path = wp_get_original_image_path( $value['id'], true );
      if ($path && $zip->addFile( $path, $fname)) {
        // otherwise fall through and try downloading via URI
        return ['id', $original ];
      };
    }

    if ( isset( $value['uri'] ) ) {
      require_once(ABSPATH . 'wp-admin/includes/media.php');
      require_once(ABSPATH . 'wp-admin/includes/file.php');
      require_once(ABSPATH . 'wp-admin/includes/image.php');

      $tmpFile = \download_url( $value['uri'] );
      if (!is_wp_error( $tmpFile)) {
        if ( $zip->addFile( $tmpFile, $fname) ) {
          return ['uri', $original ];
        };
      }
    }

    return ['no-import', $value['uri']];
  }

  public function debug() {

    foreach($this->tasks as $item) {
      list($type, $content) = $item;

      if ($type !== 'options') {
        var_dump($item);
      }
    }
  }
}
