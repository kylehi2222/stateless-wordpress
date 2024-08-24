<?php

namespace Themeco\Cornerstone\Services;

use Themeco\Cornerstone\Plugin;
class GlobalFonts implements Service {

  public $queue = array();
  public $custom_css_output = '';
  protected $font_items;
  protected $font_config;
  protected $loaded = array();
  protected $loaded_custom = array();
  protected $data = null;
  protected $missed_wp_head = false;
  protected $typekit_loaded = false;

  protected $plugin;


  public function __construct(Plugin $plugin) {
    $this->plugin = $plugin;
  }

  public function setup() {

    add_filter('cs_css_post_process_font-family', array( $this, 'cssPostProcessFontFamily') );
    add_filter('cs_css_post_process_font-weight', array( $this, 'cssPostProcessFontWeight') );
    add_filter('cs_css_post_process_tss-ff', array( $this, 'cssPostProcessFontFamily') );
    add_filter('cs_css_post_process_tss-fw', array( $this, 'cssPostProcessFontWeight') );
    add_filter( 'cs_font_data', [$this, 'font_data' ] );
    add_filter( 'cs_app_font_data', [$this, 'font_data_with_load_items' ] );

    add_filter( 'wp_check_filetype_and_ext', array( $this, 'upload_check'), 10, 5 );
    add_filter( 'upload_mimes', array( $this, 'upload_mimes' ), 999 );

    add_action( 'wp_head', array( $this, 'typekit_loading_styles' ) );
    add_action( 'wp_head', array( $this, 'load_initial_items'), 0 );
    add_action( 'wp_head', array( $this, 'load_queued_fonts'), 10000 );
    add_action( 'wp_head', array( $this, 'missed_wp_head'), 10001 );
    add_action( 'wp_footer', array( $this, 'load_queued_fonts' ) );

    add_filter( 'cs_migrate_font_weight', [ $this, 'migrateLegacyFontWeight' ] );
    add_filter( 'cs_migrate_font_family', [ $this, 'migrateLegacyFontFamily' ] );

    add_action('cornerstone_app_enqueue_scripts', [ $this, 'typekitKitLoadAsCSS' ]);
  }

  public function font_data($data = []) {
    if ( is_null( $this->data ) ) {
      $system_fonts = include $this->plugin->path('/includes/config/fonts-system.php');

      $google_fonts = [];
      if (cs_google_fonts_enabled()) {
        $google_fonts = include $this->plugin->path('/includes/config/fonts-google.php');
      }

      $this->data = array_merge( $system_fonts, $google_fonts );
    }
    return array_merge( $this->data, $data );
  }

  /**
   * Same as font_data, but adds in load URL for use in App
   *
   * @param array $data
   *
   * @return array
   */
  public function font_data_with_load_items($data = []) {
    $items = $this->font_data($data);
    return $this->get_font_items_with_load_files($items);
  }

  public function load_initial_items() {
    $items = $this->get_font_items();
    $preload = did_action('cs_before_preview_frame');

    foreach ($items as $item) {
      if ($preload || isset($item['force']) && $item['force']) {
        $font = $this->getDataForFontItem( $item );
        if ( $font ) {
          $this->queue_font( $font );
          $this->queue_font_weight( $font, $font['weightNormal'] );
          $this->queue_font_weight( $font, $font['weightBold'] );
        }
      }
    }

    if ( $preload ) {
      $this->load_fonts_typekit();
      $this->load_fonts_custom( $this->get_font_config()['customFontItems']);
    }

  }

  public function get_fallback_font() {
    return apply_filters("cs_font_fallback", [
      'name'    => 'helvetica',
      'source'  => 'system',
      'family'  => 'Helvetica',
      'stack'   => 'Helvetica, Arial, ' . $this->get_system_fallback(),
      'weights' => ['100', '200', '300', '400', '500', '600', '700', '800', '900'],
      'weightNormal' => '400',
      'weightBold'   => '700'
    ]);
  }

  // Global system name fallback
  public function get_system_fallback() {
    return apply_filters("cs_font_system_fallback", 'sans-serif');
  }

