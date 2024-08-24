<?php

// =============================================================================
// FUNCTIONS/GLOBAL/CONTENT.PHP
// -----------------------------------------------------------------------------
// Functions pertaining to content output.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Get Content Layout
//   02. Alternate Title
//   03. Link Pages
//   04. Entry Navigation
//   05. Does Not Need Entry Meta
//   06. Scroll Top Anchor
//   07. Legacy Header Widget Areas
//   08. Legacy Slider Below with New Masthead
//   09. Legacy Slider Above with New Masthead
//   10. Comment template schema
// =============================================================================

// Get Content Layout
// =============================================================================

//
// First checks if the global content layout is "full-width." If the global
// content layout is not "full-width," (i.e. displays a sidebar) then it runs
// through all possible pages to determine the correct layout for that template.
//

if ( ! function_exists( 'x_get_content_layout' ) ) :
  function x_get_content_layout() {

    $content_layout = x_get_option( 'x_layout_content' );

    if ( $content_layout != 'full-width' ) {
      if ( is_home() ) {
        $opt    = x_get_option( 'x_blog_layout' );
        $layout = ( $opt == 'sidebar' ) ? $content_layout : $opt;
      } elseif ( is_singular( 'post' ) ) {
        $meta   = get_post_meta( get_the_ID(), '_x_post_layout', true );
        $layout = ( $meta == 'on' ) ? 'full-width' : $content_layout;
      } elseif ( x_is_portfolio_item() ) {
        $layout = 'full-width';
      } elseif ( x_is_portfolio() ) {
        $meta   = get_post_meta( get_the_ID(), '_x_portfolio_layout', true );
        $layout = ( $meta == 'sidebar' ) ? $content_layout : $meta;
      } elseif ( is_page_template( 'template-layout-content-sidebar.php' ) ) {
        $layout = 'content-sidebar';
      } elseif ( is_page_template( 'template-layout-sidebar-content.php' ) ) {
        $layout = 'sidebar-content';
      } elseif ( is_page_template( 'template-layout-full-width.php' ) ) {
        $layout = 'full-width';
      } elseif ( is_archive() ) {
        if ( x_is_shop() || x_is_product_category() || x_is_product_tag() ) {
          $opt    = x_get_option( 'x_woocommerce_shop_layout_content' );
          $layout = ( $opt == 'sidebar' ) ? $content_layout : $opt;
        } else {
          $opt    = x_get_option( 'x_archive_layout' );
          $layout = ( $opt == 'sidebar' ) ? $content_layout : $opt;
        }
      } elseif ( x_is_product() ) {
        $layout = 'full-width';
      } elseif ( x_is_bbpress() ) {
        $opt    = x_get_option( 'x_bbpress_layout_content' );
        $layout = ( $opt == 'sidebar' ) ? $content_layout : $opt;
      } elseif ( x_is_buddypress() ) {
        $opt    = x_get_option( 'x_buddypress_layout_content' );
        $layout = ( $opt == 'sidebar' ) ? $content_layout : $opt;
      } elseif ( is_404() ) {
        $layout = 'full-width';
      } else {
        $layout = $content_layout;
      }
    } else {
      $layout = $content_layout;
    }

    return $layout;

  }
endif;



// Alternate Title
// =============================================================================

if ( ! function_exists( 'x_the_alternate_title' ) ) :
  function x_the_alternate_title() {

    $meta  = get_post_meta( get_the_ID(), '_x_entry_alternate_index_title', true );
    $title = ( $meta != '' ) ? $meta : get_the_title();

    echo $title;

  }
endif;



// Link Pages
// =============================================================================

if ( ! function_exists( 'x_link_pages' ) ) :
  function x_link_pages() {

    wp_link_pages( array(
      'before' => '<div class="page-links">' . __( 'Pages:', '__x__' ),
      'after'  => '</div>'
    ) );

  }
endif;



// Entry Navigation
// =============================================================================

