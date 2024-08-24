<?php

namespace Themeco\Cornerstone\Services;

use Themeco\Cornerstone\Plugin;

class Preferences implements Service {

  protected $defaults;
  protected $preferences = [];
  protected $config;

  public function __construct(Config $config) {
    $this->config = $config;
  }

  public function get_defaults() {
    if ( ! isset( $this->defaults ) ) {
      $defaults = $this->config->group( 'preference-defaults' );
      $keybinding_data = $this->config->group( 'keybindings' );

      $defaults['keybindings'] = [];

      foreach ($keybinding_data as $name => $binding) {
        $defaults['keybindings'][$name] = $binding[0];
      }

      $this->defaults = apply_filters('cs_app_preference_defaults', $defaults );
    }
    return $this->defaults;
  }

  public function get_user_preferences( $user_id = null ) {

    if ( ! $user_id ) {

      $user_id = get_current_user_id();

      if ( ! $user_id ) {
        return array();
      }

    }

    if ( ! isset( $this->preferences['u'. $user_id ] ) ) {
      $user_meta = get_user_meta( $user_id, 'cs_app_preferences', true );

      if ( ! is_array( $user_meta ) ) {
        $user_meta = array();
      }

      $defaults = $this->get_defaults();
      $preferences = array_merge( $defaults, $user_meta );
      $preferences['keybindings'] = array_merge( $defaults['keybindings'], isset( $user_meta['keybindings'] ) ? $user_meta['keybindings'] : [] );

      $this->preferences['u'. $user_id ] = apply_filters('cs_app_preferences', $preferences );

    }

    return $this->preferences['u'. $user_id ];

  }

  public function update_user_preferences( $user_id = null, $values = null ) {

    if ( ! $values ) {
      return null;
    }

    if ( ! $user_id ) {

      $user_id = get_current_user_id();

      if ( ! $user_id ) {
        return array();
      }

    }

    unset( $this->preferences['u'. $user_id ] );

    return update_user_meta( $user_id, 'cs_app_preferences', $values );

  }

  public function get_preference_controls() {
    return apply_filters("cs_preference_controls", $this->config->group( 'preference-controls' ));
  }

  public function get_preference( $name, $fallback = null, $user_id = null ) {
    $preferences = $this->get_user_preferences($user_id);
    return isset( $preferences[ $name ] ) ? $preferences[$name] : $fallback;
  }

  public function get_preferences() {
    return $this->preferences;
  }
}
