<?php

// =============================================================================
// VIEWS/GLOBAL/_SLIDER-ABOVE.PHP
// -----------------------------------------------------------------------------
// Slider output above all page content.
// =============================================================================

if ( class_exists( 'RevSlider' ) || class_exists( 'LS_Sliders' ) ) :

  if ( !is_search() ):

    $id            = x_get_the_ID();
    $slider_active = get_post_meta( $id, '_x_slider_above', true );
    $slider        = ( $slider_active == '' ) ? 'Deactivated' : $slider_active;

    if ( $slider != 'Deactivated' ) :

      $bg_video           = get_post_meta( $id, '_x_slider_above_bg_video', true );
      $bg_video_poster    = get_post_meta( $id, '_x_slider_above_bg_video_poster', true );
      $anchor             = get_post_meta( $id, '_x_slider_above_scroll_bottom_anchor_enable', true );
      $anchor_alignment   = get_post_meta( $id, '_x_slider_above_scroll_bottom_anchor_alignment', true );
      $anchor_color       = get_post_meta( $id, '_x_slider_above_scroll_bottom_anchor_color', true );
      $anchor_color_hover = get_post_meta( $id, '_x_slider_above_scroll_bottom_anchor_color_hover', true );

      ?>

      <div class="x-slider-container above<?php if ( $bg_video != '' ) { echo ' bg-video'; } ?>">

        <?php if ( $bg_video != '' ) : echo function_exists( 'cs_bg_video' ) ? cs_bg_video( $bg_video, $bg_video_poster ) : ''; endif; ?>

        <?php if ( $anchor == 'on' ) :

        $anchor_content = apply_filters( 'x_slider_scroll_top_anchor_content', x_icon_get('f107', "x-icon-angle-down"), 'above' );

        ?>

          <style scoped>
            .x-slider-scroll-bottom.above       { color: <?php echo $anchor_color; ?>;       }
            .x-slider-scroll-bottom.above:hover { color: <?php echo $anchor_color_hover; ?>; }
          </style>

          <a href="#" class="x-slider-scroll-bottom above <?php echo $anchor_alignment; ?>">
            <?php echo $anchor_content; ?>
          </a>

        <?php endif; ?>

        <?php echo do_shortcode( x_get_slider_shortcode( $slider ) ); ?>

      </div>

    <?php endif;

  endif;

endif;