  public function get_font_items() {
    if ( ! $this->font_items ) {
      $this->font_items = $this->load_items();
    }
    return $this->font_items;
  }

  /**
   * Fonts with load file if valid
   *
   * @param array $items
   *
   * @return array
   */
  public function get_font_items_with_load_files($items) {
    foreach ($items as &$item) {
      // Simpler load item for lighter load file
      $loadItem = $item;

      // Setup weight normal and weights for loadItem
      if (!empty($item['weights'])) {
        $item['weightNormal'] = $this->getClosestWeight(null, 400, $item['weights']);
        $loadItem['weights'] = [ $item['weightNormal'] ];
      }

      // Get load url
      $item['load_url'] = $this->getLoadFile($loadItem);
    }

    return $items;
  }

  public function getLoadFile($font) {
    switch ($font['source']) {
    case 'google':
      return $this->getGoogleFontLoadURI([ $font ]);
    }

    return '';
  }

  public function get_font_config() {
    if ( ! $this->font_config ) {
      $this->font_config = $this->load_config();
    }
    return $this->font_config;
  }

  protected function preload_config() {
    $preloaded = apply_filters('cs_preload_font_config', false );
    if ($preloaded) {
      return $preloaded;
    }


    $stored = get_option( 'cornerstone_font_config' );
    if ($stored === false ) {
      $stored = wp_slash( cs_json_encode( new \stdClass ) );
      update_option( 'cornerstone_font_config', $stored );
    }


    $config = ( is_null( $stored ) ) ? array() : json_decode( wp_unslash( $stored ), true );

    // Add Ids to custom font items created without them
    $persist = false;
    if ( isset( $config['customFontItems'] ) ) {
      foreach ( $config['customFontItems'] as $key => $value) {
        if ( ! isset( $value['_id'] ) ) {
          $config['customFontItems'][$key]['_id'] = uniqid();
          $persist = true;
        }
      }
    }

    if ( $persist ) {
      update_option( 'cornerstone_font_config', wp_slash( cs_json_encode( $config ) ) );
    }

    return $config;
  }

  protected function load_config() {
    return wp_parse_args( $this->preload_config(), array(
      'googleSubsets' => [],
      'googleDisabled' => false,
      'googleFontsURL' => '',
      'typekitKitID' => '',
      'typekitItems' => [],
      'customFontItems' => [],
      'customFontFaceCSS' => '',
      'fontDisplay' => 'auto'
    ) );

  }

  protected function normalizeFontItem( $item ) {

    if (isset( $item['children'])) {
      return $item;
    }

    // Transfer previously set "weights" into the weightNormal and weightBold keys
    if ( isset( $item['weights'] ) ) {
      $item['weightNormal'] = $this->getClosestWeight(null, 400, $item['weights']);
      $item['weightBold'] = $this->getClosestWeight(null, 700, $item['weights']);
      $item['weightSelection'] = $item['weights'];

      unset($item['weights']);
    }

    $data = [];

    // Migrate the item to include a name instead of using the family for storing direct values
    if ( ! isset( $item['name'] ) || ! isset( $item['weightNormal'] ) || ! isset( $item['weightBold'] ) ) {

      $data = $this->font_data();

      if ( ! isset( $item['name'] ) ) {
        $item['name'] = $this->updateFontItemName( $item['source'], $item['family'] );
      }

      $definedWeights = $this->getDefinedFontWeights( $item );

      if ( ! isset( $item['weightNormal'] ) ) {
        if ( $item['name'] === 'unknown') {
          $item['weightNormal'] = '400';
        } else {
          $item['weightNormal'] = $this->getClosestWeight(null, 400, $definedWeights);
        }
      }

      if ( ! isset( $item['weightBold'] ) ) {
        if ( $item['name'] === 'unknown') {
          $item['weightBold'] = '700';
        } else {
          $item['weightBold'] = $this->getClosestWeight(null, 700, $definedWeights);
        }
      }

    }

    if ( ! isset( $item['name'] ) ) {
      $name = $this->updateFontItemName( $item['source'], $item['family'] );
      if ($name) {
        $item['name'] = $name;
      }
    }

    return $item;
  }

