<?php

// =============================================================================
// CORNERSTONE/INCLUDES/ELEMENTS/DEFINITIONS/ICON.PHP
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

use Themeco\Cornerstone\Services\FontAwesome;

$values = cs_compose_values(
  'icon',
  'omega',
  'omega:custom-atts',
  'omega:looper-consumer'
);



// Style
// =============================================================================

function x_element_tss_icon() {
  return [
    'modules' => [ 'icon', 'effects' ]
  ];
}

// Render
// =============================================================================

function x_element_render_icon( $data ) {
  // If webfont double ensure that the webfont
  // will be loaded
  if ($data['icon_type'] === 'webfont') {
    FontAwesome::setShouldAddStyles(true);
  }

  return cs_get_partial_view( 'icon', array_merge( cs_extract( $data, [ 'effects' => ''] ), [
    'id' => $data['id'],
    'classes' => $data['classes'],
    'style' => $data['style'],
    'custom_atts' => $data['custom_atts'],
    'icon' => $data['icon'],
    'icon_type' => $data['icon_type'],
  ]));
}



// Builder Setup
// =============================================================================

function x_element_builder_setup_icon() {
  return cs_compose_controls(
    cs_partial_controls( 'icon', [
      'group_title' => cs_recall( 'label_primary_control_nav' ),
    ] ),
    cs_partial_controls( 'effects' ),
    cs_partial_controls( 'omega', [ 'add_custom_atts' => true, 'add_looper_consumer' => true ] )
  );
}



// Register Element
// =============================================================================

cs_register_element( 'icon', [
  'title'      => __( 'Icon', 'cornerstone' ),
  'values'     => $values,
  'includes'   => [ 'effects' ],
  'builder'    => 'x_element_builder_setup_icon',
  'tss'        => 'x_element_tss_icon',
  'render'     => 'x_element_render_icon',
  'icon'       => 'native',
  'options' => [
    'empty_placeholder' => false
  ],
  'group'      => 'media',
] );
