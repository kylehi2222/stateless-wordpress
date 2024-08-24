<?php
/*
Plugin Name: Asset CleanUp Pro: Plugin Filtering
Plugin URI: https://www.gabelivan.com/items/wp-asset-cleanup-pro/
Description: Prevent plugins from loading in certain situations (e.g. based on the rules set in "Plugins Manager")
Version: 1.0
Author: Gabriel Livan
Author URI: https://codeable.io/developers/gabriel-livan/?ref=d3TOr
*/

// NOTE: Please do not edit this file directly as it's generated by Asset CleanUp Pro
// Instead, create your own MU plugin if you have to
// This file is automatically deleted once Asset CleanUp Pro is deactivated

if (! defined('WP_PLUGIN_DIR')) {
	return; // can't be accessed directly as it needs to be triggering with the WordPress environment
}

$wpacuMuPluginFilterFile = WP_PLUGIN_DIR.'/wp-asset-clean-up-pro/pro/mu-plugins/wpacu-plugins-filter-main.php';

// Activated per site (default)
// Get a possible filtered value (e.g. by Plugin Organizer or other similar plugin) and not the one from the database
// as only the current active plugins matter
$wpacuActivePlugins = get_option('active_plugins', array());

// In case we're dealing with a MultiSite setup
if (is_multisite()) {
	$wpacuActiveSiteWidePlugins = (array)get_site_option('active_sitewide_plugins', array());

	if ( ! empty($wpacuActiveSiteWidePlugins) ) {
		foreach (array_keys($wpacuActiveSiteWidePlugins) as $activeSiteWidePlugin) {
			$wpacuActivePlugins[] = $activeSiteWidePlugin;
		}
	}
}

// Triggers only if the file exists and Asset CleanUp Pro is activated
if ( is_array($wpacuActivePlugins) && in_array('wp-asset-clean-up-pro/wpacu.php', $wpacuActivePlugins) && is_file($wpacuMuPluginFilterFile) ) {
	include_once $wpacuMuPluginFilterFile;
}
