<?php

// =============================================================================
// CORNERSTONE/INCLUDES/ELEMENTS/DEFINITIONS/CONTENT-AREA-DROPDOWN.PHP
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
  'omega',
  'omega:toggle-hash',
  'omega:looper-provider',
  'omega:looper-consumer'
);



// Style
// =============================================================================

function x_element_tss_layout_dropdown() {
  return [
    'modules' => [
      ['anchor', [ 'args' => [ 'keyPrefix' => 'toggle' ] ]],
      ['dropdown', [ 'nested' => true, 'args' => [ 'isLayoutElement' => true ] ]],
      ['effects', [
        'args' => [
          'selectors' => ['.x-anchor-text-primary', '.x-anchor-text-secondary', '.x-graphic-child']
        ]
      ]],
      ['dropdown-parameters', [ 'module' => 'parameters' ]]
    ]
  ];
}

// Render
// =============================================================================

function x_element_render_layout_dropdown( $data ) {

  $hoverintent = cs_split_to_object($data, 'dropdown_hover');
  $hoverintent = json_encode($hoverintent);

  $data = array_merge(
    $data,
    cs_make_aria_atts( 'toggle_anchor', [
      'controls' => 'dropdown',
      'haspopup' => 'true',
      'expanded' => 'false',
      'label'    => __( 'Toggle Dropdown Content', 'cornerstone' ),
    ], $data['id'], $data['unique_id'] )
  );

  $offscreen_atts = [];

  if (isset($data['_builder_outlet']) && ! apply_filters( 'cs_render_looper_is_virtual', false )) {
    $offscreen_atts['data-cs-dropzone'] = $data['_id'];
    $offscreen_atts['data-cs-observe'] = 'true';
    $offscreen_atts['data-cs-observeable-id'] = $data['_id'];
  }

  // Dropdown custom attributes
  if (!empty($data['dropdown_custom_atts'])) {
    $offscreen_atts = array_merge($offscreen_atts, json_decode($data['dropdown_custom_atts'], true));
  }

  $data_dropdown = array_merge( cs_extract( $data, [ 'dropdown' => '' ] ), [
    'id' => $data['id'],
    'classes' => [
      $data['_tss']['dropdown'],
      $data['_tss']['dropdown-parameters'],
      $data['style_id'],
      $data['class']
    ],
    'atts' => [
      'data-x-hoverintent' => $hoverintent,
    ],
    'style' => $data['style'],
    '_region' => $data['_region'],
    'toggleable_id' => $data['unique_id'],
    'dropdown_custom_atts' => $offscreen_atts,
    'dropdown_content_dynamic_rendering' => $data['dropdown_content_dynamic_rendering'],
    'dropdown_content' => [
      $data['dropdown_bg_advanced'] ? cs_make_bg( $data ) : '',
      cs_render_child_elements( $data, 'x_layout_dropdown' )
    ]
  ] );

  $dropdown = cs_get_partial_view( 'dropdown', $data_dropdown );

  // Dont display inline
  if (empty($data['dropdown_display_inline'])) {
    cs_defer_html('cs_deferred', $dropdown);
    $dropdown = '';
  }

  $data_toggle = array_merge( cs_extract( $data, [ 'toggle_anchor' => 'anchor', 'toggle' => '', 'effects' => '' ] ), [
    'id'             => $data['id'],
    'classes'        => $data['classes'],
    'style'          => $data['style'],
    '_region'        => $data['_region'],
    'unique_id'      => $data['unique_id'],
    'toggle_trigger' => $data['dropdown_toggle_trigger']
  ]);

  return cs_get_partial_view( 'anchor', $data_toggle ) . $dropdown;

}



// Builder Setup
// =============================================================================

function x_element_builder_setup_layout_dropdown() {
  return cs_compose_controls(
    cs_partial_controls( 'dropdown', [
      'is_layout_element' => true,
      'add_custom_atts' => true,
      'add_toggle_trigger' => true,
      'add_sensitivity' => false,
    ]),
    cs_partial_controls( 'anchor', cs_recall( 'settings_anchor:toggle' ) ),
    cs_partial_controls( 'effects' ),
    cs_partial_controls( 'omega', array( 'add_toggle_hash' => true, 'add_looper_provider' => true, 'add_looper_consumer' => true ) )
  );
}



// Register Element
// =============================================================================

cs_register_element( 'layout-dropdown', [
  'title'      => __( 'Dropdown', '__x__' ),
  'values'     => $values,
  'includes'   => [ 'toggle', 'dropdown', 'bg', 'effects' ],
  'builder'    => 'x_element_builder_setup_layout_dropdown',
  'tss'        => 'x_element_tss_layout_dropdown',
  'render'     => 'x_element_render_layout_dropdown',
  'icon'       => 'native',
  'group'      => 'interactive',
  'children'   => 'x_layout_dropdown',
  'options'    => [
    'valid_children' => '*',
    'dropzone'       => [
      'enabled'   => true,
      'offscreen' => true,
      'selector'  => '.x-dropdown'
    ],
    'toggle_on_create' => [
      'enabled' => true
    ]
  ]
] );
