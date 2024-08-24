<?php

namespace Themeco\Cornerstone\Services;

class Env implements Service {
  protected $data;

  public function envData() {
    if ( ! isset( $this->data ) ) {
      $this->data = array_merge([
        'templates' => [
          'title' => 'Themeco',
          'url'   => 'https://templates.theme.co/wp-json/asset-server/v1',
          'legacyUrl' => 'https://demo.theme.co/designcloud/wp-json/design-cloud/v3'
        ]
      ], apply_filters('_cornerstone_app_env', [
        'product' => 'cornerstone'
      ] ));


    }

    return $this->data;
  }

  // Really just means if they have access to layout and archive editors
  public function isSiteBuilder() {
    $env = $this->envData();
    return isset( $env['siteBuilder'] ) && $env['siteBuilder'];
  }
}
