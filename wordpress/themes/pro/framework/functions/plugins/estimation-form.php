<?php

// =============================================================================
// FUNCTIONS/GLOBAL/PLUGINS/estimation-form.php
// -----------------------------------------------------------------------------
// Plugin setup for theme compatibility.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Disable Licensing and Auto Updates
// =============================================================================

// Disable Licensing and Auto Updates
// =============================================================================

add_action('init', 'lfb_setThemeMode');

// Hack to hide validation message
add_action('admin_init', function() {
  // A bypass incase
  if (apply_filters('x_disable_estimation_form_hack', false)) {
    return;
  }

  try {
    // Run lfb code
    $instance = lfb_Core::instance(__FILE__, 'hack');
    $admin = lfb_Admin::instance($instance);

    $settings = $admin->getSettings();

    // Remove the auto update checker
    if (method_exists($admin, 'checkAutomaticUpdates')) {
      remove_action('admin_init', [$admin, 'checkAutomaticUpdates']);
    }

    // Fine or LFB is not ready yet
    if (empty($settings) || ( !empty($settings->purchaseCode) && strlen($settings->purchaseCode) > 8 ) ) {
      return;
    }

    // Update DB purchase code
    global $wpdb;
    $table_name = $wpdb->prefix . "lfb_settings";
    $wpdb->update($table_name, [
      'purchaseCode' => 'X-THEME-VALIDATED-CODE',
    ], [
      'id' => $settings->id,
    ]);

  } catch(\Exception $e) {
    if (WP_DEBUG) {
      trigger_error($e->getMessage(), E_USER_NOTICE);
    }
  }
}, -100);