if ( ! function_exists( 'x_entry_navigation' ) ) :
  function x_entry_navigation() {

  $stack = x_get_stack();

  if ( $stack == 'ethos' ) {
    $left_icon  = x_icon_get("f053", "x-icon-chevron-left");
    $right_icon = x_icon_get("f054", "x-icon-chevron-right");
  } else {
    $left_icon  = x_icon_get("f060", "x-icon-arrow-left");
    $right_icon = x_icon_get("f061", "x-icon-arrow-right");
  }

  $is_ltr    = ! is_rtl();
  $prev_post = get_adjacent_post( false, '', false );
  $next_post = get_adjacent_post( false, '', true );
  $prev_icon = ( $is_ltr ) ? $left_icon : $right_icon;
  $next_icon = ( $is_ltr ) ? $right_icon : $left_icon;

  ?>

  <div class="x-nav-articles">

    <?php if ( $prev_post ) : ?>
      <a href="<?php echo get_permalink( $prev_post ); ?>" title="<?php __( 'Previous Post', '__x__' ); ?>" class="prev">
        <?php echo $prev_icon; ?>
      </a>
    <?php endif; ?>

    <?php if ( $next_post ) : ?>
      <a href="<?php echo get_permalink( $next_post ); ?>" title="<?php __( 'Next Post', '__x__' ); ?>" class="next">
        <?php echo $next_icon; ?>
      </a>
    <?php endif; ?>

  </div>

  <?php

  }
endif;



// Does Not Need Entry Meta
// =============================================================================

//
// Returns true if a condition is met where displaying the entry meta data is
// not desirable.
//

if ( ! function_exists( 'x_does_not_need_entry_meta' ) ) :
  function x_does_not_need_entry_meta() {

    $post_type           = get_post_type();
    $page_condition      = $post_type == 'page';
    $post_condition      = $post_type == 'post' && x_get_option( 'x_blog_enable_post_meta' ) == '';
    $portfolio_condition = $post_type == 'x-portfolio' && x_get_option( 'x_portfolio_enable_post_meta' ) == '';

    if ( $page_condition || $post_condition || $portfolio_condition ) {
      return true;
    } else {
      return false;
    }

  }
endif;




// Legacy Header Widget Areas
// =============================================================================

if ( ! function_exists( 'x_legacy_header_widget_areas' ) ) :
  function x_legacy_header_widget_areas() {

    $n = x_get_option( 'x_header_widget_areas' );

    if (
      x_is_custom_stack()
      || ! apply_filters( 'x_legacy_cranium_headers', true ) || $n == 0 || x_is_blank( 3 ) || x_is_blank( 6 ) || x_is_blank( 7 ) || x_is_blank( 8 )
    ) {
      return;
    }

    ?>

    <div id="x-widgetbar" class="x-widgetbar x-collapsed" data-x-toggleable="x-widgetbar" data-x-toggle-collapse="1" aria-hidden="true" aria-labelledby="x-btn-widgetbar">
      <div class="x-widgetbar-inner">
        <div class="x-container max width">

          <?php

          $i = 0; while ( $i < $n ) : $i++;

            $last = ( $i == $n ) ? ' last' : '';

            echo '<div class="x-column x-md x-1-' . $n . $last . '">';
              dynamic_sidebar( 'header-' . $i );
            echo '</div>';

          endwhile;

          ?>

        </div>
      </div>
    </div>

    <a href="#" id="x-btn-widgetbar" class="x-btn-widgetbar collapsed" data-x-toggle="collapse-b" data-x-toggleable="x-widgetbar" aria-expanded="false" aria-controls="x-widgetbar" role="button">
      <?php echo x_icon_get("f055", "x-icon-plus-circle", '<span class="visually-hidden">' . __( 'Toggle the Widgetbar', '__x__' ) . '</span>'); ?>
    </a>

    <?php

  }
  add_action( 'x_after_site_end', 'x_legacy_header_widget_areas' );
endif;



// Legacy Slider Below with New Masthead
// =============================================================================

if ( ! function_exists( 'x_legacy_slider_above_with_new_masthead' ) ) :
  function x_legacy_slider_above_with_new_masthead() {
    x_get_view( 'global', '_slider-above' );
  }
  add_action( 'x_before_masthead_begin', 'x_legacy_slider_above_with_new_masthead' );
endif;



// Legacy Slider Above with New Masthead
// =============================================================================

if ( ! function_exists( 'x_legacy_slider_below_with_new_masthead' ) ) :
  function x_legacy_slider_below_with_new_masthead() {
    x_get_view( 'global', '_slider-below' );
  }
  add_action( 'x_after_masthead_end', 'x_legacy_slider_below_with_new_masthead' );
endif;


// This is used to ensure _x_video_embed and other X meta keys are decoded and can be output as HTML
add_action( 'cs_format_dynamic_content_post_meta', function( $result, $key ) {
  if ( is_string( $result) && strpos( $key, '_x_') === 0 ) {
    return stripslashes( wp_specialchars_decode( $result, ENT_QUOTES ) );
  }
  return $result;
}, 10, 2);
