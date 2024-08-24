<?php

/*

Plugin Name: Custom 404
Plugin URI: http://theme.co/
Description: Redirect all of your site's 404 errors to a custom page that you have complete control over. Easily create any layout you want using page templates, shortcodes, and more!
Version: 2.0.6
Author: Themeco
Author URI: http://theme.co/
Text Domain: __tco__
Themeco Plugin: tco-custom-404

*/

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Define Constants and Global Variables
//   02. Setup Menu
//   03. Initialize
// =============================================================================

// Define Constants and Global Variables
// =============================================================================

//
// Constants.
//

define( 'TCO_CUSTOM_404_VERSION', '2.0.6' );
define( 'TCO_CUSTOM_404_URL', plugins_url( '', __FILE__ ) );
define( 'TCO_CUSTOM_404_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );


//
// Global variables.
//

$tco_custom_404_options = array();



// Setup Menu
// =============================================================================

function tco_custom_404_options_page() {
  require( 'views/admin/options-page.php' );
}

function tco_custom_404_menu() {
  add_options_page( __( 'Custom 404', '__tco__' ), __( 'Custom 404', '__tco__' ), 'manage_options', 'tco-extensions-custom-404', 'tco_custom_404_options_page' );
}

function x_tco_custom_404_menu() {
  add_submenu_page( 'x-addons-home', __( 'Custom 404', '__tco__' ), __( 'Custom 404', '__tco__' ), 'manage_options', 'tco-extensions-custom-404', 'tco_custom_404_options_page' );
}

$theme = wp_get_theme(); // gets the current theme
$is_pro_theme = ( 'Pro' == $theme->name || 'Pro' == $theme->parent_theme );
$is_x_theme = function_exists( 'CS' );
add_action( 'admin_menu', ( $is_pro_theme || $is_x_theme ) ? 'x_tco_custom_404_menu' : 'tco_custom_404_menu', 100 );



// Initialize
// =============================================================================

function tco_custom_404_init() {

  //
  // Textdomain.
  //

  load_plugin_textdomain( '__tco__', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );


  //
  // Styles and scripts.
  //

  require( 'functions/enqueue/styles.php' );
  require( 'functions/enqueue/scripts.php' );


  //
  // Notices.
  //

  require( 'functions/notices.php' );


  //
  // Output.
  //

  require( 'functions/output.php' );

}

add_action( 'init', 'tco_custom_404_init' );

//
// Activate hook.
//

function tco_custom_404_activate () {
  $x_plugin_basename = 'x-custom-404/x-custom-404.php';

  if ( is_plugin_active( $x_plugin_basename ) ) {
    $tco_data = get_option('tco_custom_404');
    $x_data = get_option('x_custom_404');
    if (empty($tco_data) && !empty($x_data)) {
      $tco_data = array();
      foreach($x_data as $key => $value) {
        $key = str_replace('x_', 'tco_', $key);
        $tco_data[ $key ] = $value;
      }
      update_option( 'tco_custom_404', $tco_data );
    }
    deactivate_plugins( $x_plugin_basename );
  }
}

register_activation_hook( __FILE__, 'tco_custom_404_activate' );
