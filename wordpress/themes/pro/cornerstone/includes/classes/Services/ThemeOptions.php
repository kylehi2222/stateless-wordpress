<?php

namespace Themeco\Cornerstone\Services;
use Themeco\Cornerstone\Elements\BreakpointData;
use Themeco\Cornerstone\Services\Routes;
use Themeco\Cornerstone\Parsy\P;

class ThemeOptions implements Service {

  protected $defaults = [];
  protected $groups = [];
  protected $designations = [];
  protected $updates = [];
  protected $preload = [];
  protected $data = [];
  protected $registered = false;
  protected $themeManagement;
  protected $config;

  /**
   * Custom stacks set by Pro and blank
   */
  private $customStacks = [];

  private $routes;
  private $permissions;


  public function __construct(
    ThemeManagement $themeManagement,
    Config $config,
    Routes $routes,
    Permissions $permissions
  ) {
    $this->themeManagement = $themeManagement;
    $this->config = $config;
    $this->routes = $routes;
    $this->permissions = $permissions;
  }

  public function setup() {
    add_action(
      'after_setup_theme',
      [ $this, 'init' ],
      apply_filters('cs_after_theme_options_setup_priority', 0)
    );

    $this->routes->add_route('get', 'theme-options-export', [$this, 'export']);
    $this->routes->add_route('post', 'theme-options-import', [$this, 'import']);
  }

  public function init() {
    do_action("cs_theme_options_before_init");

    $this->register();

    if ($this->isCustomStack()) {
      $this->customStackInit();
    }
  }

  public function register() {

    if ( current_theme_supports( 'cornerstone-managed' ) ) {
      // @TODO handled through stack api
      //$this->register_options( $this->config->group( 'theme-option-defaults' ), [
        //'option'     => 'cs_option_data',
        //'responsive' => true
      //]);
    } else {
      $this->register_options( $this->config->group( 'theme-option-standalone-defaults' ), [
        'option'     => 'cs_option_data',
        'responsive' => true
      ]);
    }


    $this->register_option($this->get_global_css_key(), '');
    $this->register_option($this->get_global_js_key(), '');

    $this->register_option('cs_global_parameter_json', '');
    $this->register_option('cs_global_parameter_data', '');
  }

  /**
   * Custom stack init
   */
  private function customStackInit($stack = null) {
    $stack = empty($stack)
      ? $this->getStack()
      : $stack;

    // Extends init run first
    if (!empty($stack['extends'])) {
      $this->customStackInit(
        $this->getStack($stack['extends'])
      );
    }

    // Init func
    if (!empty($stack['init']) && is_callable($stack['init'])) {
      $stack['init']();
    }

    // Stylesheets init
    if (!empty($stack['stylesheets'])) {
      $this->initStyleSheets($stack['stylesheets']);
    }
  }

  /**
   * Add action and loop stylesheets
   */
  private function initStyleSheets($stylesheets = []) {
    add_action("wp_enqueue_scripts", function() use ($stylesheets) {
      foreach ($stylesheets as $id => $path) {
        if (empty($path)) {
          continue;
        }

        wp_enqueue_style($id, $path);
      }
    });
  }

  public function get_global_js_key() {
    return apply_filters( 'cs_global_js_option', 'cs_v1_custom_js' );
  }

  public function get_global_css_key() {
    return apply_filters( 'cs_global_css_option', 'cs_v1_custom_css' );
  }

  public function get_global_js() {
    return $this->get_value( $this->get_global_js_key() );
  }

  public function get_global_css() {
    return $this->get_value( $this->get_global_css_key() );
  }

  /**
   * Get stack object
   */
  public function getStack($stackName = null) {
    if (empty($stackName)) {
      // @TODO maybe a different way
      $stackName = get_option('x_stack');
    }

    $stacks = cornerstone('ThemeOptions')->getCustomStacks();

    return empty($stacks[$stackName])
      ? null
      : $stacks[$stackName];
  }

  /**
   * Not X stack
   */
  public function isCustomStack($stackName = null) {
    $stack = $this->getStack($stackName);

    if (empty($stack)) {
      //trigger_error("Stack is not found " . $stack);
      return false;
    }

    return empty($stack['is_x']);
  }

  public function get_config() {

    $global_css_key = $this->get_global_css_key();
    $global_js_key = $this->get_global_js_key();

    return array(
      'globalCssKey' => $global_css_key,
      'globalJsKey'  => $global_js_key,
      'previewExclusions' => array_merge(
        [ $global_css_key, $global_js_key ],
        apply_filters('cs_theme_option_preview_exclusions',[])
      )
    );
  }

