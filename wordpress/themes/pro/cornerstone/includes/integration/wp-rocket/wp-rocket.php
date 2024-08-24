<?php

/**
 * This integration mainly excludes JS from being double minified
 * and from being included in the "Delay JS Execution"
 */

function cs_wp_rocket_exclude_js($excludes) {
  $excludes[] = CS_ROOT_DIRECTORY . 'assets/js/site/(.*)';

  return $excludes;
}

function cs_wp_rocket_exclude_css($excludes) {
  $excludes[] = CS_ROOT_DIRECTORY . 'assets/css/site/(.*)';

  return $excludes;
}

add_filter('rocket_exclude_js', 'cs_wp_rocket_exclude_js');

add_filter('rocket_delay_js_exclusions', 'cs_wp_rocket_exclude_js');

add_filter('rocket_exclude_css', 'cs_wp_rocket_exclude_css');
