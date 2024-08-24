<?php

/**
 * Font Rest API
 */

namespace Themeco\Cornerstone\Controllers;

use DomainException;
use Themeco\Cornerstone\Services\GlobalFonts;
use Themeco\Cornerstone\Services\Routes;

class Fonts {
  protected $routes;
  private $globalFonts;

  public function __construct(Routes $routes, GlobalFonts $globalFonts) {
    $this->routes = $routes;
    $this->globalFonts = $globalFonts;
  }

  public function setup() {
    $this->routes->add_route('get', 'custom-font-css', [$this, 'getCustomFontCSS']);
  }

  /**
   * Custom Font CSS loading style content
   * @see ControlFontFamily
   */
  public function getCustomFontCSS($data) {

    if ( !isset( $data['id'] ) ) {
      return new \WP_Error( 'cornerstone', 'ID missing' );
    }

    $id = $data['id'];

    $customFonts = $this->globalFonts->get_font_config()['customFontItems'];

    foreach ($customFonts as $font) {
      if ($font['_id'] !== $id) {
        continue;
      }

      // Load CSS
      $config = $this->globalFonts->getCustomFontConfig();
      $css = $this->globalFonts->make_custom_font_css($font, $config);

      return [ 'css' => $css ];
    }

    throw new DomainException('Could not load Custom Font CSS for ID : ' . $id);
  }
}
