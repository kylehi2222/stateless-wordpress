<?php

namespace Themeco\Cornerstone\Controllers;

use Themeco\Cornerstone\Services\Routes;

class AdobeFonts {
  protected $routes;

  public function __construct(Routes $routes) {
    $this->routes = $routes;
  }

  public function setup() {
    $this->routes->add_route('get', 'typekit-kit-by-id', [$this, 'get_kit_by_id']);
  }

  public function get_kit_by_id($data) {

    if ( !isset( $data['id'] ) ) {
      return new \WP_Error( 'cornerstone', 'ID missing' );
    }

    $request = wp_remote_get('https://typekit.com/api/v1/json/kits/' . $data['id'] . '/published');


    if ( is_wp_error( $request ) ) {
      return $request;
    }

    $data = json_decode( wp_remote_retrieve_body( $request ), true );

    if ( is_null( $data ) ) {
      return new \WP_Error( 'cornerstone', 'Invalid JSON' );
    }

    return $data;
  }

}
