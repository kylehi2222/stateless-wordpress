<?php

require_once(__DIR__ . '/post-password.php');

add_action( 'cs_before_save_json_content', 'wp_remove_targeted_link_rel_filters' );
add_action( 'cs_after_save_json_content', 'wp_init_targeted_link_rel_filters' );
add_filter( 'script_loader_tag', 'cs_whitelist_script_tag', 0, 3 );


// Put this line in a plugin or child theme to disable additional WordPress HTML
// add_filter('cs_disable_wp_extraneous', '__return_true' );

if ( apply_filters( 'cs_disable_wp_extraneous', false ) ) {

  // Disconnect Block CSS
  add_action( 'wp_enqueue_scripts', function (){
    wp_dequeue_style( 'wp-block-library' );
    wp_dequeue_style( 'wp-block-library-theme' );
    wp_dequeue_style( 'wc-block-style' ); // Remove WooCommerce block CSS
  }, 100 );

  // No Global Vars
  remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
  remove_action( 'wp_footer', 'wp_enqueue_global_styles', 1 );

  // No Emoji detection
  remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
  remove_action( 'wp_print_styles', 'print_emoji_styles' );

  // No duotone SVG
  remove_filter( 'render_block', 'wp_render_duotone_support', 10 );
  remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );

}

function cornerstone_wxr_export_skip_postmeta( $skip, $meta_key, $meta  ) {
  if ( in_array( $meta_key, [ '_cs_generated_styles', '_cs_generated_tss', '_cs_component_map' ], true ) ) {
    return true;
  }

  $slash = apply_filters( 'cs_wp_export_slash', [
    '_cornerstone_settings',
    '_cornerstone_data'
  ]);

  if ( in_array( $meta_key, $slash, true ) ) { ?>
    <wp:postmeta>
      <wp:meta_key><?php echo wxr_cdata( $meta->meta_key ); ?></wp:meta_key>
      <wp:meta_value><?php echo wxr_cdata( wp_slash( $meta->meta_value ) ); ?></wp:meta_value>
    </wp:postmeta>
    <?php return true;
  }

  return $skip;
}

add_filter( 'wxr_export_skip_postmeta', 'cornerstone_wxr_export_skip_postmeta', 10, 3 );
