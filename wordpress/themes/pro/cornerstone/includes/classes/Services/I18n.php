<?php

namespace Themeco\Cornerstone\Services;

use Themeco\Cornerstone\Plugin;

class I18n implements Service {

  protected $i18n_strings = [];
  protected $plugin;
  protected $config;

  public function __construct(Plugin $plugin, Config $config) {
    $this->plugin = $plugin;
    $this->config = $config;
  }

  /**
   * Get a named set of localized strings from the i18n directory
   * @param  string  $group      Name of the strings file to load
   * @param  boolean $namespace Should we prepend a namespace to the keys?
   * @return array              Localized strings
   */
  public function group( $group, $namespace = true, $filter = '' ) {
    $strings = $this->config->group( $group, 'i18n', $this->plugin->path . '/includes/i18n', true );

    if ( $filter ) {

      $filtered = array();

      foreach ($strings as $key => $value) {
        if ( 0 === strpos($key,"$filter.") ) {
          $k = substr($key, strlen($filter) + 1);
          $filtered[$k] = $value;
        }
      }

      $strings = $filtered;

    }

    return $strings;

  }

  public function get( $key ) {

    if ( ! isset( $this->i18n_strings[ $key ] ) ) {
      $group = 'common';
      $group_index = strpos($key, '.');
      if ( -1 !== $group_index ) {
        $group = substr( $key, 0, $group_index );
      }
      $strings = $this->group( $group );
      foreach ($strings as $string => $value) {
        $this->i18n_strings[ $string ] = $value;
      }
    }

    return isset( $this->i18n_strings[ $key ] ) ? $this->i18n_strings[ $key ] : '';
  }

}
