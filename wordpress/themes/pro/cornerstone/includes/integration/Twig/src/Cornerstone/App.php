<?php


/**
 * Register CS GraphQL script
 */
add_action('cs_twig_codemirror_enqueue', function() {

  cs_js_internal_asset_register( 'cs-codemirror-xml', 'assets/js/app/codemirror/xml');
  wp_enqueue_script( 'cs-codemirror-xml' );

  cs_js_internal_asset_register( 'cs-codemirror-twig', 'assets/js/app/codemirror/twig');
  wp_enqueue_script( 'cs-codemirror-twig' );

  cs_js_internal_asset_register( 'cs-codemirror-multiplex', 'assets/js/app/codemirror/multiplex');
  wp_enqueue_script( 'cs-codemirror-multiplex' );

}, -1);

/**
 * Cornerstone booting
 */
add_action('cornerstone_app_enqueue_scripts', function() {
  do_action('cs_twig_codemirror_enqueue');
});