  public function getWeightsForCustomItem( $item ) {
    return array_filter(array_map(function($file) {
      return isset( $file['weight'] ) ? $file['weight'] : null;
    }, isset($item['files']) ? $item['files'] : []));
  }

  public function getDefinedFontWeights( $item ) {

    if ( $item['source'] === 'custom') {
      $custom = $this->locateCustomItem( $item['name'] );
      return $custom ? $this->getWeightsForCustomItem( $custom ) : [];
    }

    $data = $this->font_data();

    if ( isset( $data[$item['name']]) ) { // system, google
      return $data[$item['name']]['weights'];
    }

    return [];
  }

  protected function load_items() {
    $preloaded = apply_filters('cs_preload_font_items', false );
    if ($preloaded) {
      return $preloaded;
    }
    $stored = get_option( 'cornerstone_font_items' );
    if ($stored === false ) {
      $stored = wp_slash( cs_json_encode( [] ) );
      update_option( 'cornerstone_font_items', $stored );
    }
    $items = ( is_null( $stored ) ) ? array() : json_decode( wp_unslash( $stored ), true );

    foreach ($items as $key => $value) {
      $items[$key] = $this->normalizeFontItem( $value );
    }

    return $items;
  }


  public function updateFontItemName( $source, $family) {

    if ( $source === 'typekit') {
      return $family;
    }

    if ( $source === 'custom') {
      $config = $this->get_font_config();
      foreach ($config['customFontItems'] as $key => $value) {
        if ($value['family'] === $family) {
          return $value['_id'];
        }
      }

    } else {

      $data = $this->font_data();
      foreach ($data as $key => $value) {
        if ($value['source'] === $source && $value['family'] === $family) {
          return $key;
        }
      }

    }

    return 'unknown';
  }

  public function getClosestWeight( $current, $fallback, $options) {
    $value = intval( empty( $current ) ? $fallback : $current);
    $value = $value ? $value : $fallback;

    // Will crash wihout
    // for invalid fonts
    if (!is_array($options)) {
      $options = [];
    }

    $numeric = array_filter(array_map(function($item) {
      return intval($item);
    }, $options));

    usort($numeric,function($a,$b) use ($value){
      return abs($a - $value) - abs($b - $value);
    });

    $val = !empty($numeric[0])
      ? $numeric[0]
      : $fallback;

    return strval($val);
  }

  protected function locate_font( $input ) {
    $this->get_font_items();

    foreach ($this->font_items as $font) {
      if ( isset( $font['_id'] ) && $input === $font['_id'] ) {
        return $this->getDataForFontItem( $font );
      }
    }

    $parts = explode(':',$input);

    if (count($parts) !== 2) {
      return null;
    }

    list($source, $name) = $parts;

    return $this->getDataForFontItem( [ 'source' => $source, 'name' => $name ]);
  }


  public function locateCustomItem( $id ) {
    $config = $this->get_font_config();
    foreach ($config['customFontItems'] as $key => $value) {
      if ($id === $value['_id']) {
        return $value;
      }
    }
    return null;

  }

  public function resolveFontDefinition( $input ) {

    if ( $input['source'] === 'system' || $input['source'] === 'google' ) {
      $data = $this->font_data();

      if ( isset( $data[$input['name']] ) ) {
        return $data[$input['name']];
      }
    }

    if ( $input['source'] === 'custom' ) {
      $custom = $this->locateCustomItem( $input['name'] );
      if ( $custom ) {
        // Use set fallback or use system_fallback
        $fallback = empty($custom['fallback'])
          ? $this->get_system_fallback()
          : $custom['fallback'];

        // Use stack or famil and add fallback
        $stack = !empty($custom['stack']) ? $custom['stack'] : $custom['family'];
        $stack .= ', ' . $fallback;

        return [
          'source' => 'custom',
          'stack'   => $stack,
          'family'   => $custom['family'],
          'weights' => $this->getWeightsForCustomItem( $custom )
        ];
      }
    }

    $config = $this->get_font_config();


    if ( $input['source'] === 'typekit' ) {
      $config = $this->get_font_config();
      foreach ($config['typekitItems'] as $key => $value) {
        if ($input['name'] === $value['family']) {
          return array_merge( $value, [
            'source' => 'typekit'
          ]);
        }
      }
    }

    // Not found
    return $this->get_fallback_font();
  }

