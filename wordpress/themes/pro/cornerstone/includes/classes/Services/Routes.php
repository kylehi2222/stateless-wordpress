<?php

namespace Themeco\Cornerstone\Services;

use Themeco\Cornerstone\Plugin;

class Routes implements Service {

  protected $save_handlers = array();
  protected $document_save_handlers = array();
  protected $plugin;
  protected $http;

  public function __construct(Plugin $plugin, Http $http) {
    $this->plugin = $plugin;
    $this->http = $http;
  }

  public function setup() {
    add_action( 'rest_api_init', [ $this, 'register' ]);
  }

  public function add_save_handler( $name, $callback ) {
    $this->save_handlers[$name] = $callback;
  }

  public function add_document_save_handler( $name, $callback ) {
    $this->document_save_handlers[$name] = $callback;
  }

  public function get_save_handlers() {
    return $this->save_handlers;
  }

  public function get_document_save_handlers() {
    return $this->document_save_handlers;
  }

  public function register() {
    $params = [
      'callback' => [$this, 'rest_endpoint'],
      'methods' => ['GET', 'POST'],
      'permission_callback' => 'is_user_logged_in'
    ];

    register_rest_route( 'themeco', 'data/(?P<path>[a-zA-Z0-9-_]+)/(?P<id>[a-zA-Z0-9-_]+)', $params );
    register_rest_route( 'themeco', 'data/(?P<path>[a-zA-Z0-9-_]+)', $params );
  }

  public function handle_errors() {
    if ( defined('WP_DEBUG') && WP_DEBUG ) {
      add_filter('wp_php_error_message', [$this, 'append_error_data'], 10, 2 );
    }
  }

  public function process_rest_params( $params ) {

    $gzip = isset( $params['gzip'] ) && $params['gzip'];
    $result = [];

    if ( isset( $params['request'] ) ) {
      if ( $gzip ) {
        $result = json_decode( gzdecode( base64_decode( $params['request'], true ) ), true );
      } else if ( is_array( $params['request'] ) ) {
        $result = $params['request'];
      }
    }

    unset( $params['gzip'] );
    unset( $params['request'] );

    return array_merge( $params, $result );
  }

  public function rest_endpoint($request) {

    $this->handle_errors();

    $path = $request->get_param('path');
    $no_gzip = $request->get_param('gzip') === '0';

    $data = null;

    $this->plugin->resolveFromConfig('controllers');

    ob_start();
    do_action( 'tco_routes', $this );
    do_action( 'cornerstone_before_custom_endpoint' );
    $extraneous = ob_get_clean();

    send_origin_headers();
    header( 'X-Robots-Tag: noindex' );
    send_nosniff_header();
    nocache_headers();

    $params = $this->process_rest_params( $request->get_params() );

    try {
      $method = strtolower($_SERVER['REQUEST_METHOD']);
      ob_start();
      $data = apply_filters( "tco_routing_$method/$path", null, $params );
      $extraneous .= ob_get_clean();
    } catch (\Exception $e) {
      $message = $e->getMessage();

      if ($message === 'not-found') {
        $data = new \WP_Error( 'tco-routing', 'Not found', [ 'status' => 404 ] );
      } else {
        $data = new \WP_Error( 'tco-routing', $e->getMessage());
      }

    }

    if (is_null($data)) {
      $data = new \WP_Error( 'tco-routing', "No response for path: $path" );
    }

    $response = [];

    if ($extraneous) {
      $response['extraneous'] = $extraneous;
    }

    if (is_wp_error($data)) {
      return $data;
    } else {
      if ($no_gzip || !$this->http->gzip()) {
        $response['gzip'] = false;
        $response['data'] = $data;
      } else {
        $response['data'] = base64_encode( gzcompress( json_encode( $data ) ) );
        if (isset($params['cacheSig'])) {
          $response['cacheSig'] = md5($response['data']);
          if ( $params['cacheSig'] === $response['cacheSig'] ) {
            $response['data'] = 'cache-hit';
            $response['gzip'] = false;
          }
        }

      }
    }

    return $response;

  }

  public function add_route($method, $path, $callback) {
    add_filter("tco_routing_$method/$path", function( $result, $params ) use ($callback) {
      return call_user_func_array($callback, [$params]);
    }, 10, 2);
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
