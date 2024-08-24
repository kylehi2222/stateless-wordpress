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

function x_control_partial_omega( $settings ) {

  // Setup
  // -----

  $condition                   = ( isset( $settings['condition'] )                   ) ? $settings['condition']                   : [];
  $conditions                  = ( isset( $settings['conditions'] )                  ) ? $settings['conditions']                  : $condition;
  $title                       = ( isset( $settings['title'] )                       ) ? $settings['title']                       : false;
  $add_custom_atts             = ( isset( $settings['add_custom_atts'] )             ) ? $settings['add_custom_atts']             : false;
  $add_looper_provider         = ( isset( $settings['add_looper_provider'] )         ) ? $settings['add_looper_provider']         : false;
  $add_looper_consumer         = ( isset( $settings['add_looper_consumer'] )         ) ? $settings['add_looper_consumer']         : false;
  $add_style                   = ( isset( $settings['add_style'] )                   ) ? $settings['add_style']                   : false;
  $add_toggle_hash             = ( isset( $settings['add_toggle_hash'] )             ) ? $settings['add_toggle_hash']             : false;
  $toggle_hash_condition       = ( isset( $settings['toggle_hash_condition'] )       ) ? $settings['toggle_hash_condition']       : false;
  $add_hide_during_breakpoints = ( isset( $settings['add_hide_during_breakpoints'] ) ) ? $settings['add_hide_during_breakpoints'] : true;
  $hide_dom = ( isset( $settings['hide_dom'] ) ) ? $settings['hide_dom'] : false;
  //$hide_conditions = ( isset( $settings['hide_conditions'] ) ) ? $settings['hide_conditions'] : false;

  $add_presets = ( isset( $settings['add_presets'] ) ) ? $settings['add_presets'] : true;


  // Groups
  // ------

  $group_omega       = 'omega';
  $group_omega_setup = $group_omega . ':setup';
  $group_omega_presets = $group_omega . ':presets';


  // Control Nav
  // -----------

  $control_nav = [
    $group_omega       => cs_recall( 'label_customize' ),
    $group_omega_setup => '',
    $group_omega_presets => 'Presets',
  ];



  // Data
  // ----

  $control_setup = [
    'type'       => 'omega',
    'group'      => $group_omega_setup,
    'conditions' => $conditions,
    'options'    => [],
    'priority'   => 0
  ];

  if ( ! empty( $title ) ) {
    $control['label'] = $title;
  }


  // Keys
  // ----

  $keys = [
    'id'             => 'id',
    'class'          => 'class',
    'css'            => 'css',
    'show_condition' => 'show_condition'
  ];

  // Hide DOM Specific
  // keep show conditions
  if ($hide_dom) {
    unset($keys['id']);
    unset($keys['class']);
    unset($keys['css']);
  }

  if ( $add_hide_during_breakpoints ) {
    $keys['bp'] = 'hide_bp';
  }

  if ( $add_style ) {
    $keys['style'] = 'style';
  }

  if ( $add_toggle_hash ) {
    $keys['toggle_hash'] = 'toggle_hash';
  }

  if ( $toggle_hash_condition ) {
    $control_setup['options']['toggle_hash_condition'] = $toggle_hash_condition;
  }

  $control_setup['keys'] = $keys;

  $controls = [ $control_setup ];


  // Custom Attributes
  // -----------------

  if ( $add_custom_atts ) {
    $controls[] = [
      'key'        => 'custom_atts',
      'type'       => 'attributes',
      'label'      => cs_recall( 'label_custom_attributes' ),
      'group'      => $group_omega_setup,
      'conditions' => $conditions,
    ];
  }


  // Looper Provider
  // ---------------

  if ( $add_looper_provider ) {
    $controls[] = cs_partial_controls("looper-provider", [
      'group' => $group_omega_setup,
      'conditions' => $conditions,
    ]);
  }


  // Looper Consumer
  // ---------------

  if ( $add_looper_consumer ) {
    $controls[] = cs_partial_controls("looper-consumer", [
      'group' => $group_omega_setup,
      'conditions' => $conditions,
    ]);
  }

  if ( $add_presets ) {
    $controls[] = [
      'key'         => '__preset_control',
      'type'        => 'preset',
      'group'       => $group_omega_presets,
      'label'       => csi18n( 'app.templates.preset.entity' ),
      'description' => __( 'Will apply styles from elements of the same type', '__x__' ),
      'conditions'  => $conditions,
      'options' => [
        'ignoreControlData' => true,
      ],
    ];
  }


  // Output
  // ------

  return [
    'controls'    => $controls,
    'control_nav' => $control_nav
  ];
}

cs_register_control_partial( 'omega', 'x_control_partial_omega' );
