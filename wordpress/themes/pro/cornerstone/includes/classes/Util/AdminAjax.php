<?php

namespace Themeco\Cornerstone\Util;

class AdminAjax {

  protected $action;
  protected $handler;
  protected $noPriv = false;
  protected $nonce_name = 'cornerstone_nonce';

  public function setAction( $action ) {
    $this->action = $action;
    return $this;
  }

  public function setNonce( $nonce_name ) {
    $this->nonce_name = $nonce_name;
    return $this;
  }

  public function setHandler( $handler ) {
    $this->handler = $handler;
    return $this;
  }

  public function setNoPriv( $noPriv ) {
    $this->noPriv = $noPriv;
    return $this;
  }

  public function validateConfig() {
    if ( empty ( $this->action  ) ) {
      throw new \Exception(__CLASS__ . ' missing action');
    }

    if ( empty ( $this->handler  ) || ! is_callable( $this->handler) ) {
      throw new \Exception(__CLASS__ . ' missing callable');
    }

  }

  public function start() {
    if ( is_admin() ) {
      $this->validateConfig();
      add_action( 'wp_ajax_cs_' . $this->action, [ $this, 'handler' ] );
      if ( $this->noPriv ) {
        add_action( 'wp_ajax_nopriv_cs_' . $this->action, [ $this, 'handler' ] );
      }
    }
  }

  public function handler() {
    do_action( 'cornerstone_before_admin_ajax' );
    $handler = $this->handler;
    $input = $this->getInput();
    if ( is_wp_error($input) ) {
      wp_send_json_error($input->get_error_message());
    } else {
      $handler( $input );
    }

  }

  public function getInput() {

    $data = array( 'request' => array() );
    $nonce_verification = false;

    if ( 'POST' === $_SERVER['REQUEST_METHOD'] ) {

      if ( isset( $_POST['request'] ) ) {

        $data['request'] = $_POST['request'];

        $transparent_fields = array('_cs_nonce', 'action', 'gzip');

        foreach ($transparent_fields as $field ) {
          if ( isset( $_POST[$field] ) ) {
            $data[$field] = $_POST[$field];
          }
        }

      } elseif ( isset( $_POST['data'] ) ) {
        $data = $_POST['data']; // Allow pass-through for things like backend options
      } else {
        $data = json_decode( \WP_REST_Server::get_raw_data(), true );
      }

      if ( isset( $_POST['_cs_nonce'] ) ) {
        $nonce_verification = wp_verify_nonce( $_POST['_cs_nonce'], $this->nonce_name );
      }

      if ( isset( $data['_cs_nonce'] ) ) {
        $nonce_verification = wp_verify_nonce( $data['_cs_nonce'], $this->nonce_name );
      }

      if ( isset( $data['request'] ) && ! is_array( $data['request'] ) ) {

        $decoded = base64_decode( $data['request'] );

        if ( isset( $data['gzip'] ) && $data['gzip'] ) {
          $decoded = gzdecode( $decoded );
        }

        $data['request'] = json_decode($decoded, true);

      }

    }

    if ( ! $nonce_verification ) {
      return new \WP_Error('cornerstone','nonce verification failed.' );
    }

    if ( isset( $data['request'] ) ) {
      $request = $data['request'];
      unset($data['request']);

      foreach ($request as $key => $value) {
        $data[$key] = $value;
      }
    }
    return $data;

  }

}