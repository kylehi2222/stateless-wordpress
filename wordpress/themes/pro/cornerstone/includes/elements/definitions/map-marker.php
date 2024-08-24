<?php

// =============================================================================
// CORNERSTONE/INCLUDES/ELEMENTS/DEFINITIONS/MAP-MARKER.PHP
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
    'map_marker_lat'           => cs_value( '40.674', 'markup', true ),
    'map_marker_lng'           => cs_value( '-73.945', 'markup', true ),
    'map_marker_content_start' => cs_value( 'closed', 'markup', true ),
    'map_marker_content'       => cs_value( '', 'markup', true ),
    'map_marker_image_src'     => cs_value( '', 'markup', true ),
    'map_marker_image_retina'  => cs_value( true, 'markup', true ),
    'map_marker_image_width'   => cs_value( '', 'markup', true ),
    'map_marker_image_height'  => cs_value( '', 'markup', true ),
    'map_marker_offset_x'      => cs_value( '0%', 'markup', true ),
    'map_marker_offset_y'      => cs_value( '-50%', 'markup', true ),
  ],
  'omega:looper-consumer'
);


// Render
// =============================================================================

function x_element_render_map_marker( $data ) {

  extract( $data );

  // Prepare Atts
  // ------------

  $data = [
    'lat'          => cs_dynamic_content( $map_marker_lat ),
    'lng'          => cs_dynamic_content( $map_marker_lng ),
    'content'      => cs_dynamic_content( $map_marker_content ),
    'contentStart' => $map_marker_content_start,
  ];

  if ( $map_marker_image_src !== '' ) {
    $atts_image = cs_apply_image_atts( [
      'src'    => cs_dynamic_content( $map_marker_image_src ),
      'width'  => $map_marker_image_width,
      'height' => $map_marker_image_height
    ] );

    $data = array_merge( $data, array(
      'imageSrc'     => isset( $atts_image['src'] )    ? $atts_image['src'] : '',
      'imageWidth'   => isset( $atts_image['width'] )  ? $atts_image['width'] : '',
      'imageHeight'  => isset( $atts_image['height'] ) ? $atts_image['height'] : '',
      'imageRetina'  => $map_marker_image_retina,
      'imageOffsetX' => $map_marker_offset_x,
      'imageOffsetY' => $map_marker_offset_y,
    ) );
  }

  // Renders as JSON for map to use through
  // cs_render_child_elements_as_array
  return cs_render_function_as_array($data);
}


// Builder Setup
// =============================================================================

function x_element_builder_setup_map_marker() {

  $control_setup = [
    'type'     => 'group',
    'title'    => cs_recall( 'label_setup' ),
    'group'    => 'map_marker:setup',
    'controls' => [
      [
        'key'   => 'map_marker_lat',
        'type'  => 'text',
        'label' => cs_recall( 'label_latitude' ),
      ],
      [
        'key'   => 'map_marker_lng',
        'type'  => 'text',
        'label' => cs_recall( 'label_longitude' ),
      ],
      [
        'key'     => 'map_marker_content_start',
        'type'    => 'choose',
        'label'   => cs_recall( 'label_content' ),
        'options' => [
          'choices' => [
            [ 'value' => 'open',   'label' => cs_recall( 'label_open' )   ],
            [ 'value' => 'closed', 'label' => cs_recall( 'label_closed' ) ],
          ],
        ],
      ],
      [
        'key'     => 'map_marker_content',
        'type'    => 'textarea',
        'label'   => cs_recall( 'label_nbsp' ),
        'options' => [
          'height' => 3,
        ],
      ],
    ],
  ];

  $control_custom_image = [
    'keys' => [
      'img_source' => 'map_marker_image_src',
      'is_retina'  => 'map_marker_image_retina',
      'width'      => 'map_marker_image_width',
      'height'     => 'map_marker_image_height',
    ],
    'type'  => 'image',
    'title' => cs_recall( 'label_custom_image' ),
    'group' => 'map_marker:setup',
  ];

  $control_image_offset = [
    'type'      => 'group',
    'title'     => cs_recall( 'label_image_offset' ),
    'group'     => 'map_marker:setup',
    'condition' => [ 'key' => 'map_marker_image_src', 'op' => 'NOT IN', 'value' => [ '' ] ],
    'controls'  => [
      [
        'key'     => 'map_marker_offset_x',
        'type'    => 'unit-slider',
        'label'   => cs_recall( 'label_x_offset' ),
        'options' => [
          'available_units' => [ '%' ],
          'fallback_value'  => '0%',
          'ranges'          => [ '%' => [ 'min' => -50, 'max' => 50, 'step' => 1 ] ],
        ],
      ],
      [
        'key'     => 'map_marker_offset_y',
        'type'    => 'unit-slider',
        'label'   => cs_recall( 'label_y_offset' ),
        'options' => [
          'available_units' => [ '%' ],
          'fallback_value'  => '-50%',
          'ranges'          => [ '%' => [ 'min' => -50, 'max' => 50, 'step' => 1 ] ],
        ],
      ],
    ],
  ];

  return cs_compose_controls(
    [
      'controls' => [
        $control_setup,
        $control_custom_image,
        $control_image_offset,
      ],
      'control_nav' => [
        'map_marker'       => cs_recall( 'label_primary_control_nav' ),
        'map_marker:setup' => cs_recall( 'label_setup' ),
      ],
    ],

    // Customize / omega tab
    cs_partial_controls( 'omega', [
      'add_hide_during_breakpoints' => false,
      'add_looper_consumer' => true,
      'hide_dom' => true,
    ])
  );

}



// Register Element
// =============================================================================

cs_register_element( 'map-marker', [
  'title'   => __( 'Map Marker', 'cornerstone' ),
  'values'  => $values,
  'builder' => 'x_element_builder_setup_map_marker',
  'render'  => 'x_element_render_map_marker',
  'icon'    => 'native',
  'options' => [
    'valid_parent'   => 'map',
    'alt_breadcrumb' => __( 'Marker', 'cornerstone' ),
  ]
] );