  public function getDataForFontItem($input) {

    if ( ! isset( $input['source'] ) || ! isset( $input['name'] ) ) {
      return null;
    }

    $definition = $this->resolveFontDefinition( $input );

    // Overwrite stack if fallback passed
    if (!empty($input['fallback'])) {
      // System fonts should not be wrapped in quotes
      $wrappedFamily = $definition['source'] === 'system'
        ? $definition['family']
        : '"' . $definition['family'] . '"';

      // The real adobe typekit family is in stack
      if ($definition['source'] === 'typekit') {
        $adobeSplit = explode(',', $definition['stack']);
        $wrappedFamily = $adobeSplit[0];
      }

      // Create stack with custom fallback
      $definition['stack'] = $wrappedFamily . ', ' . $input['fallback'];
    }

    if ( ! $definition ) {
      return null;
    }

    return array_merge( $definition, [
      'handle'    => $input['source'] . ':' . $input['name'],
      'weightNormal' => $this->getClosestWeight( isset($input['weightNormal']) ? $input['weightNormal'] : "400", 400, $definition['weights']),
      'weightBold' => $this->getClosestWeight( isset($input['weightBold']) ? $input['weightBold'] : "700", 700, $definition['weights'])
    ]);
  }


  public function queue_font( $font ) {
    if ( 'system' === $font['source']) {
      return;
    }

    $handle = isset( $font['handle'] ) ? $font['handle'] : $font['family'];

    // Not isset so add with default weights
    if ( ! isset( $this->queue[$handle] ) ) {
      $weights = did_action( 'cs_element_rendering' ) || apply_filters( 'cs_google_font_load_all_weights', false )
        ? $font['weights']
        : $this->getFontWeightsArray($font);

      if (empty($weights)) {
        $weights = [];
      }

      $this->queue[$handle] = array_merge( $font, [
        'weights' => $weights,
      ]);
    }

  }


  protected function queue_font_weight( $font, $weight ) {

    if (isset($this->queue[$font['handle']]) ) {
      if ( ! in_array($weight, $this->queue[$font['handle']]['weights'], true ) ) {
        $this->queue[$font['handle']]['weights'][] = $weight;
        $this->queue[$font['handle']]['weights'][] = $weight . 'i';
      }
    }
  }

  public function migrateLegacyFontWeight( $input ) {

    if ( is_string( $input ) ) {
      if ( strpos($input, '{{dc') !== false ) {
        return $input;
      }
      if ( strpos($input, ':') !== false  ) { // Migrate font values from before Pro 5.2
        $parts = explode( ':', $input );
        return array_pop($parts);
      }
      if ( strpos($input, 'fw_fallback') === 0) { // Migrate font values from before Pro 2.0
        return 'inherit';
      }
    }
    return $input;
  }

  public function migrateLegacyFontFamily( $input ) {
    if ( is_string( $input ) && strpos($input, 'fw_fallback') === 0 ) { // Migrate font values from before Pro 2.0
      return 'inherit';
		}
		return $input;
  }


  /**
   * Process and queue to load in weights
   */
  public function cssPostProcessFontFamily( $value ) {
    $font = $this->locate_font($value);
    if ( $font ) {
      $this->queue_font( $font );
      return $font['stack'];
    }

    return !empty($value)
      ? $value
      : 'inherit';
  }


