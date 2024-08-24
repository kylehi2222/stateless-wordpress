<?php

/**
 * Theme option controls
 */

add_filter("cs_theme_options_scroll_top_group", function() {
  $choices_left_right_positioning = [
    [ 'value' => 'left',  'label' => __( 'Left', '__x__' )  ],
    [ 'value' => 'right', 'label' => __( 'Right', '__x__' ) ],
  ];

  $options_when_to_show_scroll_top_anchor = [
    'available_units' => [ '%' ],
    'fallback_value'  => '75%',
    'ranges'          => [
      '%' => [ 'min' => 0, 'max' => 100, 'step' => 5 ],
    ],
  ];

  $condition_footer_scroll_top_enable = [ 'x_footer_scroll_top_display' => true ];

  return [
    'key'         => 'x_footer_scroll_top_display',
    'type'        => 'group',
    'label'      => __( 'Scroll Top Anchor', '__x__' ),
    'options'     => cs_recall( 'options_group_toggle_off_on_bool_string' ),
    // 'description' => __( 'Activating the scroll top anchor will output a link that appears in the bottom corner of your site for users to click on that will return them to the top of your website. Once activated, set the value (%) for how far down the page your users will need to scroll for it to appear. For example, if you want the scroll top anchor to appear once your users have scrolled halfway down your page, you would enter "50" into the field.', '__x__' ),
    'description' => __( 'Once activated, set the value (%) for how far down the page your users will need to scroll for it to appear. For example, if you want the scroll top anchor to appear once your users have scrolled halfway down your page, you would enter "50" into the field.', '__x__' ),
    'controls'    => [
      [
        'key'        => 'x_footer_scroll_top_position',
        'type'       => 'choose',
        'label'      => __( 'Position', '__x__' ),
        'options'    => [ 'choices' => $choices_left_right_positioning ],
        'conditions' => [ $condition_footer_scroll_top_enable ],
      ],
      [
        'key'        => 'x_footer_scroll_top_display_unit',
        'type'       => 'unit-slider',
        'label'      => __( 'When to Show', '__x__' ),
        'options'    => $options_when_to_show_scroll_top_anchor,
        'conditions' => [ $condition_footer_scroll_top_enable ],
      ],
    ],
  ];
});

/**
 * Script setup
 */
add_action( 'wp_enqueue_scripts', function() {
  if(
    !get_option( 'x_footer_scroll_top_display', false )
    || !cs_stack_is_custom()
  ) {
    return;
  }

  //Register scroll top
  $url = cornerstone("Styling")->getCSSAsset('assets/css/site/scroll-top');
  wp_register_style( 'cs-scroll-top', $url['url'], [], $url['version'] );
  wp_enqueue_style( 'cs-scroll-top' );
});

/**
 * Output for after_end x-root
 */
add_action( 'x_after_site_end', function() {
  if ( get_option( 'x_footer_scroll_top_display', false ) == '1' ) : ?>

    <a class="x-scroll-top <?php echo x_get_option( 'x_footer_scroll_top_position' ); ?> fade" title="<?php esc_attr_e( 'Back to Top', '__x__' ); ?>" data-rvt-scroll-top>
      <?php
        $fa_solid_enable = (bool) x_get_option( 'x_font_awesome_solid_enable' );
        $fa_regular_enable = (bool) x_get_option( 'x_font_awesome_regular_enable' );
        $fa_light_enable = (bool) x_get_option( 'x_font_awesome_light_enable' );

        if ( $fa_solid_enable || $fa_regular_enable || $fa_light_enable ){
          // light
          if ( $fa_light_enable ){
            $data_x_icon = 'data-x-icon-l';
          }

          // regular
          if ( $fa_regular_enable ){
            $data_x_icon = 'data-x-icon-o';
          }

          // solid
          if ( $fa_solid_enable ){
            $data_x_icon = 'data-x-icon-s';
          }
        }else{
          // default
          $data_x_icon = 'data-x-icon-l';
        }

        // Output icon
        echo cs_fa_icon_tag_from_unicode('f106', 'x-icon-angle-up');
      ?>
    </a>

  <?php endif;

});
