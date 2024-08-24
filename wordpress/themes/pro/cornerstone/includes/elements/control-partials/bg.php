<?php

// =============================================================================
// CORNERSTONE/INCLUDES/ELEMENTS/CONTROL-PARTIALS/BG.PHP
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

function x_control_partial_bg( $settings ) {

  // Setup
  // -----

  $k_pre        = ( isset( $settings['k_pre'] )        ) ? $settings['k_pre'] . '_'  : '';
  $group        = ( isset( $settings['group'] )        ) ? $settings['group']        : 'bg';
  $conditions   = ( isset( $settings['conditions'] )   ) ? $settings['conditions']   : [];


  // Conditions
  // ----------

  $condition_bg_lower_on_base      = [ 'key' => $k_pre . 'bg_lower_type', 'op' => 'NOT IN', 'value' => [ 'none' ] ];

  $condition_bg_upper_on_base      = [ 'key' => $k_pre . 'bg_upper_type', 'op' => 'NOT IN', 'value' => [ 'none' ] ];

  $conditions_bg_border_radius = array_merge( $conditions, [ $condition_bg_lower_on_base, array_merge( $condition_bg_upper_on_base, [ 'or' => true ] ) ] );


  // Control Groups (Advanced)
  // -------------------------

  // Compose Controls
  // ----------------

  return [
    'controls' => [
      // BG Lower
      cs_partial_controls('bg-layer', array_merge(
        $settings,
        [
          'layer_prefix' => 'bg_lower',
          'label' => cs_recall('label_lower'),
        ],
      )),
      // BG Upper
      cs_partial_controls('bg-layer', array_merge(
        $settings,
        [
          'layer_prefix' => 'bg_upper',
          'label' => cs_recall('label_upper'),
        ]
      )),
      cs_control( 'border-radius', $k_pre . 'bg', [
        'group'      => $group,
        'conditions' => $conditions_bg_border_radius,
      ] )
    ]
  ];

}

cs_register_control_partial( 'bg', 'x_control_partial_bg' );
