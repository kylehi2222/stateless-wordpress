<?php

// =============================================================================
// CORNERSTONE/INCLUDES/ELEMENTS/CONTROL-PARTIALS/DROPDOWN.PHP
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

function x_control_partial_dropdown( $settings ) {

  // Setup
  // -----

  $label_prefix       = ( isset( $settings['label_prefix'] )       ) ? $settings['label_prefix']       : '';
  $group              = ( isset( $settings['group'] )              ) ? $settings['group']              : 'dropdown';
  $group_title        = ( isset( $settings['group_title'] )        ) ? $settings['group_title']        : cs_recall( 'label_dropdown' );
  $conditions         = ( isset( $settings['conditions'] )         ) ? $settings['conditions']         : [];
  $is_layout_element  = ( isset( $settings['is_layout_element'] )  ) ? $settings['is_layout_element']  : false;
  $add_custom_atts    = ( isset( $settings['add_custom_atts'] )    ) ? $settings['add_custom_atts']    : false;
  $add_toggle_trigger = ( isset( $settings['add_toggle_trigger'] ) ) ? $settings['add_toggle_trigger'] : false;
  $inc_links          = ( isset( $settings['inc_links'] )          ) ? true                            : false;
  $add_sensitivity = cs_get_array_value($settings, 'add_sensitivity', true);
  $has_hover_condition = cs_get_array_value($settings, 'has_hover_condition', false);


  // Condition
  // ---------

  $condition_dropdown_bg_advanced = [ 'dropdown_bg_advanced' => true ];


  // Groups
  // ------

  $group_dropdown_children          = $group . ':children';
  $group_dropdown_setup             = $group . ':setup';
  $group_dropdown_background_layers = $group . ':background-layers';
  $group_dropdown_size              = $group . ':size';
  $group_dropdown_design            = $group . ':design';


  // Settings
  // --------

  $settings_dropdown = [
    'label_prefix' => $label_prefix,
    'group'        => $group_dropdown_design,
    'conditions'   => $conditions,
  ];

  $settings_dropdown_first = [
    'label_prefix' => cs_recall( 'label_first_dropdown' ),
    'group'        => $group_dropdown_design,
    'conditions'   => $conditions,
  ];

  $settings_dropdown_flexbox = [
    'toggle'       => 'dropdown_flexbox',
    'label_prefix' => $label_prefix,
    'group'        => $group_dropdown_design,
    'conditions'   => $conditions
  ];


  // Individual Controls - Children
  // ------------------------------

  $control_dropdown_children = [
    'type'  => 'children',
    'group' => $group_dropdown_children
  ];


  // Individual Controls - Setup
  // ---------------------------

  $control_dropdown_base_font_size = cs_recall( 'control_mixin_font_size',  [ 'key' => 'dropdown_base_font_size'                                                      ] );
  $control_dropdown_text_align     = cs_recall( 'control_mixin_text_align', [ 'key' => 'dropdown_text_align'                                                          ] );
  $control_dropdown_transition     = cs_recall( 'control_mixin_transition', [ 'keys' => [ 'duration' => 'dropdown_duration', 'timing' => 'dropdown_timing_function' ] ] );

  $control_dropdown_trigger = [
    'key'     => 'dropdown_toggle_trigger',
    'type'    => 'choose',
    'label'   => cs_recall( 'label_trigger' ),
    'options' => cs_recall( 'options_choices_toggle_trigger' ),
  ];

  $control_dropdown_position = [
    'key'     => 'dropdown_position',
    'type'    => 'select',
    'label'   => cs_recall( 'label_position' ),
    'options' => [
      'choices' => [
        // Auto
        [
          'value' => '',
          'label' => cs_recall('label_auto'),
        ],

        // Top Left
        [
          'value' => 'ul',
          'label' => cs_recall('label_top_left'),
        ],

        // Top Left
        [
          'value' => 'ur',
          'label' => cs_recall('label_top_right'),
        ],

        // Bottom Left
        [
          'value' => 'dl',
          'label' => cs_recall('label_bottom_left'),
        ],

        // Bottom Right
        [
          'value' => 'dr',
          'label' => cs_recall('label_bottom_right'),
        ],

      ]
    ],
  ];

  // Dynamic Rendering
  $control_dropdown_dynamic_rendering = cs_partial_controls('rvt:dynamic-rendering', [
    'key' => 'dropdown_dynamic_rendering',
  ]);

  $hover_conditions = $is_layout_element === true || $has_hover_condition
    ? [
      [
        'key' => 'dropdown_toggle_trigger',
        'op' => '==',
        'value' => 'hover',
      ]
    ]
    : null;

  // Hover Interval
  $control_dropdown_hover_interval = cs_partial_controls('range', [
    'label' => __("Interval", "cornerstone"),
    'description' => __("The amount of time in miliseconds to wait till the next dropdown is opened", "cornerstone"),
    'key' => 'dropdown_hover_interval',
    'min' => 0,
    'max' => 1000,
    'steps' => 1,
    'conditions' => $hover_conditions,
  ]);

  // Hover Timeout
  $control_dropdown_hover_timeout = cs_partial_controls('range', [
    'label' => __("Timeout", "cornerstone"),
    'description' => __("The amount of time in miliseconds to wait till closing itself", "cornerstone"),
    'key' => 'dropdown_hover_timeout',
    'min' => 0,
    'max' => 1000,
    'steps' => 1,
    'conditions' => $hover_conditions,
  ]);

  // Hover Sensitivity
  $control_dropdown_hover_sensitivity = cs_partial_controls('range', [
    'label' => __("Sensitivity", "cornerstone"),
    'description' => __("Higher sensitivity means more mouse movements will trigger the opening", "cornerstone"),
    'key' => 'dropdown_hover_sensitivity',
    'min' => 0,
    'max' => 10,
    'steps' => 1,
    'conditions' => $hover_conditions,
  ]);

  // Display Inline
  $control_dropdown_inline = [
    'key' => 'dropdown_display_inline',
    'label' => __('Inline', 'cornerstone'),
    'type' => 'toggle',
    'description' => __('Display the content of the dropdown inline with the button, helps to prevent flickering if your dropdown is in a moving area like a sticky bar', 'cornerstone'),
  ];

  $control_dropdown_overflow   = cs_recall( 'control_mixin_overflow',          [ 'key' => 'dropdown_overflow'                                                       ] );
  $control_dropdown_bg_color   = cs_recall( 'control_mixin_bg_color_solo',     [ 'keys' => [ 'value' => 'dropdown_bg_color' ]                                       ] );
  $control_dropdown_background = cs_recall( 'control_mixin_bg_color_solo_adv', [ 'keys' => [ 'value' => 'dropdown_bg_color', 'checkbox' => 'dropdown_bg_advanced' ] ] );


  // Individual Controls - Size
  // --------------------------

  $control_dropdown_width      = cs_recall( 'control_mixin_width',      [ 'key' => 'dropdown_width'      ] );
  $control_dropdown_min_width  = cs_recall( 'control_mixin_min_width',  [ 'key' => 'dropdown_min_width'  ] );
  $control_dropdown_max_width  = cs_recall( 'control_mixin_max_width',  [ 'key' => 'dropdown_max_width'  ] );
  $control_dropdown_height     = cs_recall( 'control_mixin_height',     [ 'key' => 'dropdown_height'     ] );
  $control_dropdown_min_height = cs_recall( 'control_mixin_min_height', [ 'key' => 'dropdown_min_height' ] );
  $control_dropdown_max_height = cs_recall( 'control_mixin_max_height', [ 'key' => 'dropdown_max_height' ] );


  // Controls List
  // -------------

  $control_list_setup = [];
  $control_list_size  = [];


  // Standard Dropdown Controls List
  // -------------------------------

  if ( $is_layout_element === false ) {
    $control_list_setup = [
      $control_dropdown_base_font_size,
      $control_dropdown_width
    ];

    if ( $add_toggle_trigger ) {
      $control_list_setup[] = $control_dropdown_trigger;
    }

    $control_list_setup[] = $control_dropdown_bg_color;
    $control_list_setup[] = $control_dropdown_transition;
    $control_list_setup[] = $control_dropdown_position;
    $control_list_setup[] = $control_dropdown_hover_interval;
    $control_list_setup[] = $control_dropdown_hover_timeout;

    if ($add_sensitivity) {
      $control_list_setup[] = $control_dropdown_hover_sensitivity;
    }
  }

  // Layout Element Controls List
  // ----------------------------

  if ( $is_layout_element === true ) {
    $control_list_setup = [
      $control_dropdown_base_font_size,
      $control_dropdown_text_align,
      $control_dropdown_transition,
    ];

    $control_list_setup[] = $control_dropdown_position;

    if ( $add_toggle_trigger ) {
      $control_list_setup[] = $control_dropdown_trigger;
    }

    $control_list_setup[] = $control_dropdown_hover_interval;
    $control_list_setup[] = $control_dropdown_hover_timeout;

    if ($add_sensitivity) {
      $control_list_setup[] = $control_dropdown_hover_sensitivity;
    }

    $control_list_setup[] = $control_dropdown_overflow;
    $control_list_setup[] = $control_dropdown_background;

    $control_list_size = [
      $control_dropdown_width,
      $control_dropdown_min_width,
      $control_dropdown_max_width,
      $control_dropdown_height,
      $control_dropdown_min_height,
      $control_dropdown_max_height,
    ];
  }

  $control_list_setup[] = $control_dropdown_inline;

  $control_list_setup = array_merge(
    $control_list_setup,
    cs_partial_controls('toggleable', [ 'prefix' => 'dropdown_' ])
  );



  // Compose Controls
  // ----------------

  $controls_before = [];
  $controls_after  = [];

  if ( $is_layout_element === true ) {
    $controls_before['controls'][] = $control_dropdown_children;
  }

  $controls_before['controls'][] = [
    'type'       => 'group',
    'group'      => $group_dropdown_setup,
    'conditions' => $conditions,
    'controls'   => $control_list_setup
  ];

  $controls_bg = ( $is_layout_element === true ) ? cs_partial_controls( 'bg', [
    'group'     => $group_dropdown_background_layers,
    'condition' => $condition_dropdown_bg_advanced,
  ] ) : [];

  if ( $is_layout_element === true ) {
    $controls_after['controls'][] = [
      'type'       => 'group',
      'group'      => $group_dropdown_size,
      'conditions' => $conditions,
      'controls'   => $control_list_size
    ];
  }

  if ( $is_layout_element === true ) {
    $controls_after['controls'][] = cs_control( 'flexbox', 'dropdown', $settings_dropdown_flexbox );
  }

  $controls_after['controls'][] = cs_control( 'margin', 'dropdown', $settings_dropdown_first );
  $controls_after['controls'][] = cs_control( 'padding', 'dropdown', $settings_dropdown );
  $controls_after['controls'][] = cs_control( 'border', 'dropdown', $settings_dropdown );
  $controls_after['controls'][] = cs_control( 'border-radius', 'dropdown', $settings_dropdown );
  $controls_after['controls'][] = cs_control( 'box-shadow', 'dropdown', $settings_dropdown );

  if ( $add_custom_atts ) {
    $controls_after['controls'][] = [
      'key'   => 'dropdown_custom_atts',
      'type'  => 'attributes',
      'group' => 'omega:setup',
      'label' => cs_recall( 'label_dropdown_custom_attributes' ),
    ];
  }

  $controls_after['control_nav'] = [
    $group                            => $group_title,
    $group_dropdown_children          => cs_recall( 'label_children' ),
    $group_dropdown_setup             => cs_recall( 'label_setup' ),
    $group_dropdown_background_layers => cs_recall( 'label_background_layers' ),
    $group_dropdown_size              => cs_recall( 'label_size' ),
    $group_dropdown_design            => cs_recall( 'label_design' ),
  ];


  // Return Controls
  // ---------------

  return cs_compose_controls( $controls_before, $controls_bg, $controls_after );
}

cs_register_control_partial( 'dropdown', 'x_control_partial_dropdown' );
