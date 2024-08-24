<?php

// =============================================================================
// VIEWS/SITE/CUSTOM-404.PHP
// -----------------------------------------------------------------------------
// Plugin site output.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Require Options
//   02. Output
// =============================================================================

// Require Options
// =============================================================================

function tco_custom_404_filter_template( $template ) {

  require( TCO_CUSTOM_404_PATH . '/functions/options.php' );

  if ( ! isset( $tco_custom_404_enable ) || ! $tco_custom_404_enable ) {
    return $template;
  }

  global $sitepress;

  if ( function_exists( 'icl_object_id' ) && is_callable( array( $sitepress, 'get_current_language' ) ) ) {
      $custom_404_post = get_post( icl_object_id( $tco_custom_404_entry_include, 'page', false, $sitepress->get_current_language() ) );
      if($custom_404_post == null) {
        $custom_404_post = get_post( (int) $tco_custom_404_entry_include );
      }
  }
  else {
      $custom_404_post = get_post( (int) $tco_custom_404_entry_include );
  }

  if ( ! is_a( $custom_404_post, 'WP_Post' ) ) {
    return $template;
  }

  GLOBAL $wp_query;
  GLOBAL $post;

  $post = $custom_404_post;

  $wp_query->posts             = array( $post );
  $wp_query->queried_object_id = $post->ID;
  $wp_query->queried_object    = $post;
  $wp_query->post_count        = 1;
  $wp_query->found_posts       = 1;
  $wp_query->max_num_pages     = 0;
  //$wp_query->is_404            = false;
  $wp_query->is_page           = true;
  $wp_query->is_singular	     = true;

  do_action('cs_assign_active_content', $post->ID);

  return get_page_template();

}

add_filter( '404_template', 'tco_custom_404_filter_template' );
