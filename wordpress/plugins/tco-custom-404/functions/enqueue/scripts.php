<?php

// =============================================================================
// FUNCTIONS/ENQUEUE/SCRIPTS.PHP
// -----------------------------------------------------------------------------
// Plugin scripts.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Enqueue Admin Scripts
// =============================================================================

// Enqueue Admin Scripts
// =============================================================================

function tco_custom_404_enqueue_admin_scripts( $hook ) {

  $hook_prefixes = array(
    'addons_page_x-extensions-custom-404',
    'theme_page_x-extensions-custom-404',
    'x_page_x-extensions-custom-404',
    'x_page_tco-extensions-custom-404',
    'x-pro_page_x-extensions-custom-404',
    'pro_page_tco-extensions-custom-404',
    'tco-extensions-custom-404',
    'settings_page_tco-extensions-custom-404',
  );

  if ( in_array($hook, $hook_prefixes) ) {

    wp_enqueue_script( 'postbox' );
    wp_enqueue_script( 'tco-custom-404-admin-js', TCO_CUSTOM_404_URL . '/js/admin/main.js', array( 'jquery' ), NULL, true );

  }

}

add_action( 'admin_enqueue_scripts', 'tco_custom_404_enqueue_admin_scripts' );
