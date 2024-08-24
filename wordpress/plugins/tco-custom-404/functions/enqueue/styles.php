<?php

// =============================================================================
// FUNCTIONS/ENQUEUE/STYLES.PHP
// -----------------------------------------------------------------------------
// Plugin styles.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Enqueue Admin Styles
// =============================================================================

// Enqueue Admin Styles
// =============================================================================

function tco_custom_404_enqueue_admin_styles( $hook ) {

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

    wp_enqueue_style( 'postbox' );
    wp_enqueue_style( 'tco-disqus-comments-admin-css', TCO_CUSTOM_404_URL . '/css/admin/style.css', NULL, NULL, 'all' );

  }

}

add_action( 'admin_enqueue_scripts', 'tco_custom_404_enqueue_admin_styles' );
