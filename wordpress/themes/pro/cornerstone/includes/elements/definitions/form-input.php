<?php

// =============================================================================
// CORNERSTONE/INCLUDES/ELEMENTS/DEFINITIONS/FORM-INPUT.PHP
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
    'form_input_type'        => cs_value( 'text', 'markup' ),
    'form_input_name'        => cs_value( '', 'markup' ),
    'form_input_value'       => cs_value( '', 'markup' ),
    'form_input_placeholder' => cs_value( '', 'markup' ),
  ],
  'omega',
  'omega:custom-atts',
  'omega:looper-consumer'
);



// Style
// =============================================================================

function x_element_tss_form_input() {
  // return [
  //   'modules' => [ 'effects' ]
  // ];
  return [];
}


// Render
// =============================================================================

function x_element_render_form_input( $data ) {

  // Conditions
  // ----------

  $is_select             = $data['form_input_type'] === 'select';
  $is_textarea           = $data['form_input_type'] === 'textarea';
  $is_checkbox           = $data['form_input_type'] === 'checkbox';
  $is_radio              = $data['form_input_type'] === 'radio';
  $is_submit             = $data['form_input_type'] === 'submit';
  $is_select_or_textarea = $is_select || $is_textarea;
  $has_placeholder       = ! $is_radio && ! $is_checkbox && ! $is_submit;


  // Prepare Attr Values
  // -------------------

  $classes = [ 'x-form-input', 'x-form-input-' . $data['form_input_type'] ];
  $the_tag = 'input';

  if ( $is_submit ) {
    $the_tag = 'button';
  }

  if ( $is_select_or_textarea ) {
    $the_tag = $data['form_input_type'];
  }


  // Atts
  // ----

  $atts = [
    'class' => array_merge( $classes, $data['classes'] ),
  ];

  if ( isset( $data['id'] ) && ! empty( $data['id'] ) ) {
    $atts['id'] = $data['id'];
  } else {
    $atts['id'] = $data['form_input_name'] . '-' . $data['unique_id'];
  }

  if ( ! $is_select_or_textarea ) {
    $atts['type'] = $data['form_input_type'];
  }

  if ( ! $is_submit && isset( $data['form_input_name'] ) && ! empty( $data['form_input_name'] ) ) {
    $atts['name'] = $data['form_input_name'];
  }

  if ( ! $is_textarea && isset( $data['form_input_value'] ) && ! empty( $data['form_input_value'] ) ) {
    $atts['value'] = $data['form_input_value'];
  }

  if ( $has_placeholder && isset( $data['form_input_placeholder'] ) && ! empty( $data['form_input_placeholder'] ) ) {
    $atts['placeholder'] = $data['form_input_placeholder'];
  }

  if ( isset( $data['style'] ) && ! empty( $data['style'] ) ) {
    $atts['style'] = $data['style'];
  }

  // $atts = cs_apply_effect( $atts, $data );


  // Output
  // ------

  if ( $is_submit ) {
    return cs_tag( $the_tag, $atts, $data['custom_atts'], $data['form_input_value'] );
  } else if ( $is_select ) {
    return cs_tag( $the_tag, $atts, $data['custom_atts'], ['<option>Thing 1</option><option>Thing 2</option><option>Thing 3</option><option>Thing 4</option><option>Thing 5</option>'] );
  } else if ( $is_textarea ) {
    return cs_tag( $the_tag, $atts, $data['custom_atts'], [$data['form_input_value']] );
  } else {
    return cs_tag( $the_tag, $atts, $data['custom_atts'], true );
  }

}



// Builder Setup
// =============================================================================

function x_element_builder_setup_form_input() {

  // Setup
  // -----

  $group       = 'form_input';
  $group_setup = 'form_input:setup';


  // Conditions
  // ----------

  $condition_form_input_name        = [ 'key' => 'form_input_type', 'op' => 'IN', 'value' => [ 'datetime-local', 'email', 'month', 'number', 'password', 'search', 'tel', 'text', 'time', 'url', 'week', 'select', 'textarea', 'radio', 'checkbox' ] ];
  $condition_form_input_placeholder = [ 'key' => 'form_input_type', 'op' => 'IN', 'value' => [ 'email', 'number', 'password', 'search', 'tel', 'text', 'url', 'select', 'textarea' ] ];


  // Controls
  // --------

  $control_form_input_type = cs_recall( 'control_mixin_form_input_type', [ 'key' => 'form_input_type' ] );

  $control_form_input_name = [
    'key'       => 'form_input_name',
    'type'      => 'text',
    'label'     => cs_recall( 'label_name' ),
    'condition' => $condition_form_input_name,
  ];

  $control_form_input_value = [
    'key'   => 'form_input_value',
    'type'  => 'text',
    'label' => cs_recall( 'label_value' ),
  ];

  $control_form_input_placeholder = [
    'key'       => 'form_input_placeholder',
    'type'      => 'text',
    'label'     => cs_recall( 'label_placeholder' ),
    'condition' => $condition_form_input_placeholder,
  ];


  // Output
  // ------

  return cs_compose_controls(
    [
      'controls' => [
        [
          'type'     => 'group',
          'group'    => $group_setup,
          'controls' => [
            $control_form_input_type,
            $control_form_input_name,
            $control_form_input_value,
            $control_form_input_placeholder,
          ],
        ],
      ],
      'control_nav' => [
        $group       => cs_recall( 'label_primary_control_nav' ),
        $group_setup => cs_recall( 'label_setup' ),
      ]
    ],
    // cs_partial_controls( 'effects' ),
    cs_partial_controls( 'omega', [ 'add_custom_atts' => true, 'add_looper_consumer' => true ] )
  );
}



// Register Element
// =============================================================================

cs_register_element( 'form-input', [
  'title'      => __( 'Form Input', 'cornerstone' ),
  'values'     => $values,
  'includes'   => [ 'effects' ],
  'builder'    => 'x_element_builder_setup_form_input',
  'tss'        => 'x_element_tss_form_input',
  'render'     => 'x_element_render_form_input',
  'icon'       => 'native',
] );
