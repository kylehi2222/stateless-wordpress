<?php

/**
 * Register CS GraphQL script
 */
add_action('cs_graphql_codemirror_enqueue', function() {
  $graphql = cs_js_asset_get('assets/js/app/cornerstone-graphql');
  wp_register_script( 'cs-graphql', $graphql['url'], ['code-editor'], $graphql['version'] );
  wp_enqueue_script( 'cs-graphql' );
}, -1);

/**
 * Cornerstone booting
 */
add_action("cornerstone_app_enqueue_scripts", function() {
  do_action("cs_graphql_codemirror_enqueue");
});
