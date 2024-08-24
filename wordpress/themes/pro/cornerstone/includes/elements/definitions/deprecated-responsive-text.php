<?php

// =============================================================================
// CORNERSTONE/INCLUDES/ELEMENTS/DEFINITIONS/CONTENT-AREA.PHP
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

$values = [
  'selector'     => cs_value( '.h-responsive', 'markup', true ),
  'compression'  => cs_value( '1.0', 'markup', true ),
  'min_size'     => cs_value( '24', 'markup', true ),
  'max_size'     => cs_value( '96', 'markup', true )
];



// Render
// =============================================================================

function x_element_render_responsive_text( $data ) {

  $atts = cs_element_js_atts( 'responsive-text', [
    'selector'    => $data['selector'],
    'compression' => $data['compression'],
    'minFontSize' => $data['min_size'],
    'maxFontSize' => $data['max_size']
  ], true );

  $atts['class'] = $data['classes'];

  return cs_tag('span', $atts, '');
}



// Builder Setup
// =============================================================================

function x_element_builder_setup_responsive_text() {
  return cs_compose_controls(
    [
      'controls' => [
        [
          'type'     => 'group',
          'label'    => cs_recall( 'label_setup' ),
          'group'    => 'responsive_text:setup',
          'controls' => [
            [
              'key'     => 'selector',
              'type'    => 'text',
              'label'   => cs_recall( 'label_selector' ),
              'options' => [
                'placeholder' => '.h-responsive'
              ],
            ],
            [
              'key'     => 'compression',
              'type'    => 'text',
              'label'   => cs_recall( 'label_compress' ),
              'options' => [
                'placeholder' => '1.0'
              ],
            ],
            [
              'key'     => 'min_size',
              'type'    => 'text',
              'label'   => cs_recall( 'label_min' ),
              'options' => [
                'placeholder' => '24'
              ],
            ],
            [
              'key'     => 'max_size',
              'type'    => 'text',
              'label'   => cs_recall( 'label_max' ),
              'options' => [
                'placeholder' => '96'
              ],
            ],
          ],
        ],
      ],
      'control_nav' => [
        'responsive_text'       => cs_recall( 'label_primary_control_nav' ),
        'responsive_text:setup' => '',
      ]
    ]
  );
}





// Register Element
// =============================================================================

cs_register_element( 'responsive-text', [
  'title'      => __( 'Responsive Text', '__x__' ),
  'values'     => $values,
  'builder'    => 'x_element_builder_setup_responsive_text',
  'render'     => 'x_element_render_responsive_text',
  'icon'       => 'native',
  'group'      => 'deprecated',
  'options'    => [
    'empty_placeholder' => false,
    'templates' => false
  ]
] );
