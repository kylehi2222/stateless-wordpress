<?php

namespace Themeco\Cornerstone\Services;

use Themeco\Cornerstone\Plugin;

class Config implements Service {

  protected $config_store = [];

  protected $plugin;

  public function __construct(Plugin $plugin) {
    $this->plugin = $plugin;
  }

  /**
   * Retrieve a particular configuration set and apply filters.
   * @param  string $name      string path to config file
   * @param  string $namespace namespace to prepend to key for caching
   * @param  string $path      alternate path
   * @return array            requested configuration values
   */
  public function group( $name = '', $namespace = '', $path = '', $abs_path = false ) {

    $key = ( $namespace ) ? "{$namespace}.{$name}" : $name;
    $path = ( $path ) ? $path : "includes/config";
    if ( ! isset( $this->config_store[ $key ] ) ) {

      $config_path = trailingslashit( $path ) . $name . '.php';
      if ( ! $abs_path ) {
        $config_path = $this->plugin->path . '/' . $config_path;
      }
      $value = include( $config_path );
      $data = is_array( $value ) ? $value : array();

      /**
       * Filter example: $name == 'folder/defaults-file'
       * 'plugin_config_folder_defaults-file'
       * 'plugin_config_folder_defaults-file'
       */
      $filter_name = sanitize_key( str_replace( '.', '_', str_replace( '/', '_', $key ) ) );
      $this->config_store[ $key ] = apply_filters( "cornerstone_config_{$filter_name}", $data );

    }

    return $this->config_store[ $key ];

  }

  public function json($name) {
    $path = "includes/config";
    $path = $this->plugin->path . '/' . $path;

    $path = $path . '/' . $name . '.json';

    $json = file_get_contents($path);
    $json = json_decode($json, true);

    return $json;
  }
}
