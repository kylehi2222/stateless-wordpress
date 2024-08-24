<?php

$condition_text_decoration_is_line_through_or_underline = [ 'key' => 'a_text_decoration_line', 'op' => 'IN', 'value' => ['line-through', 'underline'] ];
$condition_text_decoration_is_underline                 = [ 'key' => 'a_text_decoration_line', 'op' => 'IN', 'value' => ['underline']                 ];


$choices_button_style = [
  [ 'value' => 'real',        'label' => __( '3D', '__x__' )          ],
  [ 'value' => 'flat',        'label' => __( 'Flat', '__x__' )        ],
  [ 'value' => 'transparent', 'label' => __( 'Transparent', '__x__' ) ],
];

$choices_button_shape = [
  [ 'value' => 'square',  'label' => __( 'Square', '__x__' )  ],
  [ 'value' => 'rounded', 'label' => __( 'Rounded', '__x__' ) ],
  [ 'value' => 'pill',    'label' => __( 'Pill', '__x__' )    ],
];

$choices_button_size = [
  [ 'value' => 'mini',    'label' => __( 'Mini', '__x__' )        ],
  [ 'value' => 'small',   'label' => __( 'Small', '__x__' )       ],
  [ 'value' => 'regular', 'label' => __( 'Regular', '__x__' )     ],
  [ 'value' => 'large',   'label' => __( 'Large', '__x__' )       ],
  [ 'value' => 'x-large', 'label' => __( 'Extra Large', '__x__' ) ],
  [ 'value' => 'jumbo',   'label' => __( 'Jumbo', '__x__' )       ],
];

$condition_button_background_color              = [ 'option' => 'x_button_style', 'value' => 'transparent', 'op' => '!=' ];

// Output
// =============================================================================

return [
  [
    'type'  => 'group-module',
    'label' => cs_recall( 'label_options' ),
    'controls' => [


      // Background
      // ----------

      //[
        //'type'  => 'group-sub-module',
        //'label' => cs_recall( 'label_background' ),
        //'options' => [ 'tag' => 'theme_options', 'name' => 'theme_options:bg' ],
        //'controls' => [
          //[
            //'type'     => 'group',
            //'controls' => [
              //cs_recall( 'control_mixin_color_solo', [ 'key' => 'root_background_color', 'label' => cs_recall( 'label_color' ) ] ),
            //],
          //]
        //]
      //],

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
              csThemeOptionsTextFormat( 'label' ),
            ],
          ],
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

      // Configuration
      [
        'type'  => 'group-sub-module',
        'label' => __( 'Setup', '__x__' ),
        'controls' => [
          // Font awesome
          apply_filters("cs_theme_options_fontawesome_group", null),
        ],
      ],



      // Breakpoints
      // -----------

      //[
        //'type'  => 'group-sub-module',
        //'label' => cs_recall( 'label_breakpoints' ),
        //'options' => [ 'tag' => 'theme_options', 'name' => 'theme_options:breakpoints' ],
        //'controls' => [
          //[
            //'keys' => [
              //'base'   => 'bp_base',
              //'ranges' => 'bp_ranges'
            //],
            //'type'    => 'breakpoint-manager',
            //'options' => [
              //'notify' => [
                //'message' => 'Please save and fully refresh Cornerstone for the new breakpoint configuration to take effect.',
                //'timeout' => 10000,
              //],
            //],
          //],
        //]
      //],

    ]
  ]
];