  public function cssPostProcessFontWeight( $value ) {

    $parts = explode('|', $value );

    if ( count( $parts ) !== 2) {
      return 'inherit';
    }

    list($family, $weight) = $parts;

    if ( 'inherit' === $family ) {
      if ($weight === 'fw-normal') {
        return 'normal';
      }
      if ($weight === 'fw-bold') {
        return 'bold';
      }
      return $weight;
    }

    $font = $this->locate_font($family);

    if ( $font ) {

      $this->queue_font( $font );

      if ($weight === 'inherit') {
        return 'inherit';
      }

      if ($weight === 'fw-normal') {
        $this->queue_font_weight( $font, $font['weightNormal']);
        return $font['weightNormal'];
      }

      if ($weight === 'fw-bold') {
        if ( $font['weightBold'] === $font['weightNormal'] ) {
          return 'bolder';
        }
        $this->queue_font_weight( $font, $font['weightBold']);
        return $font['weightBold'];
      }

      // If we have a numeric value, try to use the closest available weight
      if ( strval( intval( $weight ) ) === $weight ) {
        $closest = $this->getClosestWeight( $weight, $weight, $font['weights']);
        $this->queue_font_weight( $font, $closest);
        return $closest;
      }

    }

    return $weight;
  }

  public function load_queued_fonts() {


    if  (count( array_keys( $this->queue ) ) <=0 ) {
      return;
    }

    $sources = array();

    foreach ($this->queue as $item) {
      if ( ! isset( $item['source'] ) ) {
        continue;
      }
      $source = $item['source'];
      if ( ! isset( $sources[$source ] ) ) {
        $sources[$source] = array();
      }
      $sources[$source][] = $item;
    }

    ksort($sources);

    do_action( 'cs_load_queued_fonts', $this->queue, $sources );

    foreach ($sources as $source => $fonts) {
      if ($source === 'google') {
        $this->load_fonts_google( $fonts );
      } else if ($source === 'custom') {
        $this->load_fonts_custom( $fonts );
      } else if ($source === 'typekit') {
        $this->load_fonts_typekit( $fonts );
      }
    }

    $this->queue = array();

  }

