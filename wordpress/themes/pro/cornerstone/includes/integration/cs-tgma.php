<?php

namespace Cornerstone\TGMA;

use WP_Error;

// Install a plugin registered with TGM
// Used in AJAX requests from our custom install UI
add_action("cs_tgma_install_plugin", function($plugin) {
  $install = cs_install_plugin($plugin);

  if ( is_wp_error( $install ) ) {
    return wp_send_json_error( array( 'message' => $install->get_error_message() ) );
  }

  wp_send_json_success();

}, 0, 1);

add_action("cs_tgma_activate_plugin", function($plugin) {

  $activate = activate_plugin( $plugin );

  if ( is_wp_error( $activate ) ) {
    wp_send_json_error( [ 'message' => $activate->get_error_message() ] );
  }

  wp_send_json_success( [ 'plugin' => $plugin ] );

}, 0, 1);

add_filter("cs_tgma_get_instance", function() {
  cs_tgma_load();
  $tgmpa = \TGM_Plugin_Activation::get_instance();

  return $tgmpa;
});

function cs_tgma_load() {
  require_once(__DIR__ . "/../vendor/class-tgm-plugin-activation.php");
}

/**
 * Install through TGM
 */
function cs_install_plugin($plugin) {

  if ( ! $plugin ) {
    return new WP_Error( 'x-tgmpa-integration', __( 'No plugin specified.', '__x__' ) );
  }

  if ( ! current_user_can( 'install_plugins' ) ) {
    return new WP_Error( 'x-tgmpa-integration', __( 'Your user account does not have permission to install plugins.', '__x__' ) );
  }

  $tgmpa = apply_filters("cs_tgma_get_instance", (object)[]);

  // In case somehow the plugin is no longer registered in TGM
  if ( ! isset( $tgmpa->plugins[ $plugin ] ) ) {
    return new WP_Error( 'x-tgmpa-integration', __( 'Plugin not registered.', '__x__' ) );
  }

  // Nothing to do if already installed
  if ( $tgmpa->is_plugin_installed( $plugin ) ) {
    return new WP_Error( 'x-tgmpa-integration', __( 'Plugin already installed.', '__x__' ) );
  }

  // Abort if file system not writable
  if ( ! cs_can_write_to_filesystem() ) {
    return new WP_Error( 'x-tgmpa-integration', __( 'Your WordPress file permissions do not allow plugins to be installed.', '__x__' ) );;
  }

  cs_tgmpa_load_upgrader();

  $skin = new CS_Plugin_Upgrader_Skin();
  $upgrader = new \Plugin_Upgrader( $skin );
  $result = $upgrader->install( $tgmpa->get_download_url( $plugin ) );

  if ( is_wp_error( $result ) ) {
    return $result;
  }

  $skin_error = $skin->get_error();

  if ( is_wp_error( $skin_error ) ) {
    return $skin_error;
  }

  return true;

}

function cs_tgmpa_load_upgrader() {
  if ( ! class_exists( 'Plugin_Upgrader', false ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
  }

  if ( ! class_exists( 'Cornerstone\TGMA\CS_Plugin_Upgrader_Skin' ) ) {
    class CS_Plugin_Upgrader_Skin extends \WP_Upgrader_Skin {

      public $error_messages = array();

      public function get_error() {
        return empty($this->error_messages) ? false : new WP_Error('x-tgmpa-integration', implode(' | ', $this->error_messages ) );
      }

      public function error( $errors ) {
        if ( is_string( $errors ) ) {
          $this->error_messages[] = $errors;
        } elseif ( is_wp_error( $errors ) && $errors->has_errors() ) {
          foreach ( $errors->get_error_messages() as $message ) {
            if ( $errors->get_error_data() && is_string( $errors->get_error_data() ) ) {
              $this->error_messages[] = $message . ' ' . esc_html( strip_tags( $errors->get_error_data() ) );
            } else {
              $this->error_messages[] = $message;
            }
          }
        }
      }

      public function after() { }
      public function header() { }
      public function footer() { }
      public function feedback($string, ...$args) {}
    }
  }
}

