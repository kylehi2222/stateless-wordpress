<?php

// =============================================================================
// CORNERSTONE/INCLUDES/ELEMENTS/CONTROL-PARTIALS/OMEGA.PHP
// -----------------------------------------------------------------------------
// Element Controls
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Controls
// =============================================================================

// Controls
// =============================================================================

function x_control_partial_effects( $settings ) {

  // Setup
  // -----

  $conditions                              = ( isset( $settings['conditions'] )                              ) ? $settings['conditions']                              : [];
  $has_provider                            = ( isset( $settings['has_provider'] )                            ) ? $settings['has_provider']                            : false; // Link Child Interactions
  $no_scroll_offset_behavior_or_transition = ( isset( $settings['no_scroll_offset_behavior_or_transition'] ) ) ? $settings['no_scroll_offset_behavior_or_transition'] : false;


  // Groups
  // ------

  $group_effects       = 'effects';
  $group_effects_setup = $group_effects . ':setup';


  // Conditions
  // ----------

  $conditions_effects_alt              = array_merge( $conditions, [ [ 'effects_alt' => true ] ] );
  $conditions_effects_alt_animation    = array_merge( $conditions, [ [ 'effects_alt' => true ], [ 'effects_type_alt' => 'animation' ] ] );
  $conditions_effects_alt_transform    = array_merge( $conditions, [ [ 'effects_alt' => true ], [ 'effects_type_alt' => 'transform' ] ] );
  $conditions_effects_scroll           = array_merge( $conditions, [ [ 'effects_scroll' => true ] ] );
  $conditions_effects_scroll_animation = array_merge( $conditions, [ [ 'effects_scroll' => true ], [ 'effects_type_scroll' => 'animation' ] ] );
  $conditions_effects_scroll_transform = array_merge( $conditions, [ [ 'effects_scroll' => true ], [ 'effects_type_scroll' => 'transform' ] ] );
  $conditions_effects_enter_animation  = array_merge( $conditions, [ [ 'effects_scroll' => true ], [ 'effects_type_enter' => 'animation' ] ] );
  $conditions_effects_enter_transform  = array_merge( $conditions, [ [ 'effects_scroll' => true ], [ 'effects_type_enter' => 'transform' ] ] );
  $conditions_effects_exit_animation   = array_merge( $conditions, [ [ 'effects_scroll' => true ], [ 'effects_type_exit' => 'animation' ] ] );
  $conditions_effects_exit_transform   = array_merge( $conditions, [ [ 'effects_scroll' => true ], [ 'effects_type_exit' => 'transform' ] ] );
  $conditions_effects_mask             = array_merge( $conditions, [ [ 'effects_mask' => true ] ] );
  $conditions_effects_mask_linear      = array_merge( $conditions, [ [ 'effects_mask' => true ], [ 'effects_type_mask' => 'linear' ] ] );
  $conditions_effects_mask_radial      = array_merge( $conditions, [ [ 'effects_mask' => true ], [ 'effects_type_mask' => 'radial' ] ] );
  $conditions_effects_mask_image       = array_merge( $conditions, [ [ 'effects_mask' => true ], [ 'effects_type_mask' => 'image' ] ] );
  $conditions_effects_mask_custom      = array_merge( $conditions, [ [ 'effects_mask' => true ], [ 'effects_type_mask' => 'custom' ] ] );


  // Options
  // -------

  $options_effects_opacity = [
    'unit_mode'      => 'unitless',
    'fallback_value' => 1,
    'min'            => 0,
    'max'            => 1,
    'step'           => 0.025,
  ];

  $options_effects_offset = [
    'available_units' => [ 'px', '%' ],
    'fallback_value'  => '50%',
    'ranges'          => [
      'px' => [ 'min' => 0, 'max' => 200, 'step' => 10 ],
      '%'  => [ 'min' => 0, 'max' => 100, 'step' => 5  ],
    ],
  ];

  $options_effects_behavior = [
    'choices' => [
      [ 'value' => 'fire-once', 'label' => cs_recall( 'label_once' )   ],
      [ 'value' => 'reset',     'label' => cs_recall( 'label_reset' )  ],
      [ 'value' => 'in-n-out',  'label' => cs_recall( 'label_in_out' ) ],
    ]
  ];

  $options_effects_scroll_pointer_events = [
    'choices' => [
      [ 'value' => 'none', 'label' => cs_recall( 'label_none' ) ],
      [ 'value' => 'auto', 'label' => cs_recall( 'label_auto' ) ],
    ]
  ];

  $options_effects_perspective = [
    'available_units' => [ 'px' ],
    'fallback_value'  => '1000px',
    'ranges'          => [
      'px' => [ 'min' => 500, 'max' => 1500, 'step' => 50 ],
    ],
  ];

  $options_effects_type_choices = [
    [ 'value' => 'transform', 'label' => cs_recall( 'label_transform' ) ],
    [ 'value' => 'animation', 'label' => cs_recall( 'label_animation' ) ],
  ];

  $options_effects_transform = [
    'placeholder' => 'translate3d(50%, 0, 0)'
  ];

  $options_effects_transform_origin = [
    'choices' => [
      [ 'value' => '50% 100%',  'label' => cs_recall( 'label_bottom' )       ],
      [ 'value' => '0% 100%',   'label' => cs_recall( 'label_bottom_left' )  ],
      [ 'value' => '100% 100%', 'label' => cs_recall( 'label_bottom_right' ) ],
      [ 'value' => '50% 50%',   'label' => cs_recall( 'label_center' )       ],
      [ 'value' => '0% 50%',    'label' => cs_recall( 'label_left' )         ],
      [ 'value' => '100% 50%',  'label' => cs_recall( 'label_right' )        ],
      [ 'value' => '50% 0%',    'label' => cs_recall( 'label_top' )          ],
      [ 'value' => '0% 0%',     'label' => cs_recall( 'label_top_left' )     ],
      [ 'value' => '100% 0%',   'label' => cs_recall( 'label_top_right' )    ],
    ]
  ];

  $options_effects_backface_visibility = [
    'choices' => [
      [ 'value' => 'visible', 'label' => cs_recall( 'label_visible' ) ],
      [ 'value' => 'hidden',  'label' => cs_recall( 'label_hidden' )  ],
    ]
  ];

  $options_effects_filter = [
    'placeholder' => 'grayscale(100%)'
  ];

  $options_effects_animate_choices_attention_seekers = [
    [ 'value' => 'bounce',       'label' => 'Bounce'          ], // transform, animation-timing-function | transform-origin
    [ 'value' => 'flash',        'label' => 'Flash'           ], // opacity
    [ 'value' => 'flip',         'label' => 'Flip'            ], // transform, animation-timing-function | background-visibility: visible
    [ 'value' => 'headShake',    'label' => 'Head Shake'      ], // transform
    [ 'value' => 'heartBeat',    'label' => 'Heartbeat'       ], // transform
    [ 'value' => 'jackInTheBox', 'label' => 'Jack In The Box' ], // opacity, transform, transform-origin
    [ 'value' => 'jello',        'label' => 'Jello'           ], // transform | transform-origin
    [ 'value' => 'pulse',        'label' => 'Pulse'           ], // transform
    [ 'value' => 'rubberBand',   'label' => 'Rubber Band'     ], // transform
    [ 'value' => 'shakeX',       'label' => 'Shake X'         ], // transform
    [ 'value' => 'shakeY',       'label' => 'Shake Y'         ], // transform
    [ 'value' => 'swing',        'label' => 'Swing'           ], // transform | transform-origin
    [ 'value' => 'tada',         'label' => 'Tada'            ], // transform
    [ 'value' => 'wobble',       'label' => 'Wobble'          ], // transform
  ];

  $options_effects_animate_choices_enter = array_merge(
    [
      [ 'value' => 'backInDown',        'label' => 'Back In Down'         ], // opacity, transform
      [ 'value' => 'backInLeft',        'label' => 'Back In Left'         ], // opacity, transform
      [ 'value' => 'backInRight',       'label' => 'Back In Right'        ], // opacity, transform
      [ 'value' => 'backInUp',          'label' => 'Back In Up'           ], // opacity, transform

      [ 'value' => 'bounceIn',          'label' => 'Bounce In'            ], // opacity, transform, animation-timing-function
      [ 'value' => 'bounceInDown',      'label' => 'Bounce In Down'       ], // opacity, transform, animation-timing-function
      [ 'value' => 'bounceInLeft',      'label' => 'Bounce In Left'       ], // opacity, transform, animation-timing-function
      [ 'value' => 'bounceInRight',     'label' => 'Bounce In Right'      ], // opacity, transform, animation-timing-function
      [ 'value' => 'bounceInUp',        'label' => 'Bounce In Up'         ], // opacity, transform, animation-timing-function

      [ 'value' => 'fadeIn',            'label' => 'Fade In'              ], // opacity
      [ 'value' => 'fadeInDown',        'label' => 'Fade In Down'         ], // opacity, transform
      [ 'value' => 'fadeInDownBig',     'label' => 'Fade In Down Big'     ], // opacity, transform
      [ 'value' => 'fadeInLeft',        'label' => 'Fade In Left'         ], // opacity, transform
      [ 'value' => 'fadeInLeftBig',     'label' => 'Fade In Left Big'     ], // opacity, transform
      [ 'value' => 'fadeInRight',       'label' => 'Fade In Right'        ], // opacity, transform
      [ 'value' => 'fadeInRightBig',    'label' => 'Fade In Right Big'    ], // opacity, transform
      [ 'value' => 'fadeInUp',          'label' => 'Fade In Up'           ], // opacity, transform
      [ 'value' => 'fadeInUpBig',       'label' => 'Fade In Up Big'       ], // opacity, transform
      [ 'value' => 'fadeInTopLeft',     'label' => 'Fade In Top Left'     ], // opacity, transform
      [ 'value' => 'fadeInTopRight',    'label' => 'Fade In Top Right'    ], // opacity, transform
      [ 'value' => 'fadeInBottomLeft',  'label' => 'Fade In Bottom Left'  ], // opacity, transform
      [ 'value' => 'fadeInBottomRight', 'label' => 'Fade In Bottom Right' ], // opacity, transform

      [ 'value' => 'flipInX',           'label' => 'Flip In X'            ], // opacity, transform, animation-timing-function | background-visibility: visible
      [ 'value' => 'flipInY',           'label' => 'Flip In Y'            ], // opacity, transform, animation-timing-function | background-visibility: visible

      [ 'value' => 'lightSpeedInRight', 'label' => 'Light Speed In Right' ], // opacity, transform
      [ 'value' => 'lightSpeedInLeft',  'label' => 'Light Speed In Left'  ], // opacity, transform

      [ 'value' => 'rotateIn',          'label' => 'Rotate In'            ], // opacity, transform | transform-origin
      [ 'value' => 'rotateInDownLeft',  'label' => 'Rotate In Down Left'  ], // opacity, transform | transform-origin
      [ 'value' => 'rotateInDownRight', 'label' => 'Rotate In Down Right' ], // opacity, transform | transform-origin
      [ 'value' => 'rotateInUpLeft',    'label' => 'Rotate In Up Left'    ], // opacity, transform | transform-origin
      [ 'value' => 'rotateInUpRight',   'label' => 'Rotate In Up Right'   ], // opacity, transform | transform-origin

      [ 'value' => 'rollIn',            'label' => 'Roll In'              ], // opacity, transform

      [ 'value' => 'slideInDown',       'label' => 'Slide In Down'        ], // transform, visibility
      [ 'value' => 'slideInLeft',       'label' => 'Slide In Left'        ], // transform, visibility
      [ 'value' => 'slideInRight',      'label' => 'Slide In Right'       ], // transform, visibility
      [ 'value' => 'slideInUp',         'label' => 'Slide In Up'          ], // transform, visibility

      [ 'value' => 'zoomIn',            'label' => 'Zoom In'              ], // opacity, transform
      [ 'value' => 'zoomInDown',        'label' => 'Zoom In Down'         ], // opacity, transform, animation-timing-function
      [ 'value' => 'zoomInLeft',        'label' => 'Zoom In Left'         ], // opacity, transform, animation-timing-function
      [ 'value' => 'zoomInRight',       'label' => 'Zoom In Right'        ], // opacity, transform, animation-timing-function
      [ 'value' => 'zoomInUp',          'label' => 'Zoom In Up'           ], // opacity, transform, animation-timing-function
    ],
    $options_effects_animate_choices_attention_seekers
  );

  $options_effects_animate_choices_exit = array_merge(
    [
      // [ 'value' => 'hinge',              'label' => 'Hinge'                 ], // opacity, transform, animation-timing-function | transform-origin

      [ 'value' => 'backOutDown',        'label' => 'Back Out Down'         ], // opacity, transform
      [ 'value' => 'backOutLeft',        'label' => 'Back Out Left'         ], // opacity, transform
      [ 'value' => 'backOutRight',       'label' => 'Back Out Right'        ], // opacity, transform
      [ 'value' => 'backOutUp',          'label' => 'Back Out Up'           ], // opacity, transform

      [ 'value' => 'bounceOut',          'label' => 'Bounce Out'            ], // opacity, transform
      [ 'value' => 'bounceOutDown',      'label' => 'Bounce Out Down'       ], // opacity, transform
      [ 'value' => 'bounceOutLeft',      'label' => 'Bounce Out Left'       ], // opacity, transform
      [ 'value' => 'bounceOutRight',     'label' => 'Bounce Out Right'      ], // opacity, transform
      [ 'value' => 'bounceOutUp',        'label' => 'Bounce Out Up'         ], // opacity, transform

      [ 'value' => 'fadeOut',            'label' => 'Fade Out'              ], // opacity
      [ 'value' => 'fadeOutDown',        'label' => 'Fade Out Down'         ], // opacity, transform
      [ 'value' => 'fadeOutDownBig',     'label' => 'Fade Out Down Big'     ], // opacity, transform
      [ 'value' => 'fadeOutLeft',        'label' => 'Fade Out Left'         ], // opacity, transform
      [ 'value' => 'fadeOutLeftBig',     'label' => 'Fade Out Left Big'     ], // opacity, transform
      [ 'value' => 'fadeOutRight',       'label' => 'Fade Out Right'        ], // opacity, transform
      [ 'value' => 'fadeOutRightBig',    'label' => 'Fade Out Right Big'    ], // opacity, transform
      [ 'value' => 'fadeOutUp',          'label' => 'Fade Out Up'           ], // opacity, transform
      [ 'value' => 'fadeOutUpBig',       'label' => 'Fade Out Up Big'       ], // opacity, transform
      [ 'value' => 'fadeOutTopLeft',     'label' => 'Fade Out Top Left'     ], // opacity, transform
      [ 'value' => 'fadeOutTopRight',    'label' => 'Fade Out Top Right'    ], // opacity, transform
      [ 'value' => 'fadeOutBottomRight', 'label' => 'Fade Out Bottom Right' ], // opacity, transform
      [ 'value' => 'fadeOutBottomLeft',  'label' => 'Fade Out Bottom Left'  ], // opacity, transform

      [ 'value' => 'flipOutX',           'label' => 'Flip Out X'            ], // opacity, transform | background-visibility: visible
      [ 'value' => 'flipOutY',           'label' => 'Flip Out Y'            ], // opacity, transform | background-visibility: visible

      [ 'value' => 'lightSpeedOutRight', 'label' => 'Light Speed Out Right' ], // opacity, transform
      [ 'value' => 'lightSpeedOutLeft',  'label' => 'Light Speed Out Left'  ], // opacity, transform

      [ 'value' => 'rotateOut',          'label' => 'Rotate Out'            ], // opacity, transform | transform-origin
      [ 'value' => 'rotateOutDownLeft',  'label' => 'Rotate Out Down Left'  ], // opacity, transform | transform-origin
      [ 'value' => 'rotateOutDownRight', 'label' => 'Rotate Out Down Right' ], // opacity, transform | transform-origin
      [ 'value' => 'rotateOutUpLeft',    'label' => 'Rotate Out Up Left'    ], // opacity, transform | transform-origin
      [ 'value' => 'rotateOutUpRight',   'label' => 'Rotate Out Up Right'   ], // opacity, transform | transform-origin

      [ 'value' => 'rollOut',            'label' => 'Roll Out'              ], // opacity, transform

      [ 'value' => 'slideOutDown',       'label' => 'Slide Out Down'        ], // transform, visibility
      [ 'value' => 'slideOutLeft',       'label' => 'Slide Out Left'        ], // transform, visibility
      [ 'value' => 'slideOutRight',      'label' => 'Slide Out Right'       ], // transform, visibility
      [ 'value' => 'slideOutUp',         'label' => 'Slide Out Up'          ], // transform, visibility

      [ 'value' => 'zoomOut',            'label' => 'Zoom Out'              ], // opacity, transform
      [ 'value' => 'zoomOutDown',        'label' => 'Zoom Out Down'         ], // opacity, transform, animation-timing-function | transform-origin
      [ 'value' => 'zoomOutLeft',        'label' => 'Zoom Out Left'         ], // opacity, transform, animation-timing-function | transform-origin
      [ 'value' => 'zoomOutRight',       'label' => 'Zoom Out Right'        ], // opacity, transform, animation-timing-function | transform-origin
      [ 'value' => 'zoomOutUp',          'label' => 'Zoom Out Up'           ], // opacity, transform, animation-timing-function | transform-origin
    ],
    $options_effects_animate_choices_attention_seekers
  );

  $options_effects_animate_alt   = [ 'choices' => $options_effects_animate_choices_attention_seekers ];
  $options_effects_animate_enter = [ 'choices' => $options_effects_animate_choices_enter             ];
  $options_effects_animate_exit  = [ 'choices' => $options_effects_animate_choices_exit              ];

  $options_effects_mask_choices = [
    [ 'value' => 'linear', 'label' => cs_recall( 'label_linear' ) ],
    [ 'value' => 'radial', 'label' => cs_recall( 'label_radial' ) ],
    [ 'value' => 'image',  'label' => cs_recall( 'label_image' )  ],
    [ 'value' => 'custom', 'label' => cs_recall( 'label_custom' ) ],
  ];

  $options_effects_mask_repeat = [
    'choices' => [
      [ 'value' => 'no-repeat', 'label' => cs_recall( 'label_no_repeat' ) ],
      [ 'value' => 'repeat-x',  'label' => cs_recall( 'label_repeat_x' )  ],
      [ 'value' => 'repeat-y',  'label' => cs_recall( 'label_repeat_y' )  ],
      [ 'value' => 'repeat',    'label' => cs_recall( 'label_repeat' )    ],
      [ 'value' => 'space',     'label' => cs_recall( 'label_space' )     ],
      [ 'value' => 'round',     'label' => cs_recall( 'label_round' )     ],
    ],
  ];

  $options_effects_mask_direction = [
    'choices' => [
      [ 'value' => 'to right',  'label' => cs_recall( 'label_x' ) ],
      [ 'value' => 'to bottom', 'label' => cs_recall( 'label_y' ) ],
    ],
  ];

  $options_effects_mask_application = [
    'choices' => [
      [ 'value' => 'outer', 'label' => cs_recall( 'label_outer' ) ],
      [ 'value' => 'inner', 'label' => cs_recall( 'label_inner' ) ],
    ],
  ];

  $options_effects_mask_radial_shape = [
    'choices' => [
      [ 'value' => 'circle',  'label' => cs_recall( 'label_circle' )  ],
      [ 'value' => 'ellipse', 'label' => cs_recall( 'label_ellipse' ) ],
    ],
  ];

  $options_effects_mask_radial_size = [
    'choices' => [
      [ 'value' => 'farthest-side',   'label' => cs_recall( 'label_farthest_side' )   ],
      [ 'value' => 'farthest-corner', 'label' => cs_recall( 'label_farthest_corner' ) ],
      [ 'value' => 'closest-side',    'label' => cs_recall( 'label_closest_side' )    ],
      [ 'value' => 'closest-corner',  'label' => cs_recall( 'label_closest_corner' )  ],
    ],
  ];

  $options_effects_mask_composite = [
    'choices' => [
      [ 'value' => 'exclude',   'label' => cs_recall( 'label_exclude' )   ],
      [ 'value' => 'intersect', 'label' => cs_recall( 'label_intersect' ) ],
      [ 'value' => 'subtract',  'label' => cs_recall( 'label_subtract' )  ],
      [ 'value' => 'add',       'label' => cs_recall( 'label_add' )       ],
    ],
  ];


  // Control Nav
  // -----------

  $control_nav = [
    $group_effects       => cs_recall( 'label_effects' ),
    $group_effects_setup => '',
  ];


  // Controls
  // --------

  $controls = [];


  // Controls - Base
  // ---------------

  $controls[] = [
    'type'       => 'group',
    'group'      => $group_effects_setup,
    'conditions' => $conditions,
    'controls'   => [
      [
        'key'     => 'effects_opacity',
        'type'    => 'unit-slider',
        'label'   => cs_recall( 'label_opacity' ),
        'options' => $options_effects_opacity,
      ],
      [
        'key'     => 'effects_filter',
        'type'    => 'filter',
        'label'   => cs_recall( 'label_filter' ),
        'options' => $options_effects_filter,
      ],
      [
        'key'     => 'effects_transform',
        'type'    => 'transform',
        'label'   => cs_recall( 'label_transform' ),
        'options' => $options_effects_transform,
      ],
      [
        'key'     => 'effects_transform_origin',
        'type'    => 'select',
        'label'   => cs_recall( 'label_origin' ),
        'options' => $options_effects_transform_origin,
      ],
      [
        'key'     => 'effects_backface_visibility',
        'type'    => 'choose',
        'label'   => cs_recall( 'label_backface' ),
        'options' => $options_effects_backface_visibility,
      ],
      // [
      //   'key'     => 'effects_perspective',
      //   'type'    => 'unit-slider',
      //   'label'   => cs_recall( 'label_perspective' ),
      //   'options' => $options_effects_perspective,
      // ],
      // [
      //   'key'   => 'effects_perspective_origin',
      //   'type'  => 'text',
      //   'label' => cs_recall( 'label_perspective_origin' ),
      // ],
      [
        'type' => 'transition',
        'keys' => [
          'duration' => 'effects_duration',
          // 'delay'    => 'effects_delay',
          'timing'   => 'effects_timing_function'
        ],
      ],
    ],
  ];


  // Controls - Interaction Provider
  // -------------------------------

  if ( $has_provider ) {
    $controls[] = [
      'key'        => 'effects_provider',
      'type'       => 'group',
      'label'      => cs_recall( 'label_link_child_interactions' ),
      'group'      => $group_effects_setup,
      'options'    => cs_recall( 'options_group_toggle_off_on_bool' ),
      'conditions' => $conditions,
      'controls'   => [
        [
          'key'     => 'effects_provider_targets',
          'type'    => 'multi-choose',
          'label'   => cs_recall( 'label_targets' ),
          'options' => [
            'weighted' => true,
            'choices'  => [
              [ 'value' => 'colors',    'label' => cs_recall( 'label_colors' )    ],
              [ 'value' => 'particles', 'label' => cs_recall( 'label_particles' ) ],
              [ 'value' => 'effects',   'label' => cs_recall( 'label_effects' )   ],
            ],
          ],
        ],
      ],
    ];
  }


  // Controls - Interaction
  // ----------------------

  $controls[] = [
    'keys'     => [
      'toggle' => 'effects_alt',
      'select' => 'effects_type_alt'
    ],
    'type'     => 'group',
    'label'    => cs_recall( 'label_interaction' ),
    'group'    => $group_effects_setup,
    'options'  => [
      'toggle' => [
        'on'        => true,
        'off'       => false,
        'off_value' => cs_recall( 'label_off' ),
        'choices'   => $options_effects_type_choices,
      ]
    ],
    'controls' => [
      [
        'key'        => 'effects_opacity_alt',
        'type'       => 'unit-slider',
        'label'      => cs_recall( 'label_opacity' ),
        'conditions' => $conditions_effects_alt,
        'options'    => $options_effects_opacity,
      ],
      [
        'key'        => 'effects_filter_alt',
        'type'       => 'filter',
        'label'      => cs_recall( 'label_filter' ),
        'conditions' => $conditions_effects_alt,
        'options'    => $options_effects_filter,
      ],
      [
        'key'        => 'effects_animation_alt',
        'type'       => 'select',
        'label'      => cs_recall( 'label_animation' ),
        'conditions' => $conditions_effects_alt_animation,
        'options'    => $options_effects_animate_alt,
      ],
      [
        'key'        => 'effects_transform_alt',
        'type'       => 'transform',
        'label'      => cs_recall( 'label_transform' ),
        'conditions' => $conditions_effects_alt_transform,
        'options'    => $options_effects_transform,
      ],
      [
        'type'       => 'transition',
        'label'      => cs_recall( 'label_animation_transition' ),
        'conditions' => $conditions_effects_alt_animation,
        'keys'       => [
          'duration' => 'effects_duration_animation_alt',
          // 'delay'    => 'effects_delay_animation_alt',
          'timing'   => 'effects_timing_function_animation_alt'
        ],
      ],
    ],
  ];


  // Controls - Scroll
  // -----------------

  $controls[] = [
    'keys'     => [
      'toggle' => 'effects_scroll',
      'select' => 'effects_type_scroll'
    ],
    'type'     => 'group',
    'label'    => cs_recall( 'label_scroll' ),
    'group'    => $group_effects_setup,
    'options'  => [
      'toggle' => [
        'on'        => true,
        'off'       => false,
        'off_value' => cs_recall( 'label_off' ),
        'choices'   => $options_effects_type_choices,
      ]
    ],
    'controls' => [

      // Enter / In
      // ----------

      [
        'type'       => 'sub-group',
        'label'      => cs_recall( 'label_entrance' ),
        'conditions' => $conditions_effects_scroll,
        'controls'   => [
          [
            'key'     => 'effects_opacity_enter',
            'type'    => 'unit-slider',
            'label'   => cs_recall( 'label_opacity' ),
            'options' => $options_effects_opacity,
          ],
          [
            'key'     => 'effects_filter_enter',
            'type'    => 'filter',
            'label'   => cs_recall( 'label_filter' ),
            'options' => $options_effects_filter,
          ],
          [
            'key'        => 'effects_animation_enter',
            'type'       => 'select',
            'label'      => cs_recall( 'label_animation' ),
            'conditions' => $conditions_effects_scroll_animation,
            'options'    => $options_effects_animate_enter,
          ],
          [
            'key'        => 'effects_transform_enter',
            'type'       => 'transform',
            'label'      => cs_recall( 'label_transform' ),
            'conditions' => $conditions_effects_scroll_transform,
            'options'    => $options_effects_transform,
          ],
        ],
      ],


      // Exit / Out
      // ----------

      [
        'type'       => 'sub-group',
        'label'      => cs_recall( 'label_exit' ),
        'conditions' => $conditions_effects_scroll,
        'controls'   => [
          [
            'key'     => 'effects_opacity_exit',
            'type'    => 'unit-slider',
            'label'   => cs_recall( 'label_opacity' ),
            'options' => $options_effects_opacity,
          ],
          [
            'key'     => 'effects_filter_exit',
            'type'    => 'filter',
            'label'   => cs_recall( 'label_filter' ),
            'options' => $options_effects_filter,
          ],
          [
            'key'        => 'effects_animation_exit',
            'type'       => 'select',
            'label'      => cs_recall( 'label_animation' ),
            'conditions' => $conditions_effects_scroll_animation,
            'options'    => $options_effects_animate_exit,
          ],
          [
            'key'        => 'effects_transform_exit',
            'type'       => 'transform',
            'label'      => cs_recall( 'label_transform' ),
            'conditions' => $conditions_effects_scroll_transform,
            'options'    => $options_effects_transform,
          ],
        ],
      ],


      // Offset
      // ------

      $no_scroll_offset_behavior_or_transition ? null : [
        'type'       => 'group',
        'conditions' => $conditions_effects_scroll,
        'label'      => cs_recall( 'label_nbsp' ),
        'controls'   => [
          [
            'type'    => 'label',
            'label'   => cs_recall( 'label_top' ),
            'options' => [
              'columns' => 1
            ]
          ],
          [
            'type'    => 'label',
            'label'   => cs_recall( 'label_bottom' ),
            'options' => [
              'columns' => 1
            ]
          ],
        ],
      ],
      $no_scroll_offset_behavior_or_transition ? null : [
        'type'       => 'group',
        'label'      => cs_recall( 'label_offset' ),
        'conditions' => $conditions_effects_scroll,
        'options'    => [ 'grouped' => true ],
        'controls'   => [
          [
            'key'        => 'effects_offset_top',
            'type'       => 'unit',
            'label'      => cs_recall( 'label_top_offset' ),
            'conditions' => $conditions_effects_scroll,
            'options'    => $options_effects_offset,
          ],
          [
            'key'        => 'effects_offset_bottom',
            'type'       => 'unit',
            'label'      => cs_recall( 'label_bottom_offset' ),
            'conditions' => $conditions_effects_scroll,
            'options'    => $options_effects_offset,
          ],
        ],
      ],


      // Behavior
      // --------

      $no_scroll_offset_behavior_or_transition ? null : [
        'key'        => 'effects_behavior_scroll',
        'type'       => 'select',
        'label'      => cs_recall( 'label_behavior' ),
        'conditions' => $conditions_effects_scroll,
        'options'    => $options_effects_behavior,
      ],


      // Pointer Events
      // --------------

      [
        'key'        => 'effects_pointer_events_scroll',
        'type'       => 'choose',
        'label'      => cs_recall( 'label_exit_pointer_events' ),
        'conditions' => $conditions_effects_scroll,
        'options'    => $options_effects_scroll_pointer_events,
      ],


      // Transition
      // ----------

      $no_scroll_offset_behavior_or_transition ? null : [
        'type' => 'transition',
        'keys' => [
          'duration' => 'effects_duration_scroll',
          'delay'    => 'effects_delay_scroll',
          'timing'   => 'effects_timing_function_scroll'
        ],
      ],

    ],
  ];


  // Controls - Mask
  // ---------------

  $controls[] = [
    'keys'     => [
      'toggle' => 'effects_mask',
      'select' => 'effects_type_mask'
    ],
    'type'     => 'group',
    'label'    => cs_recall( 'label_mask' ),
    'group'    => $group_effects_setup,
    'options'  => [
      'toggle' => [
        'on'        => true,
        'off'       => false,
        'off_value' => cs_recall( 'label_off' ),
        'choices'   => $options_effects_mask_choices,
      ]
    ],
    'controls' => [

      // Linear
      // ------

      [
        'key'        => 'effects_mask_linear_direction',
        'type'       => 'choose',
        'label'      => cs_recall( 'label_direction' ),
        'conditions' => $conditions_effects_mask_linear,
        'options'    => $options_effects_mask_direction,
      ],
      [
        'key'        => 'effects_mask_linear_application',
        'type'       => 'choose',
        'label'      => cs_recall( 'label_application' ),
        'conditions' => $conditions_effects_mask_linear,
        'options'    => $options_effects_mask_application,
      ],
      [
        'key'        => 'effects_mask_linear_outer_stop_begin',
        'type'       => 'unit-slider',
        'label'      => cs_recall( 'label_outer_stop_begin' ),
        'conditions' => $conditions_effects_mask_linear,
        'options'    => cs_recall( 'options_width_and_height' ),
      ],
      [
        'key'        => 'effects_mask_linear_inner_stop_begin',
        'type'       => 'unit-slider',
        'label'      => cs_recall( 'label_inner_stop_begin' ),
        'conditions' => $conditions_effects_mask_linear,
        'options'    => cs_recall( 'options_width_and_height' ),
      ],
      [
        'key'        => 'effects_mask_linear_inner_stop_end',
        'type'       => 'unit-slider',
        'label'      => cs_recall( 'label_inner_stop_end' ),
        'conditions' => $conditions_effects_mask_linear,
        'options'    => cs_recall( 'options_width_and_height' ),
      ],
      [
        'key'        => 'effects_mask_linear_outer_stop_end',
        'type'       => 'unit-slider',
        'label'      => cs_recall( 'label_outer_stop_end' ),
        'conditions' => $conditions_effects_mask_linear,
        'options'    => cs_recall( 'options_width_and_height' ),
      ],


      // Radial
      // ------

      [
        'key'        => 'effects_mask_radial_shape',
        'type'       => 'choose',
        'label'      => cs_recall( 'label_shape' ),
        'conditions' => $conditions_effects_mask_radial,
        'options'    => $options_effects_mask_radial_shape,
      ],
      [
        'key'        => 'effects_mask_radial_application',
        'type'       => 'choose',
        'label'      => cs_recall( 'label_application' ),
        'conditions' => $conditions_effects_mask_radial,
        'options'    => $options_effects_mask_application,
      ],
      [
        'key'        => 'effects_mask_radial_size',
        'type'       => 'select',
        'label'      => cs_recall( 'label_contain_at' ),
        'conditions' => $conditions_effects_mask_radial,
        'options'    => $options_effects_mask_radial_size,
      ],
      [
        'key'        => 'effects_mask_radial_center',
        'type'       => 'text',
        'label'      => cs_recall( 'label_center_at' ),
        'conditions' => $conditions_effects_mask_radial,
      ],
      [
        'key'        => 'effects_mask_radial_inner_stop',
        'type'       => 'unit-slider',
        'label'      => cs_recall( 'label_inner_stop' ),
        'conditions' => $conditions_effects_mask_radial,
        'options'    => cs_recall( 'options_width_and_height' ),
      ],
      [
        'key'        => 'effects_mask_radial_outer_stop',
        'type'       => 'unit-slider',
        'label'      => cs_recall( 'label_outer_stop' ),
        'conditions' => $conditions_effects_mask_radial,
        'options'    => cs_recall( 'options_width_and_height' ),
      ],


      // Image
      // -----

      [
        'key'        => 'effects_mask_image_src',
        'type'       => 'image-source',
        'label'      => cs_recall( 'label_source' ),
        'conditions' => $conditions_effects_mask_image,
        'options'    => [
          'height' => 3,
        ],
      ],


      // Custom
      // ------

      [
        'key'        => 'effects_mask_custom_mask_image',
        'type'       => 'code-editor',
        'label'      => cs_recall( 'label_image' ),
        'conditions' => $conditions_effects_mask_custom,
        'options'    => [
          'button_label' => cs_recall( 'label_edit_mask' ),
          'header_label' => cs_recall( 'label_mask' ),
          'height'       => 3,
          'placeholder'  => __( "Provide a comma-separated list of CSS images:\n\ne.g.\n\nurl(...),\nlinear-gradient(...)", 'cornerstone' )
        ],
      ],


      // Shared
      // ------

      [
        'key'        => 'effects_mask_repeat',
        'type'       => 'select',
        'label'      => cs_recall( 'label_repeat' ),
        'conditions' => $conditions_effects_mask,
        'options'    => $options_effects_mask_repeat,
      ],
      [
        'key'        => 'effects_mask_size',
        'type'       => 'text',
        'label'      => cs_recall( 'label_size' ),
        'conditions' => $conditions_effects_mask,
        'options'    => [ 'dynamic_content' => true ],
      ],
      [
        'key'        => 'effects_mask_position',
        'type'       => 'text',
        'label'      => cs_recall( 'label_position' ),
        'conditions' => $conditions_effects_mask,
        'options'    => [ 'dynamic_content' => true ],
      ],
      [
        'key'         => 'effects_mask_composite',
        'type'        => 'select',
        'label'       => cs_recall( 'label_composite' ),
        'conditions'  => $conditions_effects_mask_custom,
        'description' => __( 'The mask-composite property will only work when two or more images are applied.', '__x__' ),
        'options'     => $options_effects_mask_composite,
      ],

    ],
  ];


  // Controls - Specialty
  // --------------------

  $controls[] = [
    'type'        => 'group',
    'label'       => cs_recall( 'label_specialty' ),
    'group'       => $group_effects_setup,
    'description' => __( 'As these features are more experimental in nature and may not behave consistently from browser to browser, we recommend using them in a &ldquo;progressive enhancement&rdquo; capacity for your designs.', '__x__' ),
    'controls'    => [
      [
        'key'     => 'effects_mix_blend_mode',
        'type'    => 'select',
        'label'   => cs_recall( 'label_mix_blend_mode' ),
        'options' => cs_recall( 'options_choices_mix_blend_mode' ),
      ],
      [
        'key'     => 'effects_backdrop_filter',
        'type'    => 'filter',
        'label'   => cs_recall( 'label_backdrop_filter' ),
        'options' => $options_effects_filter,
      ],
    ],
  ];


  // Output
  // ------

  return [
    'controls'    => $controls,
    'control_nav' => $control_nav
  ];
}

cs_register_control_partial( 'effects', 'x_control_partial_effects' );