  public function load_fonts_google( $fonts ) {

    if ( !cs_google_fonts_enabled() ) {
      return;
    }

    $request = $this->getGoogleFontLoadURI($fonts);

    if (empty($request)) {
      return;
    }

    $in_footer = 'wp_footer' === current_action();


    $output = cs_tag( 'link', [
      'rel'   => 'stylesheet',
      'href'  => apply_filters( 'cs_google_fonts_href', $request ),
      'type'  => 'text/css',
      'media' => 'all',
      'crossorigin' => 'anonymous',
      'data-x-google-fonts' => null,
    ], '');

    if ( $in_footer ) { // append the link tag to the head later
      cornerstone('EnqueueScripts')->outputScript( "(function(){
        document.addEventListener('DOMContentLoaded', () => {
          window.csGlobal.rivet.util.appendHtmlString(document.head, '$output')
        })
      })();");
    } else {
      echo $output;
    }

  }

  /**
   * Get request needed for google fonts load
   */
  public function getGoogleFontLoadURI($fonts) {
    $config = apply_filters( 'cs_google_font_config', wp_parse_args($this->get_font_config(), array(
      'googleSubsets' => array(),
      'fontDisplay' => 'auto'
    ) ) );

    $subsets = array_merge( array('latin', 'latin-ext'), $config['googleSubsets'] );
    $subsets = array_unique($subsets);

    $family_strings = array();

    // Loop fonts and add to URL params
    foreach ($fonts as $font) {
      // Use all weights or set weights
      $weights = array_unique( $font['weights'] );

      $to_load = str_replace(' ', '+', $font['family'] ) . ':' . implode(',', $weights );
      if ( ! isset( $this->loaded[$to_load] ) ) {
        $family_strings[] = $to_load;
        $this->loaded[$to_load] = true;
      }
    }

    if ( count($family_strings) <=0 ) {
      return;
    }

    $request = esc_url( add_query_arg( array(
      'family' => implode('%7C', $family_strings), //Was | (pipe), but %7C is required for W3C Markup validation, this is also more optimized than using urlencoder
      'subset' => implode(',', $subsets ),
      'display' => $config['fontDisplay']
    ), cs_google_fonts_uri() ) );

    return $request;
  }

  public function missed_wp_head() {
    $this->missed_wp_head = true;
  }

  public function load_fonts_typekit( $fonts = []) {
    if ( ! $this->typekit_loaded ) {
      $this->typekit_loaded = true;
      if ( $this->missed_wp_head ) {
        add_action( 'wp_footer', [ $this, 'output_typekit_script' ] );
      } else {
        add_action( 'wp_head', [ $this, 'output_typekit_script' ], 10002 );
      }
    }
  }

  public function load_fonts_custom( $fonts ) {
    $config = $this->getCustomFontConfig();

    $load = array();
    $buffer = '';

    foreach ($fonts as $font) {
      if ( ! in_array( $font['family'], $this->loaded_custom, true ) ) {
        $load[] = $font['family'];
      }
    }

    foreach ($config['customFontItems'] as $item) {
      if (in_array($item['family'], $load)) {
        $buffer .= $this->make_custom_font_css( $item, $config );
        $this->loaded_custom[] = $item['family'];
      }
    }

    if ( $buffer ) {
      cornerstone('Styling')->addStyles( 'cs-custom-fonts', $buffer, 3 );
    }

  }

  public function getCustomFontConfig() {
    $config = apply_filters( 'cs_custom_font_config', wp_parse_args($this->get_font_config(), [
      'customFontItems' => array(),
      'fontDisplay'     => 'auto'
    ] ) );

    return $config;
  }

  public function identify_custom_font_variants( $item ) {

    $variants = [];
    $variant_config = [];

    foreach ($item['files'] as $file) {
      $key = $file['weight'] . ':' . $file['style'];

      if ( ! isset( $variants[ $key ] ) ) {
        $variant_config[$key] = [ esc_attr($file['weight']), esc_attr($file['style']) ];
        $variants[ $key ] = [];
      }
      $file_parts = explode( '.', $file['filename']);
      $format = array_pop( $file_parts );
      if ($format) {
        $variants[ $key ][] = [esc_attr($file['url']), $this->normalize_format( $format)];
      }
    }

    return [$variants, $variant_config];

  }

  public function normalize_format( $format ) {
    switch ($format) {
      case 'ttf':
        return "format('truetype')";
      case 'otf':
        return "format('opentype')";
      case 'woff':
        return "format('woff')";
      case 'woff2':
        return "format('woff2')";
    }

    return "";
  }

  public function make_custom_font_css( $item, $config ) {

    list( $variants, $variant_config ) = $this->identify_custom_font_variants( $item );

    $family = isset($item['stack']) ? $item['stack'] : $item['family'];
    $display = esc_attr( $config['fontDisplay'] );

    $buffer = '';

    foreach ($variants as $key => $variant_files) {
      list($weight, $style) = $variant_config[$key];

      $sources = [];
      foreach ($variant_files as $file) {
        list($url, $format) = $file;
        $sources[] = "url('$url') $format";
      }
      $sources = implode(', ', $sources);

      // Once was being set as regular
      // @TODO never send as regular
      // @see FontManager.js
      if ($style === "regular") {
        $style = "normal";
      }

      $buffer .= "@font-face { font-family: $family; font-display: $display; src: $sources; font-weight: $weight; font-style: $style; }";
    }

    return $buffer;
  }

  public function get_typekit_js( $id ) {
    ob_start(); ?>

    (function(doc){
      var config = { kitId:'<?php echo $id;?>', async:true };

      var timer = setTimeout(function(){
        doc.documentElement.className = doc.documentElement.className.replace(/\bwf-loading\b/g,"") + " wf-inactive";
      }, 3000);

      var tk = doc.createElement("script");
      var loaded = false;
      var firstScript = doc.getElementsByTagName("script")[0];

      doc.documentElement.className += " wf-loading";

      tk.src = 'https://use.typekit.net/' + config.kitId + '.js';
      tk.async = true;
      tk.onload = tk.onreadystatechange = function(){
        if (loaded || this.readyState && this.readyState != "complete" && this.readyState != "loaded") return;
        loaded = true;
        clearTimeout(timer);
        try { Typekit.load(config) } catch(e){}
      };

      firstScript.parentNode.insertBefore(tk, firstScript);
    })(window.document);

    <?php return ob_get_clean();

  }

  /**
   * Load in typekit
   * https://helpx.adobe.com/fonts/using/embed-codes.html
   */
  public function output_typekit_script() {

    $config = $this->get_font_config();
    if ( ! empty( $config['typekitKitID'] ) ) {
      // Load typekit as style tag
      if (!empty($config['typekitKitLoadAsCSS'])) {
        $this->typekitKitLoadAsCSS($config);
        return;
      }

      // load as a script
      // this used to be the only way to do it
      wp_print_inline_script_tag( $this->get_typekit_js( $config['typekitKitID'] ), [ 'id' => 'cs-typekit-loader' ] );
    }

  }

  public function typekitKitLoadAsCSS($config) {
    if (empty($config)) {
      $config = $this->get_font_config();
    }

    // Not setup at all
    if (empty($config['typekitKitID'])) {
      return;
    }

    wp_register_style('cs-typekit-css', false);
    wp_enqueue_style('cs-typekit-css', false);
    wp_add_inline_style('cs-typekit-css', "@import url('https://use.typekit.net/{$config['typekitKitID']}.css');");
  }

  public function typekit_loading_styles() {

    $config = $this->get_font_config();

    // Prevent flickering since we are loading as CSS
    if (!empty($config['typekitKitLoadAsCSS'])) {
      return;
    }

    if ( ! empty( $config['typekitKitID'] ) ) {
      $css = '.wf-loading a, .wf-loading p, .wf-loading ul, .wf-loading ol, .wf-loading dl, .wf-loading h1, .wf-loading h2, .wf-loading h3, .wf-loading h4, .wf-loading h5, .wf-loading h6, .wf-loading em, .wf-loading pre, .wf-loading cite, .wf-loading span, .wf-loading table, .wf-loading strong, .wf-loading blockquote { visibility: hidden !important; }';
      cornerstone('Styling')->addStyles( 'typekit', $css, 0 );
    }

  }

  public function upload_check( $result, $file, $filename, $mimes, $real_mime ) {

    $mime_types = $this->mime_types();

    $parts = explode( '.', $filename);
    $ext = end($parts);

    if ( isset($mime_types[$ext]) && false !== strpos( $mime_types[$ext], $real_mime ) ) {
      $ext_mime_types = explode('|', $mime_types[$ext]);
      $result['ext'] = $ext;
      $result['type'] = array_shift( $ext_mime_types );
    }

    return $result;

  }

  public function upload_mimes( $mime_types ) {

    $new_types = $this->mime_types();

    foreach ($new_types as $ext => $type) {
      if (! isset($mime_types[$ext])) {
        $mime_types[$ext] = $type;
      }
    }

    return $mime_types;
  }

  public function mime_types() {

    return apply_filters( 'cs_font_manager_mime_types', array(
      'woff2' => 'font/woff2|application/octet-stream',
      'woff' => 'font/woff|application/font/woff|application/font-woff|application/octet-stream',
      'ttf' => 'font/sfnt|application/x-font-ttf'
    ) );

  }

  /**
   * Generate array of weights based on normal and bold
   * @see getDataForFontItem
   */
  private function getFontWeightsArray($font = []) {
    if (
      empty($font['weightNormal'])
      && empty($font['weightBold'])
      && !empty($font['weights'])
    ) {
      return $font['weights'];
    }

    $output = [];

    if (!empty($font['weightNormal'])) {
      $output[] = $font['weightNormal'];
      $output[] = $font['weightNormal'] . 'i';
    }

    // No need to add same one if already there
    if (
      !empty($font['weightBold'])
      && $font['weightBold'] !== $font['weightNormal']
    ) {
      $output[] = $font['weightBold'];
      $output[] = $font['weightBold'] . 'i';
    }

    return $output;
  }

  public function getAppData() {
    return array(
      'fontItems'           => $this->get_font_items(),
      'fontConfig'          => $this->get_font_config(),
      'customFontMimeTypes' => $this->mime_types(),
      'fallbackFont'        => $this->get_fallback_font(),
      'managed'             => apply_filters( 'cs_font_manager_items', [
        'items',
        'google',
        'adobe',
        'custom',
        'display'
      ] )
    );
  }

}
