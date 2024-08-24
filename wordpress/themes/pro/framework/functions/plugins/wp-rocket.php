<?php

/**
 * This integration mainly excludes JS from being double minified
 * and from being included in the "Delay JS Execution"
 */

function x_wp_rocket_exclude_js($excludes) {
  $excludes[] = X_ROOT_DIRECTORY . '/framework/dist/js/site/(.*)';

  return $excludes;
}

function x_wp_rocket_exclude_css($excludes) {
  $excludes[] = X_ROOT_DIRECTORY . '/framework/dist/css/site/(.*)';

  return $excludes;
}

add_filter('rocket_exclude_js', 'x_wp_rocket_exclude_js');

add_filter('rocket_delay_js_exclusions', 'x_wp_rocket_exclude_js');

add_filter('rocket_exclude_css', 'x_wp_rocket_exclude_css');