  public function get_controls() {

    $data = [];

    if ( ! $this->themeManagement->isStandalone() ) {
      // Handled by framework
      //$data = $this->config->group( 'theme-option-controls' );
    } else {
      // Standalone controls
      $data = $this->config->group('theme-option-standalone-controls');

      // Theme option modules like External API
      if (!empty($data[0]['controls'])) {
        $data[0]['controls'] = apply_filters("cs_theme_options_modules", $data[0]['controls']);
      }
    }

    return apply_filters( 'cs_theme_options_controls', $data );

  }

  public function register_option( $name, $default_value, $designation = 'markup', $group = null ) {
    $this->defaults[ $name ] = $default_value;
    $this->designations[ $name ] = $designation;
    if ($group) {
      $this->groups[ $name ] = $group;
    }
  }

  public function register_options( $options, $args = [] ) {

    $group = !empty($args['option']) ? $args['option'] :null;
    $responsive = isset( $args['responsive'] ) && $args['responsive'];
    foreach ( $options as $name => $item ) {

      if ( $responsive) {
        list($value, $designation) = $item;
      } else {
        $value = $item;
        $designation = 'markup';
      }
      $this->register_option( $name, $value, $designation, $group );
    }
  }

  public function get_default( $name ) {
    if (empty($name)) {
      trigger_error("Invalid default name passed");
      return null;
    }

    // No default grabbed is probably a bug
    if (!isset($this->defaults[$name])) {
      if (WP_DEBUG) {
        trigger_error("No default theme option for " . $name);
      }
      return null;
    }

    return $this->defaults[ $name ];
  }

  public function getValues() {

    $data = [];
    $defaultKeys = array_keys( $this->defaults );
    $defaults = [];

    foreach ($defaultKeys as $key) {
      $data[$key] = $this->get_value( $key );
      $defaults[$key] = [$this->defaults[$key], $this->designations[$key]];
    }

    $data = array_merge( $data, $this->preload );

    // Standalone addons
    if ( ! current_theme_supports( 'cornerstone-managed' ) ) {

      $data = array_merge( $this->data['cs_option_data'], $data); // merge in any saved values that were not registered as options (e.g. _bp_data)
      list($base, $ranges, $size) = cornerstone('Breakpoints')->breakpointConfig();

      if ( ! isset( $data['_bp_base'] ) ) {
        $data['_bp_base'] = $base . '_' . $size;
      }

      if ($base . '_' . $size !== $data['_bp_base'] ) { // current element does not match current base breakpoint
        $breakpointData = cornerstone()->resolve(BreakpointData::class);
        $breakpointData->setElement($data, $defaults);
        $data = $breakpointData->convertTo($base, $size);
      }
    }

    return [$data, $this->defaults,$this->designations];

  }

  public function get_value( $name ) {

    if ( isset( $this->groups[ $name ] ) ) {
      $group = $this->groups[ $name ];

      if ( ! isset( $this->data[$group] ) ) {
        $this->data[$group] = get_option( $group, []);
      }

      if ( ! isset( $this->data[$group][$name] ) ) {
        $this->data[$group][$name] = isset( $this->defaults[ $name ] ) ? $this->defaults[ $name ] : null;
      }
      return $this->data[$group][$name];
    }
    return get_option( $name, $this->get_default( $name ) );
  }

  /**
   * Get theme option registered options
   */
  public function getKeys() {
    return array_keys($this->defaults);
  }

  public function update_value( $name, $value ) {

    if ( is_bool($value) ) {
      $value = sanitize_key($value); // Convert bool to "1" and ""
    }

    if ( 0 === strpos( $name, '_bp_') )  {
      $this->groups[$name] = 'cs_option_data';
    }
    if ( isset( $this->groups[$name] ) ) {
      $group = $this->groups[$name];
      if ( ! isset( $this->updates[$group] ) ) {
        $this->updates[$group] = [];
      }
      $this->updates[$group][$name] = $value;
    } else {
      update_option( $name, $value );
    }

  }

  public function commit() {
    // save groups
    foreach ( $this->updates as $group => $value ) {
      unset($this->data[$group]);
      $existing = get_option($group, []);
      $existing = !empty($existing) && is_array( $existing ) ? $existing : [];
      update_option( $group, array_merge( $existing, $value ));
    }

    // Run init on save request
    if ($this->isCustomStack()) {
      $this->customStackInit();
    }
  }

  public function previewPreFilter( $data ) {
    $this->preload = $data;


    $handler = function( $value ) {
      $option_name = preg_replace( '/^pre_option_/', '', current_filter() );

      if ( isset( $this->preload[ $option_name ] ) ) {
        $value = apply_filters( 'option_' . $option_name, $this->preload[ $option_name ] );
      }

      return $value;
    };


    $exclude = apply_filters( 'cs_theme_option_preview_exclusions', [] );
    foreach ($this->preload as $key => $value) {
      if ( ! empty( $this->groups[ $key ] ) ) {
        continue;
      }
      if ( in_array( $key, $exclude, true ) ) continue;
      add_filter( "pre_option_$key", $handler );
    }
  }

