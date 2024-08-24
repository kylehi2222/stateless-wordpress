<?php

/**
 * Register from cs_element_rendering
 */
add_action('wp_enqueue_scripts', function() {
  $asset = cs_js_asset_get("assets/js/site/cs-text-type");
  wp_register_script("cs-text-type", $asset['url'], ['cs'], $asset['version'], true);
}, -1);

// Enqueue always for preview
add_action("cs_before_preview_frame", function() {
  wp_enqueue_script("cs-text-type");
});
