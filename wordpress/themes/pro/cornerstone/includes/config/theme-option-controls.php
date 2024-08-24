<?php

$condition_text_decoration_is_line_through_or_underline = [ 'key' => 'a_text_decoration_line', 'op' => 'IN', 'value' => ['line-through', 'underline'] ];
$condition_text_decoration_is_underline                 = [ 'key' => 'a_text_decoration_line', 'op' => 'IN', 'value' => ['underline']                 ];


// Output
// =============================================================================

return [
  [
    'type'  => 'group-module',
    'label' => cs_recall( 'label_options' ),
    'controls' => [


      // Background
      // ----------

      [
        'type'  => 'group-sub-module',
        'label' => cs_recall( 'label_background' ),
        'options' => [ 'tag' => 'theme_options', 'name' => 'theme_options:bg' ],
        'controls' => [
          [
            'type'     => 'group',
            'controls' => [
              cs_recall( 'control_mixin_color_solo', [ 'key' => 'root_background_color', 'label' => cs_recall( 'label_color' ) ] ),
            ],
          ]
        ]
      ],

      // Container
      // ---------

      [
        'type'  => 'group-sub-module',
        'label' => cs_recall( 'label_container' ),
        'options' => [ 'tag' => 'theme_options', 'name' => 'theme_options:global_container' ],
        'controls' => [
          [
            'type'     => 'group',
            'controls' => [
              cs_recall( 'control_mixin_width',     [ 'key' => 'container_width'     ] ),
              cs_recall( 'control_mixin_max_width', [ 'key' => 'container_max_width' ] ),
            ],
          ]
        ]
      ],


      // Anchors
      // -------

      [
        'type'  => 'group-sub-module',
        'label' => cs_recall( 'label_anchors' ),
        'options' => [ 'tag' => 'theme_options', 'name' => 'theme_options:a' ],
        'controls' => [
          [
            'type'     => 'group',
            'controls' => [
              cs_recall( 'control_mixin_color_int', [ 'keys' => [ 'value' => 'a_color', 'alt' => 'a_color_alt' ], 'label' => cs_recall( 'label_color' ) ] ),
              [
                'type'     => 'sub-group',
                'label'    => cs_recall( 'label_decoration' ),
                'controls' => [
                  [
                    'key'     => 'a_text_decoration_line',
                    'type'    => 'choose',
                    'label'   => cs_recall( 'label_line' ),
                    'options' => [
                      'off_value' => 'none',
                      'choices'   => [
                        [ 'value' => 'underline',    'icon' => 'ui:underline'     ],
                        [ 'value' => 'line-through', 'icon' => 'ui:strikethrough' ],
                      ],
                    ],
                  ],
                  [
                    'key'       => 'a_text_decoration_style',
                    'type'      => 'select',
                    'label'     => cs_recall( 'label_style' ),
                    'options'   => cs_recall( 'options_choices_text_decoration_styles' ),
                    'condition' => $condition_text_decoration_is_line_through_or_underline,
                  ],
                  cs_recall( 'control_mixin_gap',                       [ 'key' => 'a_text_underline_offset',                                                         'label' => cs_recall( 'label_offset' ),    'condition' => $condition_text_decoration_is_underline                 ] ),
                  cs_recall( 'control_mixin_text_decoration_thickness', [ 'key' => 'a_text_decoration_thickness',                                                     'label' => cs_recall( 'label_thickness' ), 'condition' => $condition_text_decoration_is_line_through_or_underline ] ),
                  cs_recall( 'control_mixin_color_int',                 [ 'keys' => [ 'value' => 'a_text_decoration_color', 'alt' => 'a_text_decoration_color_alt' ], 'label' => cs_recall( 'label_color' ),     'condition' => $condition_text_decoration_is_line_through_or_underline ] ),
                ],
              ],
            ],
          ],
        ]
      ],



      // Typography
      // ----------

      [
        'type'  => 'group-sub-module',
        'label' => cs_recall( 'label_typography' ),
        'options' => [ 'tag' => 'theme_options', 'name' => 'theme_options:typography' ],
        'controls' => [
          [
            'type'     => 'group',
            'controls' => [
              [
                'type'     => 'sub-group',
                'label'    => cs_recall( 'label_root' ),
                'controls' => array_merge(
                  csThemeOptionsFontFamilyAndWeight( 'root' ),
                  [
                    cs_recall( 'control_mixin_font_size',      [ 'key' => 'root_font_size',      'label' => cs_recall( 'label_size' )    ] ),
                    cs_recall( 'control_mixin_letter_spacing', [ 'key' => 'root_letter_spacing', 'label' => cs_recall( 'label_spacing' ) ] ),
                    cs_recall( 'control_mixin_line_height',    [ 'key' => 'root_line_height',    'label' => cs_recall( 'label_height' )  ] ),
                    cs_recall( 'control_mixin_color_solo',     [ 'key' => 'root_color',          'label' => cs_recall( 'label_color' )   ] ),
                  ]
                ),
              ],
              csThemeOptionsTextFormat( 'h1' ),
              csThemeOptionsTextFormat( 'h2' ),
              csThemeOptionsTextFormat( 'h3' ),
              csThemeOptionsTextFormat( 'h4' ),
              csThemeOptionsTextFormat( 'h5' ),
              csThemeOptionsTextFormat( 'h6' ),
              csThemeOptionsTextFormat( 'h6' ),
              csThemeOptionsTextFormat( 'label' ),
            ],
          ],
        ]
      ],



      // Content Spacing
      // ---------------

      [
        'type'  => 'group-sub-module',
        'label' => cs_recall( 'label_content_spacing' ),
        'options' => [ 'tag' => 'theme_options', 'name' => 'theme_options:content_spacing' ],
        'controls' => [
          [
            'type'     => 'group',
            'controls' => [
              cs_recall( 'control_mixin_gap', [ 'key' => 'content_copy_spacing', 'label' => cs_recall( 'label_copy' ) ] ),
              [
                'type'     => 'sub-group',
                'label'    => cs_recall( 'label_headlines' ),
                'controls' => [
                  cs_recall( 'control_mixin_gap', [ 'key' => 'content_h_margin_top',    'label' => cs_recall( 'label_top' )    ] ),
                  cs_recall( 'control_mixin_gap', [ 'key' => 'content_h_margin_bottom', 'label' => cs_recall( 'label_bottom' ) ] ),
                ],
              ],
              [
                'type'     => 'sub-group',
                'label'    => cs_recall( 'label_lists' ),
                'controls' => [
                  cs_recall( 'control_mixin_gap', [ 'key' => 'content_ol_padding_inline_start', 'label' => cs_recall( 'label_ol_inset' )  ] ),
                  cs_recall( 'control_mixin_gap', [ 'key' => 'content_ul_padding_inline_start', 'label' => cs_recall( 'label_ul_inset' )  ] ),
                  cs_recall( 'control_mixin_gap', [ 'key' => 'content_li_spacing',              'label' => cs_recall( 'label_list_item' ) ] ),
                ],
              ],
              cs_recall( 'control_mixin_gap', [ 'key' => 'content_media_spacing', 'label' => cs_recall( 'label_media' ) ] ),
            ],
          ],
        ]
      ],

      // Forms: Inputs
      // -------------

      [
        'type'  => 'group-sub-module',
        'label' => cs_recall( 'label_inputs' ),
        'options' => [ 'tag' => 'theme_options', 'name' => 'theme_options:forms_inputs' ],
        'controls' => [
          [
            'type'     => 'group',
            'controls' => [
              cs_recall( 'control_mixin_color_int', [ 'keys' => [ 'value' => "input_background_color", 'alt' => "input_background_color_alt" ], 'label' => cs_recall( 'label_background' ) ] ),
              csThemeOptionsTextFormat( 'input', [ 'label' => cs_recall('label_typography'), 'has_alt_color' => true, 'include_text_align' => true ] ),
              [
                'type'     => 'sub-group',
                'label'    => cs_recall( 'label_placeholder_opacity' ),
                'controls' => [
                  cs_recall( 'control_mixin_opacity', [ 'key' => 'input_placeholder_opacity'    , 'label' => cs_recall( 'label_base' )        ] ),
                  cs_recall( 'control_mixin_opacity', [ 'key' => 'input_placeholder_opacity_alt', 'label' => cs_recall( 'label_interaction' ) ] ),
                ],
              ],
              [
                'type'     => 'sub-group',
                'label'    => cs_recall( 'label_outline' ),
                'controls' => [
                  cs_recall( 'control_mixin_border_width', [ 'key' => "input_outline_width"                                        ] ),
                  cs_recall( 'control_mixin_color_solo',   [ 'key' => "input_outline_color", 'label' => cs_recall( 'label_color' ) ] ),
                ],
              ],
              [
                'type'     => 'sub-group',
                'label'    => cs_recall( 'label_indicators' ),
                'controls' => [
                  cs_recall( 'control_mixin_font_size', [ 'key'  => 'input_indicator_size',                                                       'label' => cs_recall( 'label_size' )    ] ),
                  cs_recall( 'control_mixin_gap',       [ 'key'  => 'input_indicator_spacing_x',                                                  'label' => cs_recall( 'label_spacing' ) ] ),
                  cs_recall( 'control_mixin_color_int', [ 'keys' => [ 'value' => "input_indicator_color", 'alt' => "input_indicator_color_alt" ], 'label' => cs_recall( 'label_color' )   ] ),
                ],
              ],
              csThemeOptionsBorder( 'input' ),
              [
                'type'     => 'sub-group',
                'label'    => cs_recall( 'label_padding' ),
                'controls' => [
                  cs_recall( 'control_mixin_gap', [ 'key' => 'input_padding_x',       'label' => cs_recall( 'label_horizontal' ) ] ),
                  cs_recall( 'control_mixin_gap', [ 'key' => 'input_padding_y_extra', 'label' => cs_recall( 'label_vertical' )   ] ),
                ],
              ],
              csThemeOptionsBoxShadow( 'input' ),
            ],
          ],
        ]
      ],



      // Forms: Checkboxes & Radios
      // --------------------------

      [
        'type'  => 'group-sub-module',
        'label' => cs_recall( 'label_checkboxes_and_radios' ),
        'options' => [ 'tag' => 'theme_options', 'name' => 'theme_options:forms_checkboxes_and_radios' ],
        'controls' => [
          [
            'type'     => 'group',
            'controls' => [
              cs_recall( 'control_mixin_color_int', [ 'keys' => [ 'value' => "rc_background_color", 'alt' => "rc_background_color_alt" ], 'label' => cs_recall( 'label_background' ) ] ),
              cs_recall( 'control_mixin_font_size', [ 'key'  => "rc_font_size",                                                           'label' => cs_recall( 'label_size' )       ] ),
              [
                'type'     => 'sub-group',
                'label'    => cs_recall( 'label_outline' ),
                'controls' => [
                  cs_recall( 'control_mixin_border_width', [ 'key' => "rc_outline_width"                                        ] ),
                  cs_recall( 'control_mixin_color_solo',   [ 'key' => "rc_outline_color", 'label' => cs_recall( 'label_color' ) ] ),
                ],
              ],
              [
                'type'     => 'sub-group',
                'label'    => cs_recall( 'label_markers' ),
                'controls' => [
                  cs_recall( 'control_mixin_gap',        [ 'key' => "checkbox_marker_inset", 'label' => cs_recall( 'label_checkbox' ) . '<br/>' . cs_recall( 'label_inset' ) ] ),
                  cs_recall( 'control_mixin_gap',        [ 'key' => "radio_marker_inset",    'label' => cs_recall( 'label_radio' ) . '<br/>' . cs_recall( 'label_inset' )    ] ),
                  cs_recall( 'control_mixin_color_solo', [ 'key' => "rc_marker_color",       'label' => cs_recall( 'label_color' )                                           ] ),
                ],
              ],
              csThemeOptionsBorder( 'rc' ),
              csThemeOptionsBoxShadow( 'rc' ),
            ],
          ],
        ]
      ],




      // Forms: Submits
      // --------------

      [
        'type'  => 'group-sub-module',
        'label' => cs_recall( 'label_submits' ),
        'options' => [ 'tag' => 'theme_options', 'name' => 'theme_options:forms_submits' ],
        'controls' => [
          [
            'type'     => 'group',
            'controls' => [
              cs_recall( 'control_mixin_color_int', [ 'keys' => [ 'value' => "submit_background_color", 'alt' => "submit_background_color_alt" ], 'label' => cs_recall( 'label_background' ) ] ),
              [
                'type'     => 'sub-group',
                'label'    => cs_recall( 'label_typography' ),
                'controls' => [
                  [
                    'keys'  => [ 'value' => "submit_font_weight", 'font_family' => "input_font_family" ],
                    'type'  => 'font-weight',
                    'label' => cs_recall( 'label_weight' ),
                  ],
                  csThemeOptionsTextAlign( 'submit' ),
                  cs_recall( 'control_mixin_color_int', [ 'keys' => [ 'value' => "submit_color", 'alt' => "submit_color_alt" ], 'label' => cs_recall( 'label_color' ) ] ),
                ],
              ],
              [
                'type'     => 'sub-group',
                'label'    => cs_recall( 'label_outline' ),
                'controls' => [
                  cs_recall( 'control_mixin_border_width', [ 'key' => "submit_outline_width"                                        ] ),
                  cs_recall( 'control_mixin_color_solo',   [ 'key' => "submit_outline_color", 'label' => cs_recall( 'label_color' ) ] ),
                ],
              ],
              csThemeOptionsBorder( 'submit', [ 'include' => [ 'radius', 'color' ] ] ),
              csThemeOptionsBoxShadow( 'submit' ),
            ],
          ],
        ]
      ],


      // Breakpoints
      // -----------

      [
        'type'  => 'group-sub-module',
        'label' => cs_recall( 'label_breakpoints' ),
        'options' => [ 'tag' => 'theme_options', 'name' => 'theme_options:breakpoints' ],
        'controls' => [
          [
            'keys' => [
              'base'   => 'bp_base',
              'ranges' => 'bp_ranges'
            ],
            'type'    => 'breakpoint-manager',
            'options' => [
              'notify' => [
                'message' => 'Please save and fully refresh Cornerstone for the new breakpoint configuration to take effect.',
                'timeout' => 10000,
              ],
            ],
          ],
        ]
      ],

    ]
  ]
];
