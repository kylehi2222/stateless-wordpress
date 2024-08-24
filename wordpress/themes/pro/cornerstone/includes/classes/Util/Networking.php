<?php

namespace Themeco\Cornerstone\Util;

class Networking {

  static protected $timeout = null;

  public static function set_curl_timeout_begin( $timeout ) {

    if ( ! is_null( self::$timeout ) || ! is_int( $timeout) ) {
      return;
    }

    self::$timeout = $timeout;

    add_filter('http_request_args', array( self::class, 'curl_timeout_request_args' ), 1000 );
    add_action('http_api_curl', array( self::class, 'curl_timeout_api_curl' ), 1000 );

  }

  public static function curl_timeout_request_args( $args ) {
    if ( is_int( self::$timeout ) ) {
      $args['timeout'] = self::$timeout;
    }
    return $args;
  }

  public static function curl_timeout_api_curl( $handle ) {

    if ( is_int( self::$timeout ) ) {
      curl_setopt( $handle, CURLOPT_CONNECTTIMEOUT, self::$timeout );
    	curl_setopt( $handle, CURLOPT_TIMEOUT, self::$timeout );
    }

  }

  public static function set_curl_timeout_end() {
    remove_filter('http_request_args', 'curl_timeout_request_args', 1000 );
    remove_action('http_api_curl', array( self::class, 'curl_timeout_api_curl' ), 1000 );
    self::$timeout = null;
  }

}
