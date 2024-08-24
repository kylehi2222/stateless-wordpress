<?php

// =============================================================================
// CORNERSTONE/INCLUDES/ELEMENTS/DEFINITIONS/LOTTIE.PHP
// -----------------------------------------------------------------------------
// V2 element definitions.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Values
//   02. Style
//   03. Render
//   04. Builder Setup
//   05. Register Element
// =============================================================================

// Values
// =============================================================================

$values = cs_compose_values(
  [
    'lottie_base_font_size'        => cs_value( '1em' ),
    'lottie_source_type'           => cs_value( 'url', 'markup' ),
    'lottie_source_json'           => cs_value( '', 'markup' ),
    'lottie_source_url'            => cs_value( '', 'markup' ),
    'lottie_renderer'              => cs_value( 'svg', 'markup' ),

    'lottie_trigger_type'          => cs_value( 'autoplay', 'markup' ),
    'lottie_loop'                  => cs_value( false, 'markup' ),
    'lottie_loop_amount'           => cs_value( -1, 'markup' ),
    'lottie_offset_top'            => cs_value( '10%', 'markup' ),
    'lottie_offset_bottom'         => cs_value( '10%', 'markup' ),
    'lottie_animation_frame_start' => cs_value( '0%', 'markup' ),
    'lottie_animation_frame_end'   => cs_value( '100%', 'markup' ),
    'lottie_hover_behavior'        => cs_value( 'fire-once', 'markup' ),
    'lottie_speed_multiplier'      => cs_value( '1', 'markup' ),
    'lottie_animation_delay'       => cs_value( '0ms', 'markup' ),

    'lottie_width'                 => cs_value( 'auto' ),
    'lottie_max_width'             => cs_value( 'none' ),
    'lottie_margin'                => cs_value( '!0px 0px 0px 0px' ),
  ],
  'omega',
  'omega:custom-atts',
  'omega:looper-consumer'
);



// Style
// =============================================================================

function x_element_tss_lottie() {
  return [
    'require' => [ 'elements-extra' ],
    'modules' => [ 'lottie', 'effects'],
  ];
}



// Render
// =============================================================================

function x_element_render_lottie( $data ) {

  extract($data);

  // Prepare Attr Values
  // -------------------

  $lottie_data = [
    'type'    => $lottie_source_type,
    'trigger' => $lottie_trigger_type,
    'loop'    => $lottie_loop,
    'loop_amount' => $lottie_loop_amount,
    'renderer' => $data['lottie_renderer'],
  ];

  if ( $lottie_data['type'] === 'url' ) {
    $lottie_data['url'] = $lottie_source_url;
  }

  if ( $lottie_data['type'] === 'json' ) {
    $lottie_data['json'] = $lottie_source_json;
  }

  if ( $lottie_data['trigger'] === "hover" ) {
    $lottie_data['hover_behavior'] = $lottie_hover_behavior;
  }

  if (
    $lottie_data['trigger'] === "play-when-visible"
    || $lottie_data['trigger'] === "scroll-position-seek"
  ) {
    $lottie_data['offset_top']              = $lottie_offset_top;
    $lottie_data['offset_bottom']           = $lottie_offset_bottom;
  }

  $lottie_data['animation_delay']         = $lottie_animation_delay;
  $lottie_data['animation_frame_start']   = $lottie_animation_frame_start;
  $lottie_data['animation_frame_end']     = $lottie_animation_frame_end;
  $lottie_data['speed_multiplier']        = $lottie_speed_multiplier;


  // Prepare Atts
  // ------------

  $atts = [
    'class' => array_merge( ['x-lottie'], $classes ),
  ];

  if ( isset( $id ) && ! empty( $id ) ) {
    $atts['id'] = $id;
  }

  if ( isset( $data['style'] ) && ! empty( $data['style'] ) ) {
    $atts['style'] = $data['style'];
  }

  $atts = cs_apply_effect( $atts, $data );

  $atts = array_merge( $atts, cs_element_js_atts( 'lottie', $lottie_data, true ) ); // adds data-x-element-lottie


  // Output
  // ------

  wp_enqueue_script( 'cs-lottie' );

  return cs_tag( 'div', $atts, $custom_atts, '' );

}


// Builder Setup
// =============================================================================

