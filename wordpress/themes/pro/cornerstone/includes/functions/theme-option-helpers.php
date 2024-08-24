<?php

// Conditions
// =============================================================================

$condition_text_decoration_is_line_through_or_underline = [ 'key' => 'a_text_decoration_line', 'op' => 'IN', 'value' => ['line-through', 'underline'] ];
$condition_text_decoration_is_underline                 = [ 'key' => 'a_text_decoration_line', 'op' => 'IN', 'value' => ['underline']                 ];



// Shared Formatting
// =============================================================================

function csThemeOptionsBorder( $k_pre = 'input', $options = [] ) {

  $defaults = [
    'label'   => cs_recall( "label_border" ),
    'include' => [ 'width', 'style', 'radius', 'color' ],
  ];

  $options = array_merge( $defaults, $options );

  $controls = [];

  if ( in_array( 'width', $options['include'] ) ) {
    $controls[] = cs_recall( 'control_mixin_border_width', [ 'key' => "{$k_pre}_border_width" ] );
  }

  if ( in_array( 'style', $options['include'] ) ) {
    $controls[] = cs_recall( 'control_mixin_border_style', [ 'key' => "{$k_pre}_border_style" ] );
  }

  if ( in_array( 'radius', $options['include'] ) ) {
    $controls[] = cs_recall( 'control_mixin_border_radius', [ 'key' => "{$k_pre}_border_radius" ] );
  }

  if ( in_array( 'color', $options['include'] ) ) {
    $controls[] = cs_recall( 'control_mixin_color_int', [ 'keys' => [ 'value' => "{$k_pre}_border_color", 'alt' => "{$k_pre}_border_color_alt" ], 'label' => cs_recall( 'label_color' ) ] );
  }

  return [
    'type'     => 'sub-group',
    'label'    => $options['label'],
    'controls' => $controls,
  ];

}

function csThemeOptionsBoxShadow( $k_pre = 'input', $options = [] ) {

  $defaults = [
    'label' => cs_recall( "label_box_shadow" ),
  ];

  $options = array_merge( $defaults, $options );

  return [
    'type'     => 'sub-group',
    'label'    => $options['label'],
    'controls' => [
      cs_recall( 'control_mixin_gap',       [ 'key' => "{$k_pre}_box_shadow_x",                                                               'label' => cs_recall( 'label_x_offset' ) ] ),
      cs_recall( 'control_mixin_gap',       [ 'key' => "{$k_pre}_box_shadow_y",                                                               'label' => cs_recall( 'label_y_offset' ) ] ),
      cs_recall( 'control_mixin_gap',       [ 'key' => "{$k_pre}_box_shadow_blur",                                                            'label' => cs_recall( 'label_blur' )     ] ),
      cs_recall( 'control_mixin_gap',       [ 'key' => "{$k_pre}_box_shadow_spread",                                                          'label' => cs_recall( 'label_spread' )   ] ),
      cs_recall( 'control_mixin_color_int', [ 'keys' => [ 'value' => "{$k_pre}_box_shadow_color", 'alt' => "{$k_pre}_box_shadow_color_alt" ], 'label' => cs_recall( 'label_color' )    ] ),
    ],
  ];

}

function csThemeOptionsFontFamilyAndWeight( $k_pre = 'root' ) {
  return [
    [
      'key'   => "{$k_pre}_font_family",
      'type'  => 'font-family',
      'label' => cs_recall( 'label_font' ),
    ],
    [
      'keys'  => [ 'value' => "{$k_pre}_font_weight", 'font_family' => "{$k_pre}_font_family" ],
      'type'  => 'font-weight',
      'label' => cs_recall( 'label_weight' ),
    ],
  ];
}

function csThemeOptionsTextAlign( $k_pre = 'root' ) {
  return [
    'key'     => "{$k_pre}_text_align",
    'type'    => 'choose',
    'label'   => cs_recall( 'label_style' ),
    'options' => [
      'off_value' => 'inherit',
      'choices'   => [
        [ 'value' => 'left',    'icon' => 'ui:text-align-left'    ],
        [ 'value' => 'center',  'icon' => 'ui:text-align-center'  ],
        [ 'value' => 'right',   'icon' => 'ui:text-align-right'   ],
        [ 'value' => 'justify', 'icon' => 'ui:text-align-justify' ],
      ],
    ],
  ];
}

function csThemeOptionsTextFormat( $k_pre = 'h1', $options = [] ) {

  $defaults = [
    'label'              => cs_recall( "label_{$k_pre}" ),
    'has_alt_color'      => false,
    'include_text_align' => false,
  ];

  $options = array_merge( $defaults, $options );

  $controls = array_merge(
    csThemeOptionsFontFamilyAndWeight( $k_pre ),
    [
      cs_recall( 'control_mixin_font_size',      [ 'key' => "{$k_pre}_font_size",      'label' => cs_recall( 'label_size' )    ] ),
      cs_recall( 'control_mixin_letter_spacing', [ 'key' => "{$k_pre}_letter_spacing", 'label' => cs_recall( 'label_spacing' ) ] ),
      cs_recall( 'control_mixin_line_height',    [ 'key' => "{$k_pre}_line_height",    'label' => cs_recall( 'label_height' )  ] ),
      [
        'key'     => "{$k_pre}_font_style",
        'type'    => 'choose',
        'label'   => cs_recall( 'label_style' ),
        'options' => [
          'off_value' => 'inherit',
          'choices'   => [
            [ 'value' => 'normal', 'icon' => 'css:fs-normal' ],
            [ 'value' => 'italic', 'icon' => 'css:fs-italic' ],
          ],
        ],
      ],
    ]
  );

  if ( $options['include_text_align'] ) {
    $controls[] = csThemeOptionsTextAlign( $k_pre );
  }

  $controls[] = [
    'key'     => "{$k_pre}_text_transform",
    'type'    => 'choose',
    'label'   => cs_recall( 'label_transform' ),
    'options' => [
      'off_value' => 'inherit',
      'choices'   => [
        [ 'value' => 'uppercase',  'icon' => 'css:tt-uppercase'  ],
        [ 'value' => 'capitalize', 'icon' => 'css:tt-capitalize' ],
        [ 'value' => 'lowercase',  'icon' => 'css:tt-lowercase'  ],
      ],
    ],
  ];

  if ( $options['has_alt_color'] ) {
    $controls[] = cs_recall( 'control_mixin_color_int', [ 'keys' => [ 'value' => "{$k_pre}_color", 'alt' => "{$k_pre}_color_alt" ], 'label' => cs_recall( 'label_color' ) ] );
  } else {
    $controls[] = cs_recall( 'control_mixin_color_solo', [ 'key' => "{$k_pre}_color", 'label' => cs_recall( 'label_color' ) ] );
  }

  return [
    'type'     => 'sub-group',
    'label'    => $options['label'],
    'controls' => $controls
  ];
}


