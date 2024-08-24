<?php

/**
 * Register CodeMirror twig extension
 */
add_action('cs_twig_codemirror_enqueue', function() {
  $graphql = cs_js_asset_get('assets/js/app/cornerstone-twig');

  wp_register_script( 'cs-codemirror-twig', $graphql['url'], ['code-editor'], $graphql['version'] );
  wp_enqueue_script( 'cs-codemirror-twig' );
}, -1);

/**
 * Cornerstone booting enqueue codemirror twig
 */
add_action("cornerstone_app_enqueue_scripts", function() {
  do_action("cs_twig_codemirror_enqueue");
});
