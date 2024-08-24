<?php

namespace Themeco\Cornerstone\Util;

class Endpoint {
  protected $config;

  public function config( $config ) {
    $this->config = array_merge( [
      'requestKey' => null,
      'requireUser' => true,
      'handler' => null,
      'before_action' => 'cornerstone_before_custom_endpoint',
      'nonce_name' => 'cornerstone_nonce'
    ], $config );
    return $this;
  }

  public function start() {

    if (!isset( $this->config ) ) {
      throw new \Exception( 'Endpoint must have config called before start');
    }

    if (isset($_REQUEST[$this->config['requestKey']])) {
      add_filter( 'pre_handle_404', '__return_true' );
      add_action( 'template_redirect', [ $this, 'detect_request' ], 0 );
    }

    return $this;
  }

  public function detect_request() {

    // Tap into wp_die handler to add additional error details
    $this->handle_errors();

    if ( ! defined( 'DOING_AJAX' ) ) {
      define( 'DOING_AJAX', true );
    }

    do_action( $this->config['before_action'] );

    send_origin_headers();
    header( 'X-Robots-Tag: noindex' );
    send_nosniff_header();
    nocache_headers();

    ob_start();

    if ( ! is_user_logged_in() && $this->config['requireUser'] ) {
      return wp_send_json_error( array(
        'invalid_user' => true,
        'message' => 'No logged in user.'
      ) );
    }

    try {

      if ( ! is_callable( $this->config['handler' ] ) ) {
        throw new \Exception('Endpoint handle not set');
      }

      wp_send_json_success( call_user_func_array( $this->config['handler'], $this->get_input() ) );

    } catch( \Exception $e ) {
      return wp_send_json_error( array(
        'message' => $e->getMessage()
      ) );
    }

    wp_die();

  }

  public function get_input() {

    $request = new \WP_REST_Request( $_SERVER['REQUEST_METHOD'], 'cs-endpoint/' . $this->config['requestKey'] );

    $rawData = \WP_REST_Server::get_raw_data();
		$request->set_query_params( wp_unslash( $_GET ) );
		$request->set_body_params( wp_unslash( $_POST ) );
		$request->set_file_params( $_FILES );
		$request->set_body( $rawData );

    if ( is_wp_error( $request->has_valid_params() ) || is_wp_error( $request->sanitize_params() ) ) {
      throw new \Exception( 'invalid parameters' );
    }

    $data = json_decode($rawData, true);
    $nonce = null;
    if ($request->has_param('_nonce')) {
      $nonce = $request->get_param('_nonce');
    } else if ( isset($data['_nonce'])) {
      $nonce = $data['_nonce'];
    }

    if ( ! wp_verify_nonce( $nonce, $this->config['nonce_name'] ) ) {
      throw new \Exception( 'nonce verification failed: ' . $request->get_param('_nonce') );
    }

    if ( isset( $data['request'] ) ) {

      if ( isset( $data['gzip'] ) && $data['gzip'] ) {
        return [json_decode( gzdecode( base64_decode( $data['request'] ) ), true ), $request];
      }

      if ( is_array( $data['request'] ) ) {
        return [$data['request'], $request];
      }

    }

    return [$request];

  }


  public function handle_errors() {
    if ( defined('WP_DEBUG') && WP_DEBUG ) {
      add_filter('wp_php_error_message', [$this, 'append_error_data'], 10, 2 );
    }
  }

  public function append_error_data( $message, $error ) {
    $type = $this->lookup_error_type( $error['type'] );
    return $type . ': ' . $error['message'] . ' in ' . $error['file'] . ' on line ' . $error['line'] . '. ' . $message;
  }

  public function lookup_error_type( $type ) {

    switch ( $type ) {
      case E_ERROR:
        return 'E_ERROR';
      case E_WARNING:
        return 'E_WARNING';
      case E_PARSE:
        return 'E_PARSE';
      case E_NOTICE:
        return 'E_NOTICE';
      case E_CORE_ERROR:
        return 'E_CORE_ERROR';
      case E_CORE_WARNING:
        return 'E_CORE_WARNING';
      case E_COMPILE_ERROR:
        return 'E_COMPILE_ERROR';
      case E_COMPILE_WARNING:
        return 'E_COMPILE_WARNING';
      case E_USER_ERROR:
        return 'E_USER_ERROR';
      case E_USER_WARNING:
        return 'E_USER_WARNING';
      case E_USER_NOTICE:
        return 'E_USER_NOTICE';
      case E_STRICT:
        return 'E_STRICT';
      case E_RECOVERABLE_ERROR:
        return 'E_RECOVERABLE_ERROR';
      case E_DEPRECATED:
        return 'E_DEPRECATED';
      case E_USER_DEPRECATED:
        return 'E_USER_DEPRECATED';
    }

    return '';

  }

}
