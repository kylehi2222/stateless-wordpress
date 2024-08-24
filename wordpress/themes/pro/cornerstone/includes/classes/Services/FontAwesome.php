<?php

namespace Themeco\Cornerstone\Services;

use Themeco\Cornerstone\Util\View;
use Themeco\Cornerstone\Plugin;

class FontAwesome implements Service {

  protected $plugin;
  protected $data;

  protected static $shouldAddStyles = false;
  protected static $didAdd = false;
  protected $styling;
  protected $config;
  protected $view;

  // Default types for icon picker
  public static $FA_DEFAULT_TYPES = [
    'x_font_awesome_light_enable' => 'l-hand-pointer',
    'x_font_awesome_solid_enable' => 'hand-pointer',
    'x_font_awesome_regular_enable' => 'o-hand-pointer',
    'x_font_awesome_brands_enable' => 'wordpress-simple',
  ];

  public static $SHORTHAND_MAP = [
    's' => 'solid',
    'o' => 'regular',
    'l' => 'light',
    'b' => 'brands',
    'ss' => 'sharp-solid',
    'sr' => 'sharp-regular',
    'sl' => 'sharp-light',
  ];

  public static $OPTION_NAME = [
    'fa_solid_enable'   => 'solid',
    'fa_regular_enable' => 'regular',
    'fa_light_enable'   => 'light',
    'fa_brands_enable'  => 'brands',

    // Sharp
    'fa_sharp-light_enable' => 'sharp-light',
    'fa_sharp-regular_enable' => 'sharp-regular',
    'fa_sharp-solid_enable' => 'sharp-solid',

    // Duo
    'fa_sharp-duotone' => 'duotone',
  ];

  public function __construct(Plugin $plugin, Styling $styling, Config $config, View $view) {
    $this->plugin = $plugin;
    $this->styling = $styling;
    $this->config = $config;
    $this->view = $view;
  }

  public function setup() {
    add_action( 'template_redirect', array( $this, 'registerStyles' ) );

    // Always load webfonts in the preview
    add_action("cs_before_preview_frame", function() {
      $this->setShouldAddStyles(true);
    });

    add_action("cs_fa_add_webfont_styles", function() {
      static::setShouldAddStyles(true);
    });
  }

  // Add font-awesome webfonts
  public function registerStyles() {
    // Check if enqueed or
    // is the default
    if (
      !static::$didAdd
      && (
        static::$shouldAddStyles
        || static::getDefaultLoadType() === "webfont"
      )
    ) {
      $this->styling->addStyles( 'fa-config', $this->view->name('frontend/font-awesome')->render(false, static::config(), true), 5 );
      static::$didAdd = true;
    }
  }

  public function ensureData() {
    if (!isset($this->data)) {
      $aliases = [];

      // V5 shim
      if (get_option("x_font_awesome_shim_enable", true)) {
        $aliases = [
          'aliases' => $this->config->group("font-icons-shims")
        ];
      }

      $config = static::config();

      $builtIconData = $this->loadConfigIconMap($config);

      $this->data = apply_filters('cs_font_icon_data', array_merge( $builtIconData, $aliases, array(
        'groups' => array(
          'solid'   => __( 'Solid', 'cornerstone' ),
          'regular' => __( 'Regular', 'cornerstone' ),
          'light'   => __( 'Light', 'cornerstone' ),
          'sharp-light' => __( 'Sharp Light', 'cornerstone' ),
          'sharp-regular' => __( 'Sharp Regular', 'cornerstone' ),
          'sharp-solid' => __( 'Sharp Solid', 'cornerstone' ),
          'brands'  => __( 'Brands', 'cornerstone ')
        )
      )));
    }
  }

  // Loads config map of font icon by group
  private function loadConfigIconMap($config) {
    unset($config['fa_font_path']);

    // Loads in default icons group
    $builtConfig = [
      'icons' => $this->config->group( 'font-awesome/icons' ),
      'light' => [],
      'regular' => [],
    ];

    foreach ($config as $type => $enabled) {
      // Invalid
      if (empty($enabled) || empty(static::$OPTION_NAME[$type])) {
        continue;
      }

      $groupName = static::$OPTION_NAME[$type];

      $groupConfig = $this->config->group( 'font-awesome/' . $groupName );

      $builtConfig[$groupName] = $groupConfig;
    }

    return $builtConfig;
  }

  static public function config() {
    $config = apply_filters( 'cs_fa_config', array(
      'icon_type' => get_option("x_font_awesome_icon_type", apply_filters("cs_fa_default_type", 'webfont')),
      'load_type_for_elements' => get_option("x_font_awesome_load_types_for_elements"),
      'fa_font_path'      => apply_filters("cs_fa_font_path", CS_ROOT_URL . 'assets/fonts/'),
      'fa_solid_enable'   => true,
      'fa_regular_enable' => true,
      'fa_light_enable'   => true,
      'fa_brands_enable'  => true,

      // Sharp
      'fa_sharp-light_enable' => false,
      'fa_sharp-regular_enable' => false,
      'fa_sharp-solid_enable' => false,
    ) );

    return $config;
  }

