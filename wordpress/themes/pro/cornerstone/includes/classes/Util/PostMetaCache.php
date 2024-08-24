<?php

namespace Themeco\Cornerstone\Util;

class PostMetaCache {
  protected $key = '';
  protected $bypass = null;

  public function setup( $key, $bypass = null ) {
    $this->key = $key;
    $this->bypass = $bypass;
  }

  public function resolve( $id, $fn ) {

    if (! $this->key) {
      trigger_error("Can not use PostMetaCache before it is setup | $id", E_USER_WARNING);
      return null;
    }

    $cached = $this->unserialize( get_post_meta( $id, $this->key, true ) );

    if ( ! is_array( $cached ) || $this->shouldNotCache() ) {

      $cached = [ 'v' => $fn( $id ) ];

      if ( is_null( $cached ) ) {
        return null;
      }

      update_post_meta( $id, $this->key, $this->serialize( $cached ) );

    }

    return $cached['v'];
  }

  public function shouldNotCache() {
    $bypass = $this->bypass;
    return is_callable($bypass) ? $bypass() : false;
  }

  public function unserialize( $input ) {
    return json_decode( $input, true );
  }

  public function serialize($input) {
    return wp_slash( wp_json_encode( $input ) );
  }
}