  /**
   * Get all stacks
   * X and Pro ones are added
   */
  public function getAllStacks() {
    $stacks = apply_filters("cs_filter_stacks", $this->getCustomStacks());

    return $stacks;
  }

  /**
   * Get custom stacks
   */
  public function getCustomStacks() {
    return $this->customStacks;
  }

  /**
   * Formats controls from callable
   * to array
   */
  public function getAllStacksWithControls() {
    $stacks = $this->getAllStacks();

    foreach ($stacks as $id => &$stack) {
      // One function
      if (is_callable($stack['controls'])) {
        $stack['controls'] = $stack['controls']();

        // Apply modules like External API
        $stack['controls'] = apply_filters("cs_theme_options_modules", $stack['controls']);
        continue;
      }


      if (!is_array($stack['controls'])) {
        trigger_error("Stack controls not array or callable : {$id}");
        continue;
      }

      $controls = [];
      foreach ($stack['controls'] as $control) {
        $formatted = is_callable($control)
          ? $control()
          : $control;

        $controls = array_merge($controls, $formatted);
      }

      // Apply modules like External API
      $stack['controls'] = apply_filters("cs_theme_options_modules", $controls);
    }

    return $stacks;
  }

  /**
   * Set custom stacks
   */
  public function addCustomStack($stack = []) {
    if (empty($stack['id'])) {
      throw new \RuntimeException("No stack ID passed");
    }

    // Normalize
    if (empty($stack['controls'])) {
      $stack['controls'] = [];
    }

    if (is_callable($stack['controls'])) {
      $stack['controls'] = [$stack['controls']];
    }

    if (empty($stack['css'])) {
      $stack['css'] = [];
    }

    if (!is_array($stack['css'])) {
      $stack['css'] = [$stack['css']];
    }

    if (empty($stack['stylesheets'])) {
      $stack['stylesheets'] = [];
    }

    // Values in DB
    if (!empty($stack['values'])) {
      $this->register_options($stack['values']);
    }

    if (
      !empty($stack['extends'])
      && empty($this->customStacks[$stack['extends']])
    ) {
      trigger_error("Extending from stack that does not exist " . $stack['extends']);
    }

    // Stack extension
    // Stylesheets handled by init
    if (
      !empty($stack['extends'])
      && !empty($this->customStacks[$stack['extends']])
    ) {
      $extendedFrom = $this->customStacks[$stack['extends']];
      $stack['controls'] = array_merge($stack['controls'], $extendedFrom['controls']);
      $stack['css'] = array_merge($extendedFrom['css'], $stack['css']);
    }

    $this->customStacks[$stack['id']] = $stack;
  }

  /**
   * Custom css
   */
  public function getCustomStackCSS() {
    if (!$this->isCustomStack()) {
      return;
    }

    $stack = $this->getStack();

    // No stack CSS
    if (empty($stack['css'])) {
      return;
    }

    // From string
    if (!is_array($stack['css'])) {
      return $this->stackCSSProcess($stack['css']);
    }

    // Stack array of css files or
    $css = "";
    foreach ($stack['css'] as $arrCSS) {
      $css .= $this->stackCSSProcess($arrCSS);
    }

    return $css;
  }

  /**
   * Loads file or string
   */
  private function stackCSSProcess($fileOrCSS) {
    if (@file_exists($fileOrCSS)) {
      return file_get_contents($fileOrCSS);
    }

    return $fileOrCSS;
  }

  /**
   * Export Theme Options to JSON
   * originally  X_Validation_Theme_Options_Manager::export
   */
  public function export() {
    if (
      ! current_user_can( 'manage_options' )
      || !$this->permissions->userCan("global.theme_options")
      || !$this->permissions->userCan("global.theme_options_export")
    ) {
      throw new \RuntimeException("Invalid permissions");
    }

    $data = [];

    foreach ( $this->defaults as $option => $default ) {
      $data[$option] = maybe_unserialize( get_option( $option, $default ) );
    }

    return $data;

  }

  /**
   * Import Theme Options from JSON
   * originally  X_Validation_Theme_Options_Manager::import
   */
  public function import( $options = [] ) {

    if (
      ! current_user_can( 'manage_options' )
      || !$this->permissions->userCan("global.theme_options")
      || !$this->permissions->userCan("global.theme_options_import")
    ) {
      throw new \RuntimeException("Invalid permissions");
    }

    $default_options = array_keys( $this->defaults );

    foreach ( $default_options as $key  ) {

      if ( isset( $options[$key] ) ) {
        update_option( $key, $options[$key] );
      }

    }

    do_action("cs_purge_tmp");

    return [ 'success' => true ];

  }
}