  public function resolveFontAlias( $key ) {
    $this->ensureData();

    $prefix = preg_replace("/^([solb][srl]?-)?.*$/", "$1", $key);
    $iconName = preg_replace("/^$prefix/", "", $key);

    $alias = isset( $this->data['aliases'][$iconName]['icon'] )
      ? $prefix . $this->data['aliases'][$iconName]['icon']
      : $key;

    return $alias;
  }

  /**
   * Get Font Icon Unicode Value as a string
   * @return string
   */
  public function getFontIcon( $key ) {

    $config = static::config();
    $key = $this->resolveFontAlias( $key );

    $set = 's';

    // Detect type
    foreach (static::$SHORTHAND_MAP as $id => $type) {
      // Disabled
      if (empty($this->data[$type])) {
        continue;
      }

      // Check like o- or sr-
      $idCheck = $id . '-';
      $idLen = strlen($idCheck);

      // Found in type
      if (0 === strpos($key, $id . '-')) {
        $keyToCheck = substr($key, $idLen);

        if (in_array($keyToCheck, $this->data[$type])) {
          $key = $keyToCheck;
          $set = $id;
          break;
        }
      }
    }

    // Check Brands
    if (!empty($config['fa_brands_enable'])) {
      if (in_array($key, $this->data['brands'])) {
        $set = 'b';
      }
    }

    $icon = ( isset( $this->data['icons'][ $key] ) ) ? $this->data['icons'][$key] : 'f00d';

    return array( $set, $icon, $key );
  }

  /**
   * FA icon data as assoc array
   *
   * @return array
   */
  public function getFontIconObject($key) {
    list($set, $icon, $key) = $this->getFontIcon($key);
    $iconName = preg_replace("/^[solb][srl]?-/", "", $key);

    return [
      'shorthand' => $set,
      'type' => static::$SHORTHAND_MAP[$set],
      'character' => $icon,
      'icon' => $iconName,
    ];
  }

  public function getIconFromUnicode($unicode) {
    static $flippedIcons = null;

    if (empty($flippedIcons)) {
      $flippedIcons = array_flip($this->data['icons']);
    }

    return isset($flippedIcons[$unicode])
      ? $flippedIcons[$unicode]
      : '';
  }

  /**
   * Return font icon cache
   * @return array
   */
  public function getFontIcons() {
    $this->ensureData();
    return $this->data['icons'];
  }

  /**
   * Return font icon cache
   * @return array
   */
  public function getFontIds() {
    $this->ensureData();
    $ids = array_keys( $this->data['icons'] );

    foreach ($this->data['regular'] as $key) {
      $ids[] = "o-$key";
    }

    foreach ($this->data['light'] as $key) {
      $ids[] = "l-$key";
    }

    return $ids;

  }

  public function getFontIconsData() {
    $this->ensureData();
    return $this->data;
  }

  function attr( $key ) {
    list($name, $unicode) = $this->getFontIcon( $key );
    return [
      'attr' => 'data-x-icon-' . $name,
      'unicode' => $unicode,
      'entity'  => '&#x' . $unicode . ';'
    ];
  }

  /**
   * Default icon for an icon picker depending on what
   * font awesome icons are enabled
   */
  public static function getDefaultIcon($notEnabledDefault = 'l-hand-pointer') {
    foreach (static::$FA_DEFAULT_TYPES as $type => $default) {
      // Type is not enabled
      if (!get_option($type, false)) {
        continue;
      }

      return $default;
    }

    // FA not even enabled
    return $notEnabledDefault;
  }

  // Default load type from option
  public static function getDefaultLoadType() {
    $config = static::config();

    return $config['icon_type'];
  }

  /**
   * Had individual element load types enabled
   */
  public static function hasIndividualLoadTypes() {
    $config = static::config();

    return apply_filters(
      "cs_fa_has_individual_load_types",
      !empty($config['load_type_for_elements'])
    );
  }

  /**
   * Get SVG path from key
   */
  public function getSVGPath($key) {
    $font = cornerstone('FontAwesome')->getFontIconObject( $key );

    $path = CS_ROOT_URL . 'assets/svg/font_awesome/'
      . $font['type'] . '/' . $font['icon'] . '.svg';

    return apply_filters("cs_fa_svg_path", $path, $font);
  }

  // SVG Output
  // Gets the full tag output
  public function getSVGOutput($key) {
    do_action("cs_fa_svg_ensure_load", "svg");

    // Find font icon object
    $font = cornerstone('FontAwesome')->getFontIconObject( $key );
    $font = apply_filters("cs_fa_svg_icon_object", $font);

    $path = CS_ROOT_PATH . 'assets/svg/font_awesome/'
      . $font['type'] . '/' . $font['icon'] . '.svg';

    if (!file_exists($path)) {
      trigger_error("Could not find icon : " . $key . " @ " . $path);
      return "";
    }

    return apply_filters("cs_fa_svg_output", file_get_contents($path), $font);
  }

  public static function setShouldAddStyles($flag = true) {
    static::$shouldAddStyles = $flag;

    // After the action
    // considered to be a 'late' load
    // as in the default type is svg
    // but there is a webfont on the page
    if (did_action('template_redirect')) {
      cornerstone("FontAwesome")->registerStyles();
    }
  }

  /**
   * Migration for 6.3 to 6.4
   * and other products too
   */
  public static function migrateFromBeforeVersion64() {
    // To keep consitency between 6.3 to 6.4
    update_option("x_font_awesome_icon_type", "webfont", true);
  }
}