function x_element_builder_setup_lottie() {

  // Conditions
  // ----------

  $condition_trigger_type_is_x1_hover                                                   = [ 'key' => 'lottie_trigger_type', 'op' => 'IN', 'value' => [ 'hover' ]                                                 ];
  $condition_trigger_type_is_x2_autoplay_and_play_when_visible = [
    'key' => 'lottie_trigger_type', 'op' => 'IN',
    'value' => [ 'autoplay', 'play-when-visible', 'click', ]
  ];
  $condition_trigger_type_is_x2_play_when_visible_and_scroll_position_seek              = [ 'key' => 'lottie_trigger_type', 'op' => 'IN', 'value' => [ 'play-when-visible', 'scroll-position-seek' ]             ];
  $condition_trigger_type_is_x3_autoplay_and_play_when_visible_and_scroll_position_seek = [ 'key' => 'lottie_trigger_type', 'op' => 'IN', 'value' => [ 'autoplay', 'play-when-visible', 'scroll-position-seek' ] ];
  $condition_trigger_type_is_x3_autoplay_and_play_when_visible_and_hover                = [ 'key' => 'lottie_trigger_type', 'op' => 'IN', 'value' => [ 'autoplay', 'play-when-visible', 'hover' ]                ];

  $condition_trigger_type_is_scroll_position_seek = [ 'key' => 'lottie_trigger_type', 'op' => 'IN', 'value' => [ 'scroll-position-seek' ]];

  $condition_trigger_type_is_not_scroll  = [
    'key' => 'lottie_trigger_type',
    'op' => '!=',
    'value' => 'scroll-position-seek'
  ];

  $condition_is_loop = [ 'key' => 'lottie_loop', 'op' => '==', 'value' => true ];


  // Conditions
  // ----------

  $options_lottie_offset_and_animation_frames = [
    'available_units' => [ '%' ],
    'fallback_value'  => '50%',
    'ranges'          => [
      '%' => [ 'min' => 0, 'max' => 100, 'step' => 5 ],
    ],
  ];

  $options_lottie_speed_multiplier = [
    'unit_mode'      => 'unitless',
    'fallback_value' => 1,
    'min'            => 0.25,
    'max'            => 3,
    'step'           => 0.25,
  ];

  $options_lottie_animation_delay = [
    'unit_mode'       => 'time',
    'available_units' => [ 's', 'ms' ],
    'fallback_value'  => '0ms',
    'ranges'          => [
      's'  => [ 'min' => 0, 'max' => 2,    'step' => 0.1 ],
      'ms' => [ 'min' => 0, 'max' => 2000, 'step' => 100 ],
    ],
  ];

  $options_lottie_loop_amount = [
    'unit_mode'      => 'unitless',
    'fallback_value' => -1,
    'min'            => -1,
    'max'            => 20,
    'step'           => 1,
  ];


  // Individual Controls - Setup
  // ---------------------------

  $control_lottie_base_font_size = cs_recall( 'control_mixin_font_size', [ 'key' => 'lottie_base_font_size' ] );

  $control_lottie_source_type = [
    'key'     => 'lottie_source_type',
    'type'    => 'choose',
    'label'   => cs_recall( 'label_source' ),
    'options' => [
      'choices' => [
        [ 'value' => 'url',  'label' => cs_recall( 'label_url' )  ],
        [ 'value' => 'json', 'label' => cs_recall( 'label_json' ) ],
      ],
    ],
  ];

  $control_lottie_source_url = [
    'key'       => 'lottie_source_url',
    'type'      => 'text',
    'label'     => cs_recall( 'label_url' ),
    'condition' => [ 'lottie_source_type' => 'url' ],
  ];

  $control_lottie_renderer = [
    'key'       => 'lottie_renderer',
    'type'      => 'select',
    'label'     => __("Renderer", "cornerstone"),
    'description' => __("SVG will scale the best, Canvas will have the best performance and for complex animations, and HTML for very simple animations and shapes.", "cornerstone"),
    'options' => [
      'choices' => [
        [
          'value' => 'canvas',
          'label' => __("Canvas", "cornerstone"),
        ],
        [
          'value' => 'html',
          'label' => __("HTML", "cornerstone"),
        ],
        [
          'value' => 'svg',
          'label' => __("SVG", "cornerstone"),
        ],
      ],
    ]
  ];

  $control_lottie_source_json = [
    'key'       => 'lottie_source_json',
    'type'      => 'code-editor',
    'label'     => cs_recall( 'label_json' ),
    'condition' => [ 'lottie_source_type' => 'json' ],
    'options'   => [
      'mode'         => 'json',
      'height'       => 2,
      'button_label' => cs_recall( 'label_edit' ),
      'header_label' => cs_recall( 'label_json' ),
    ],
  ];


  // Individual Controls - Options
  // -----------------------------

  // Trigger Type
  // ------------

  $control_lottie_trigger_type = [
    'key'     => 'lottie_trigger_type',
    'type'    => 'select',
    'label'   => cs_recall( 'label_trigger' ),
    'options' => [
      'choices' => [
        [ 'value' => 'autoplay',             'label' => cs_recall( 'label_autoplay' )             ],
        [ 'value' => 'play-when-visible',    'label' => cs_recall( 'label_play_when_visible' )    ],
        [ 'value' => 'scroll-position-seek', 'label' => cs_recall( 'label_scroll_position_seek' ) ],
        [ 'value' => 'hover',                'label' => cs_recall( 'label_hover' )                ],
        [ 'value' => 'click',                'label' => cs_recall( 'label_click' )                ],
      ],
    ],
  ];


  // Loop
  // ----

  $control_lottie_loop = [
    'key'       => 'lottie_loop',
    'type'      => 'choose',
    'label'     => cs_recall( 'label_loop_animation' ),
    //'condition' => $condition_trigger_type_is_x2_autoplay_and_play_when_visible,
    'options'   => cs_recall( 'options_choices_off_on_bool' ),
  ];

  $control_lottie_loop_amount = [
    'key'       => 'lottie_loop_amount',
    'type'      => 'unit-slider',
    'label'     => cs_recall( 'label_loop_amount' ),
    'description' => __("-1 is infinite except in scroll mode where it is also a 0. All other numbers are the number of times to loop", "cornerstone"),
    'condition' => $condition_is_loop,
    'options'   => $options_lottie_loop_amount,
  ];


  // Viewport Offset Top / Bottom
  // ----------------------------

  $control_lottie_viewport_offset_control_labels = [
    'type'      => 'group',
    'label'     => cs_recall( 'label_nbsp' ),
    'condition' => $condition_trigger_type_is_x3_autoplay_and_play_when_visible_and_scroll_position_seek,
    'description' => __("These percentages are based on the screen height", "cornerstone"),
    'options'   => [ 'faux_columns' => true ],
    'controls'  => [
      [
        'type'    => 'label',
        'label'   => cs_recall( 'label_top' ),
        'options' => [ 'columns' => 1 ],
      ],
      [
        'type'    => 'label',
        'label'   => cs_recall( 'label_bottom' ),
        'options' => [ 'columns' => 1 ],
      ],
    ],
  ];

  $control_lottie_viewport_offset_control_group = [
    'type'      => 'group',
    'label'     => cs_recall( 'label_viewport_offset' ),
    'condition' => $condition_trigger_type_is_x3_autoplay_and_play_when_visible_and_scroll_position_seek,
    'options'   => [ 'grouped' => true ],
    'controls'  => [
      [
        'key'     => 'lottie_offset_top',
        'type'    => 'unit',
        'label'   => cs_recall( 'label_top_offset' ),
        'options' => $options_lottie_offset_and_animation_frames,
      ],
      [
        'key'     => 'lottie_offset_bottom',
        'type'    => 'unit',
        'label'   => cs_recall( 'label_bottom_offset' ),
        'options' => $options_lottie_offset_and_animation_frames,
      ],
    ],
  ];


  // Animation Start / End Frames
  // ----------------------------

  $control_lottie_animation_start_end_frames_control_labels = [
    'type'      => 'group',
    'label'     => cs_recall( 'label_nbsp' ),
	//'condition' => $condition_trigger_type_is_x2_play_when_visible_and_scroll_position_seek,
    'options'   => [ 'faux_columns' => true ],
    'controls'  => [
      [
        'type'    => 'label',
        'label'   => cs_recall( 'label_start' ),
        'options' => [ 'columns' => 1 ],
      ],
      [
        'type'    => 'label',
        'label'   => cs_recall( 'label_end' ),
        'options' => [ 'columns' => 1 ],
      ],
    ],
  ];

  $control_lottie_animation_start_end_frames_control_group = [
    'type'      => 'group',
    'label'     => cs_recall( 'label_animation_frames' ),
    //'condition' => $condition_trigger_type_is_x2_play_when_visible_and_scroll_position_seek,
    'options'   => [ 'grouped' => true ],
    'controls'  => [
      [
        'key'     => 'lottie_animation_frame_start',
        'type'    => 'unit',
        'label'   => cs_recall( 'label_top_offset' ),
        'options' => $options_lottie_offset_and_animation_frames,
      ],
      [
        'key'     => 'lottie_animation_frame_end',
        'type'    => 'unit',
        'label'   => cs_recall( 'label_bottom_offset' ),
        'options' => $options_lottie_offset_and_animation_frames,
      ],
    ],
  ];


  // Hover Behavior
  // --------------

  $control_lottie_hover_behavior = [
    'key'       => 'lottie_hover_behavior',
    'type'      => 'select',
    'label'     => cs_recall( 'label_hover_behavior' ),
    'condition' => $condition_trigger_type_is_x1_hover,
    'options'   => [
      'choices' => [
        [ 'value' => 'fire-once',        'label' => cs_recall( 'label_play_once' )        ],
        [ 'value' => 'reverse-on-leave', 'label' => cs_recall( 'label_reverse_on_leave' ) ],
      ],
    ],
  ];


  // Animation Speed
  // ---------------

  $control_lottie_speed_multiplier = [
    'key'       => 'lottie_speed_multiplier',
    'type'      => 'unit-slider',
    'label'     => cs_recall( 'label_speed_multiplier' ),
    'condition' => $condition_trigger_type_is_x3_autoplay_and_play_when_visible_and_hover,
    'options'   => $options_lottie_speed_multiplier,
  ];


  // Animation Delay
  // ---------------

  $control_lottie_animation_delay = [
    'key'       => 'lottie_animation_delay',
    'type'      => 'unit-slider',
    'label'     => cs_recall( 'label_animation_delay' ),
    'condition' => $condition_trigger_type_is_not_scroll,
    'options'   => $options_lottie_animation_delay,
  ];



  // Individual Controls - Size
  // --------------------------

  $control_lottie_width     = cs_recall( 'control_mixin_width',     [ 'key' => 'lottie_width'     ] );
  $control_lottie_max_width = cs_recall( 'control_mixin_max_width', [ 'key' => 'lottie_max_width' ] );


  // Compose Controls
  // ----------------

  return cs_compose_controls(
    [
      'controls' => [
        [
          'type'     => 'group',
          'group'    => 'lottie:setup',
          'controls' => [
            $control_lottie_base_font_size,
            $control_lottie_renderer,
            $control_lottie_source_type,
            $control_lottie_source_url,
            $control_lottie_source_json,
          ],
        ],
        [
          'type'     => 'group',
          'group'    => 'lottie:options',
          'controls' => [
            $control_lottie_trigger_type,
            $control_lottie_loop,
            $control_lottie_loop_amount,
            $control_lottie_viewport_offset_control_labels,
            $control_lottie_viewport_offset_control_group,
            $control_lottie_animation_start_end_frames_control_labels,
            $control_lottie_animation_start_end_frames_control_group,
            $control_lottie_hover_behavior,
            $control_lottie_speed_multiplier,
            $control_lottie_animation_delay,
          ],
        ],
        [
          'type'     => 'group',
          'group'    => 'lottie:size',
          'controls' => [
            $control_lottie_width,
            $control_lottie_max_width,
          ],
        ],
        cs_control( 'margin', 'lottie', [ 'group' => 'lottie:design' ] ),
      ],
      'control_nav' => [
        'lottie'         => cs_recall( 'label_primary_control_nav' ),
        'lottie:setup'   => cs_recall( 'label_setup' ),
        'lottie:options' => cs_recall( 'label_options' ),
        'lottie:size'    => cs_recall( 'label_size' ),
        'lottie:design'  => cs_recall( 'label_design' ),
      ],
    ],
    cs_partial_controls( 'effects' ),
    cs_partial_controls( 'omega', [ 'add_custom_atts' => true, 'add_looper_consumer' => true ] )
  );
}



// Register Element
// =============================================================================

cs_register_element( 'lottie', [
  'title'    => __( 'Lottie', 'cornerstone' ),
  'values'   => $values,
  'includes' => [ 'effects' ],
  'builder'  => 'x_element_builder_setup_lottie',
  'tss'      => 'x_element_tss_lottie',
  'render'   => 'x_element_render_lottie',
  'icon'     => 'native',
  'group'    => 'media',

  //We handle empty placeholder in cs-lottie.js
  'options'  => [
    'empty_placeholder' => false
  ],

] );
