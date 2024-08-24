<?php

namespace Themeco\Cornerstone\Services;

use Themeco\Cornerstone\Plugin;
use Themeco\Cornerstone\Util\AdminAjax;

class Http implements Service {

  protected $ajaxMakeRestNonce;

  public function __construct(AdminAjax $ajaxMakeRestNonce ) {
    $this->ajaxMakeRestNonce = $ajaxMakeRestNonce;
  }

  public function setup() {
    $this->ajaxMakeRestNonce
      ->setAction('make_nonce')
      ->setHandler([$this,'ajaxMakeNonce'])
      ->start();
  }

  public function fetchConfig() {
    return [
      'rootUrl' => esc_url_raw( rest_url() ),
      'nonce' => $this->createRestNonce(),
      'nonceUrl' => esc_url_raw(add_query_arg(
        [ 'action' => 'cs_make_nonce'],
        $this->ajaxUrl()
      ) )
    ];
  }

  public function ajaxUrl() {
    return admin_url( 'admin-ajax.php' );
  }

  public function ajaxMakeNonce() {
    echo $this->createRestNonce();
    die;
  }

  public function createNonce() {
    return wp_create_nonce( 'cornerstone_nonce' );
  }

  public function createRestNonce() {
    return wp_create_nonce('wp_rest');
  }

  public function gzip() {
    return ( ! defined('CS_DISABLE_GZIP') || ! CS_DISABLE_GZIP ) && function_exists('gzcompress') && function_exists('gzdecode');
  }
}
