<?php

namespace Themeco\Cornerstone\Services;

class GlobalColors implements Service {

  public $queue = array();
  protected $stored_color_items;
  protected $color_items;
  protected $font_config;

  public function setup() {
    add_filter( 'cs_css_post_process_color', array( $this, 'cssPostProcessColor') );
    add_filter( 'cs_css_post_process_tss-color', array( $this, 'cssPostProcessColor') );
  }

  public function getStoredColorItems() {
    if ( ! $this->stored_color_items ) {
      $this->stored_color_items = $this->loadColors();
    }

    return $this->stored_color_items;
  }

  public function getAllColorItems() {

    if ( ! $this->color_items ) {
      $this->color_items = array_merge( $this->getStoredColorItems(), $this->getExtended() );
    }

    return $this->color_items;

  }

  public function getExtended() {
    return apply_filters('cs_colors_extend', array() );
  }

  public function getAppData() {
    return array(
      'items' => $this->getStoredColorItems()
    );
  }

  protected function loadColors() {

    $preloaded = apply_filters('cs_preload_colors', false );
    if ( $preloaded ) {
      return $preloaded;
    }

    $stored = get_option( 'cornerstone_color_items' );

    if ( $stored === false ) {
      $stored = wp_slash( cs_json_encode( [] ) );
      update_option( 'cornerstone_color_items', $stored );
    }

    return apply_filters( 'cs_color_items', ( is_null( $stored ) ) ? array() : json_decode( wp_unslash( $stored ), true ) );

  }

  public function locateColor( $_id ) {
    $this->getAllColorItems();
    foreach ($this->color_items as $color) {
      if ( isset( $color['_id'] ) && $_id === $color['_id'] ) {
        return $color;
      }
    }
    return null;
  }

  public function applyColor($matches) {
    if ( ! isset( $matches[1] ) ) {
      return 'transparent';
    }

    $color = $this->locateColor( $matches[1] );

    if ( ! $color ) {
      return 'transparent';
    }

    // No opacity
    if ( ! isset( $matches[2] ) ) {
      return $color['value'];
    }

    // PLACEHOLDER: Parse $color['value'] and re apply "alpha" (See ColorParser.php)
    $alpha = max(min(1,floatval($matches[2])),0);

    // RGBA
    if (strpos($color['value'], "rgb") !== false) {
      preg_match('/rgba?\((?:\s*?(\d+),)(?:\s*?(\d+),)(?:\s*?(\d+))/', $color['value'], $matches);
      array_shift($matches);
      if (count($matches) === 3) {
        return "rgba(" . $matches[0] . ',' . $matches[1] . ',' . $matches[2] . ',' . $alpha .')';
      }

      // Fallback if bad value
      return $color['value'];
    }

    // HEX Alpha
    if (strpos($color['value'], "#") === 0) {
      $value = $color['value'];

      // 3 Letter Hex like #FFF
      // Add the FFF again
      if (strlen($color['value']) === 4) {
        $value .= str_replace("#", "", $color['value']);
      }

      $alpha_255 = min(round($alpha * 255), 255);
      $alpha_hex = sprintf("%02x", $alpha_255);
      $value = $value . $alpha_hex;
      return $value;
    }

    // Unknown color fallback to value
    return $color['value'];
  }

  public function cssPostProcessColor( $value ) {

    if ( is_string( $value ) && false !== strpos( $value, 'global-color:' ) ) {
      while (preg_match( '/global-color:([\w\d-]+)?(?:\:(\d+\.\d+|\.\d+|\d+))?/', $value, $matches ) ) {
        $value = str_replace($matches[0], $this->applyColor($matches), $value);
      }
    }

    return $value;
  }

  /**
   * Converts string to hex if it is not already
   *
   * @param string $rgba
   *
   * @return string
   */
  public function rgbToHex( $rgba ) {
    if ( strpos( $rgba, '#' ) === 0 ) {
      return $rgba;
    }

    preg_match( '/^rgba?[\s+]?\([\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?/i', $rgba, $by_color );

    if (empty($by_color)) {
      return $rgba;
    }

    return sprintf( '#%02x%02x%02x', $by_color[1], $by_color[2], $by_color[3] );
  }
}
