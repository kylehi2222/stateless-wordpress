<?php

/**
 * Filter to add typography controls
 */
add_filter("cs_theme_options_typography_group", function($results, $args = []) {
  $choices_list_font_families = 'list:fonts';
  $choices_list_font_weights  = 'list:font-weights';

  // Dup
  $options_letter_spacing_compressed = [
    'available_units' => [ 'em' ],
    'fallback_value'  => '-0.015em',
    'ranges'          => [
      'em' => [ 'min' => -0.05, 'max' => 0.25, 'step' => 0.005 ],
    ],
  ];

  $condition_root_font_size_mode_stepped          = [ 'x_root_font_size_mode' => 'stepped' ];
  $condition_root_font_size_mode_scaling          = [ 'x_root_font_size_mode' => 'scaling' ];
  $condition_google_fonts_subsets_enabled         = [ 'x_google_fonts_subsets' => true ];

  $condition_font_manager_disabled                = [ 'x_enable_font_manager' => false ];
  $condition_font_manager_enabled                 = [ 'x_enable_font_manager' => true ];

  $choices_root_font_size_mode = [
    [ 'value' => 'stepped', 'label' => __( 'Stepped', '__x__' ) ],
    [ 'value' => 'scaling', 'label' => __( 'Scaling', '__x__' ) ],
  ];

  $choices_px_em = [
    [ 'value' => 'px', 'label' => __( 'px', '__x__' ) ],
    [ 'value' => 'em', 'label' => __( 'em', '__x__' ) ],
  ];


  $options_content_font_size = [
    'available_units' => [ 'rem' ],
    'fallback_value'  => '1rem',
    'ranges'          => [
      'rem' => [ 'min' => 0.5, 'max' => 2, 'step' => 0.005 ],
    ],
  ];


  return [
    'type'  => 'group-sub-module',
    'label' => __( 'Typography', '__x__' ),
    'options' => [ 'tag' => 'typography', 'name' => 'x-theme-options:typography' ],
    'controls' => [
      [
        'key'         => 'x_enable_font_manager',
        'type'        => 'toggle',
        'label'       => __( 'Enable Font Manager', '__x__' ),
        // 'description' => __( 'Here you will find global typography options for your body copy and headings, while more specific typography options for elements like your navbar are found grouped with that element to make customization more streamlined. If you are using Google Fonts, you can also enable custom subsets here for expanded character sets.', '__x__' ),
        'description' => __( 'Assign your own font selections instead of directly using System or Google Fonts.', '__x__' ),
        // 'options'     => cs_recall( 'options_group_toggle_off_on_bool' ),
      ], [
        'type'        => 'group',
        'label'       => __( 'Root Font Size', '__x__' ),
        'description' => __( 'Select the method for outputting your site\'s root font size, then adjust the settings to suit your design. "Stepped" mode allows you to set a font size at each of your site\'s breakpoints, whereas "Scaling" will dynamically scale between a range of minimum and maximum font sizes and breakpoints that you specify.', '__x__' ),
        'controls'    => [
          [
            'key'        => 'x_root_font_size_mode',
            'type'    => 'choose',
            'label'   => __( 'Mode', '__x__' ),
            'options' => [ 'choices' => $choices_root_font_size_mode ],
          ],
          [
            'key'        => 'x_root_font_size_stepped_unit',
            'type'       => 'choose',
            'label'      => __( 'Unit', '__x__' ),
            'options'    => [ 'choices' => $choices_px_em ],
            'conditions' => [ $condition_root_font_size_mode_stepped ],
          ],
          [
            'key'        => 'x_root_font_size_stepped_xs',
            'type'       => 'text',
            'label'      => __( 'XS Breakpoint', '__x__' ),
            'conditions' => [ $condition_root_font_size_mode_stepped ],
          ],
          [
            'key'        => 'x_root_font_size_stepped_sm',
            'type'       => 'text',
            'label'      => __( 'SM Breakpoint', '__x__' ),
            'conditions' => [ $condition_root_font_size_mode_stepped ],
          ],
          [
            'key'        => 'x_root_font_size_stepped_md',
            'type'       => 'text',
            'label'      => __( 'MD Breakpoint', '__x__' ),
            'conditions' => [ $condition_root_font_size_mode_stepped ],
          ],
          [
            'key'        => 'x_root_font_size_stepped_lg',
            'type'       => 'text',
            'label'      => __( 'LG Breakpoint', '__x__' ),
            'conditions' => [ $condition_root_font_size_mode_stepped ],
          ],
          [
            'key'        => 'x_root_font_size_stepped_xl',
            'type'       => 'text',
            'label'      => __( 'XL Breakpoint', '__x__' ),
            'conditions' => [ $condition_root_font_size_mode_stepped ],
          ],
          [
            'key'        => 'x_root_font_size_scaling_unit',
            'type'       => 'choose',
            'label'      => __( 'Font Size Unit', '__x__' ),
            'options'    => [ 'choices' => $choices_px_em ],
            'conditions' => [ $condition_root_font_size_mode_scaling ],
          ],
          [
            'key'        => 'x_root_font_size_scaling_min',
            'type'       => 'text',
            'label'      => __( 'Minimum Font Size', '__x__' ),
            'conditions' => [ $condition_root_font_size_mode_scaling ],
          ],
          [
            'key'        => 'x_root_font_size_scaling_max',
            'type'       => 'text',
            'label'      => __( 'Maximum Font Size', '__x__' ),
            'conditions' => [ $condition_root_font_size_mode_scaling ],
          ],
          [
            'key'        => 'x_root_font_size_scaling_lower_limit',
            'type'       => 'text',
            'label'      => __( 'Lower Limit', '__x__' ),
            'conditions' => [ $condition_root_font_size_mode_scaling ],
          ],
          [
            'key'        => 'x_root_font_size_scaling_upper_limit',
            'type'       => 'text',
            'label'      => __( 'Upper Limit', '__x__' ),
            'conditions' => [ $condition_root_font_size_mode_scaling ],
          ],
        ],
      ], [
        'type'        => 'group',
        'label'       => __( 'Body and Content', '__x__' ),
        'description' => __( '"Content Font Size" will affect the sizing of all copy inside a post or page content area. It uses rems, which are a unit relative to your root font size. For example, if your root font size is 10px and you want your content font size to be 12px, you would enter "1.2" as a value. Headings are set with percentages and sized proportionally to these settings.', '__x__' ),
        'controls'    => [
          [
            'key'        => 'x_body_font_family_selection',
            'type'       => 'font-family',
            'label'      => __( 'Font Family', '__x__' ),
            'conditions' => [ $condition_font_manager_enabled ],
          ],
          [
            'key'        => 'x_body_font_family',
            'type'       => 'select',
            'label'      => __( 'Font Family', '__x__' ),
            'options'    => [ 'choices' => $choices_list_font_families ],
            'conditions' => [ $condition_font_manager_disabled ],
          ],
          [
            'keys' => [
              'value'       => 'x_body_font_weight_selection',
              'font_family' => 'x_body_font_family_selection'
            ],
            'type'       => 'font-weight',
            'label'      => __( 'Font Weight', '__x__' ),
            'conditions' => [ $condition_font_manager_enabled ],
          ],
          [
            'key'        => 'x_body_font_weight',
            'type'       => 'select',
            'label'      => __( 'Font Weight', '__x__' ),
            'conditions' => [ $condition_font_manager_disabled ],
            'options'    => [
              'filter'  => [ 'key' => 'choices', 'method' => 'font-weights', 'source' => 'x_body_font_family' ],
              'choices' => $choices_list_font_weights,
            ],
          ],
          [
            'key'        => 'x_body_font_italic',
            'type'       => 'toggle',
            'label'      => __( 'Italic', '__x__' ),
            'options'    => cs_recall( 'options_group_toggle_off_on_bool_string' ),
            'conditions' => [ $condition_font_manager_enabled ],
          ],
          [
            'key'   => 'x_body_font_color',
            'type'  => 'color',
            'label' => __( 'Color', '__x__' ),
          ],
          [
            'key'     => 'x_content_font_size_rem',
            'type'    => 'unit-slider',
            'label'   => __( 'Content Font Size', '__x__' ),
            'options' => $options_content_font_size,
          ],
        ],
      ], [
        'type'        => 'group',
        'label'       => __( 'Headings', '__x__' ),
        'description' => __( 'The letter spacing controls for each heading level will only affect that heading if it does not have a "looks like" class or if the "looks like" class matches that level. For example, if you have an &lt;h1&gt; with no modifier class, the &lt;h1&gt; slider will affect that heading. However, if your &lt;h1&gt; has an .h2 modifier class, then the &lt;h2&gt; slider will take over as it is supposed to appear as an &lt;h2&gt;.', '__x__' ),
        'controls'    => [
          [
            'key'        => 'x_headings_font_family_selection',
            'type'       => 'font-family',
            'label'      => __( 'Font Family', '__x__' ),
            'conditions' => [ $condition_font_manager_enabled ],
          ],
          [
            'key'        => 'x_headings_font_family',
            'type'       => 'select',
            'label'      => __( 'Font Family', '__x__' ),
            'conditions' => [ $condition_font_manager_disabled ],
            'options'    => [ 'choices' => $choices_list_font_families ],
          ],
          [
            'keys' => [
              'value'       => 'x_headings_font_weight_selection',
              'font_family' => 'x_headings_font_family_selection'
            ],
            'type'       => 'font-weight',
            'label'      => __( 'Font Weight', '__x__' ),
            'conditions' => [ $condition_font_manager_enabled ],
          ],
          [
            'key'        => 'x_headings_font_weight',
            'type'       => 'select',
            'label'      => __( 'Font Weight', '__x__' ),
            'conditions' => [ $condition_font_manager_disabled ],
            'options'    => [
              'filter'  => [ 'key' => 'choices', 'method' => 'font-weights', 'source' => 'x_headings_font_family' ],
              'choices' => $choices_list_font_weights,
            ],
          ],
          [
            'key'     => 'x_h1_letter_spacing',
            'type'    => 'unit-slider',
            'label'   => __( 'h1 Letter Spacing', '__x__' ),
            'options' => $options_letter_spacing_compressed,
          ],
          [
            'key'     => 'x_h2_letter_spacing',
            'type'    => 'unit-slider',
            'label'   => __( 'h2 Letter Spacing', '__x__' ),
            'options' => $options_letter_spacing_compressed,
          ],
          [
            'key'     => 'x_h3_letter_spacing',
            'type'    => 'unit-slider',
            'label'   => __( 'h3 Letter Spacing', '__x__' ),
            'options' => $options_letter_spacing_compressed,
          ],
          [
            'key'     => 'x_h4_letter_spacing',
            'type'    => 'unit-slider',
            'label'   => __( 'h4 Letter Spacing', '__x__' ),
            'options' => $options_letter_spacing_compressed,
          ],
          [
            'key'     => 'x_h5_letter_spacing',
            'type'    => 'unit-slider',
            'label'   => __( 'h5 Letter Spacing', '__x__' ),
            'options' => $options_letter_spacing_compressed,
          ],
          [
            'key'     => 'x_h6_letter_spacing',
            'type'    => 'unit-slider',
            'label'   => __( 'h6 Letter Spacing', '__x__' ),
            'options' => $options_letter_spacing_compressed,
          ],
          [
            'key'        => 'x_headings_font_italic',
            'type'       => 'toggle',
            'label'      => __( 'Italic', '__x__' ),
            'conditions' => [ $condition_font_manager_enabled ],
          ],
          [
            'key'     => 'x_headings_uppercase_enable',
            'type'    => 'toggle',
            'label'   => __( 'Uppercase', '__x__' ),
            'options' => cs_recall( 'options_group_toggle_off_on_bool_string' ),
          ],
          [
            'key'   => 'x_headings_font_color',
            'type'  => 'color',
            'label' => __( 'Color', '__x__' ),
          ],
          [
            'key'     => 'x_headings_widget_icons_enable',
            'type'    => 'toggle',
            'label'   => __( 'Widget Icons', '__x__' ),
            'options' => cs_recall( 'options_group_toggle_off_on_bool_string' ),
          ],
        ],
      ], [
        'type'        => 'group',
        'label'       => __( 'Site Links', '__x__' ),
        'description' => __( 'Site link colors are also used as accents for various elements throughout your site, so make sure to select something you really enjoy and keep an eye out for how it affects your design.', '__x__' ),
        'controls'    => array_merge(
          [
            [
              'keys' => [
                'value' => 'x_site_link_color',
                'alt'   => 'x_site_link_color_hover',
              ],
              'type'    => 'color',
              'label'   => __( 'Color', '__x__' ),
              'options' => cs_recall( 'options_swatch_base_interaction_labels' ),
            ],
          ],
          !empty($args['no_oembed'])
            ? []
            : [
                [
                  'key'     => 'x_site_link_oembed',
                  'type'    => 'toggle',
                  'label'   => __( 'Use OEmbed', '__x__' ),
                ],
                [
                  'key'     => 'x_site_link_oembed_own_site',
                  'type'    => 'toggle',
                  'label'   => __( 'OEmbed for Internal Links', '__x__' ),
                  'conditions' => [
                    [
                      'x_site_link_oembed' => true,
                    ],
                  ],
                ]
              ]
        )
      ], [
        'key'        => 'x_google_fonts_subsets',
        'type'       => 'group',
        'label'      => __( 'Google Subsets', '__x__' ),
        'conditions' => [ $condition_font_manager_disabled ],
        'options'    => cs_recall( 'options_group_toggle_off_on_bool' ),
        'controls'   => [
          [
            'key'        => 'x_google_fonts_subset_cyrillic',
            'type'       => 'toggle',
            'label'      => __( 'Cyrillic', '__x__' ),
            'options'    => cs_recall( 'options_group_toggle_off_on_bool_string' ),
            'conditions' => [ $condition_google_fonts_subsets_enabled ],
          ],
          [
            'key'        => 'x_google_fonts_subset_greek',
            'type'       => 'toggle',
            'label'      => __( 'Greek', '__x__' ),
            'options'    => cs_recall( 'options_group_toggle_off_on_bool_string' ),
            'conditions' => [ $condition_google_fonts_subsets_enabled ],
          ],
          [
            'key'        => 'x_google_fonts_subset_vietnamese',
            'type'       => 'toggle',
            'label'      => __( 'Vietnamese', '__x__' ),
            'options'    => cs_recall( 'options_group_toggle_off_on_bool_string' ),
            'conditions' => [ $condition_google_fonts_subsets_enabled ],
          ],
        ],
      ]
    ]
  ];
}, 10, 2);
