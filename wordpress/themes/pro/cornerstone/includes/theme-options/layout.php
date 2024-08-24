<?php

/**
 * Filter to add typography controls
 */
add_filter("cs_theme_options_layout_group", function() {

  $condition_stack_is_icon                        = [ 'x_stack' => 'icon' ];
  $condition_stack_is_not_icon                    = [ 'option' => 'x_stack', 'value' => 'icon', 'op' => '!=' ];

  $condition_layout_content_not_full_width        = [ 'option' => 'x_layout_content', 'value' => 'full-width', 'op' => '!=' ];

  $choices_fullwidth_boxed = [
    [ 'value' => 'full-width', 'label' => __( 'Fullwidth', '__x__' ) ],
    [ 'value' => 'boxed',      'label' => __( 'Boxed', '__x__' )     ],
  ];

  $choices_layout_content = [
    [ 'value' => 'content-sidebar', 'label' => __( 'Content / Sidebar', '__x__' ) ],
    [ 'value' => 'sidebar-content', 'label' => __( 'Sidebar / Content', '__x__' ) ],
    [ 'value' => 'full-width',      'label' => __( 'Fullwidth', '__x__' )         ],
  ];

  $options_site_width = [
    'available_units' => [ '%' ],
    'valid_keywords'  => [ 'calc' ],
    'fallback_value'  => '88%',
    'ranges'          => [
      '%' => [ 'min' => 70, 'max' => 100, 'step' => 1 ],
    ],
  ];

  $options_site_max_width = [
    'available_units' => [ 'px' ],
    // 'available_units' => [ 'px', 'em', 'rem' ],
    'fallback_value'  => '1200px',
    'ranges'          => [
      'px'  => [ 'min' => 800, 'max' => 1400, 'step' => 10  ],
      // 'em'  => [ 'min' => 50,  'max' => 88,   'step' => 0.5 ],
      // 'rem' => [ 'min' => 50,  'max' => 88,   'step' => 0.5 ],
    ],
  ];

  $options_content_width = [
    'available_units' => [ '%' ],
    'fallback_value'  => '88%',
    'ranges'          => [
      '%' => [ 'min' => 70, 'max' => 100, 'step' => 1 ],
    ],
  ];

  $options_sidebar_width = [
    'available_units' => [ 'px' ],
    'fallback_value'  => '250px',
    'ranges'          => [
      'px' => [ 'min' => 200, 'max' => 300, 'step' => 5 ],
    ],
  ];

  $options_bg_image_fade = [
    'unit_mode'       => 'time',
    'available_units' => [ 'ms' ],
    'fallback_value'  => '750ms',
    'ranges'          => [
      'ms' => [ 'min' => 0, 'max' => 5000, 'step' => 50 ],
    ],
  ];



  return [
    'type'        => 'group',
    // 'label'       => $labels['setup'],
    'group'       => 'x:layout-and-design',
    // 'group'       => 'x-layout-and-design:setup',
    // 'description' => __( 'Select your site\'s global layout options here. "Site Width" is the percentage of the screen your site should take up while you can think of "Site Max Width" as an upper limit that your site will never be wider than. "Content Layout" has to do with your site\'s global setup of having a sidebar or not. The "Background Pattern" setting will override the "Background Color" unless the image used is transparent, and the "Background Image" option will take precedence over both. The "Background Image Fade (ms)" option allows you to set a time in milliseconds for your image to fade in. To disable this feature, set the value to "0."', '__x__' ),
    'description' => __( 'Specify the values for various fundamental features of your site such as the primary width, max width, et cetera.', '__x__' ),
    'controls'    => [
      [
        'key'     => 'x_layout_site',
        'type'    => 'choose',
        'label'   => __( 'Site Layout', '__x__' ),
        'options' => [ 'choices' => $choices_fullwidth_boxed ],
      ],
      [
        'key'     => 'x_layout_content',
        'type'    => 'select',
        'label'   => __( 'Content Layout', '__x__' ),
        'options' => [ 'choices' => $choices_layout_content ],
      ],
      [
        'key'     => 'x_layout_site_width',
        'type'    => 'unit-slider',
        'label'   => __( 'Site Width', '__x__' ),
        'options' => $options_site_width,
      ],
      [
        'key'     => 'x_layout_site_max_width',
        'type'    => 'unit-slider',
        'label'   => __( 'Site Max Width', '__x__' ),
        'options' => $options_site_max_width,
      ],      [
        'key'        => 'x_layout_content_width',
        'type'       => 'unit-slider',
        'label'      => __( 'Content Width', '__x__' ),
        'options'    => $options_content_width,
        'conditions' => [ $condition_stack_is_not_icon, $condition_layout_content_not_full_width ],
      ],
      [
        'key'        => 'x_layout_sidebar_width',
        'type'       => 'unit-slider',
        'label'      => __( 'Sidebar Width', '__x__' ),
        'options'    => $options_sidebar_width,
        'conditions' => [ $condition_stack_is_icon, $condition_layout_content_not_full_width ],
      ],
      [
        'key'   => 'x_design_bg_color',
        'type'  => 'color',
        'label' => __( 'Background Color', '__x__' ),
      ],
      [
        'key'     => 'x_design_bg_image_pattern',
        'type'    => 'image',
        'label'   => __( 'Background Pattern', '__x__' ),
        'options' => [ 'pattern' => true ],
      ],
      [
        'key'   => 'x_design_bg_image_full',
        'type'  => 'image',
        'label' => __( 'Background Image', '__x__' ),
      ],
      [
        'key'     => 'x_design_bg_image_full_fade',
        'type'    => 'unit-slider',
        'label'   => __( 'Background Image Fade', '__x__' ),
        'options' => $options_bg_image_fade,
      ],
    ]
  ];
});
