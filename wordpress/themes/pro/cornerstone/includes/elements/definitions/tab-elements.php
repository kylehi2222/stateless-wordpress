<?php

// =============================================================================
// CORNERSTONE/INCLUDES/ELEMENTS/DEFINITIONS/TAB.PHP
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
    'tab_label_content'       => cs_value( __( 'Tab', 'cornerstone' ), 'markup:seo', true ),
    'tab_label_custom_atts'   => cs_value( '', 'markup' ),
    'tab_content_custom_atts' => cs_value( '', 'markup' ),
  ],
  'omega',
  'omega:toggle-hash',
  'omega:looper-provider',
  'omega:looper-consumer'
);


// Builder Setup
// =============================================================================

function x_element_builder_setup_tab_elements() {

  $control_setup = [
    'type'     => 'group',
    'title'    => cs_recall( 'label_content' ),
    'group'    => 'tab:setup',
    'controls' => [
      [
        'key'     => 'tab_label_content',
        'type'    => 'text-editor',
        'label'   => cs_recall( 'label_label' ),
        'options' => [
          'height' => 1,
        ],
      ],
    ],
  ];

  return cs_compose_controls(
    [
      'controls' => [
        $control_setup,
        [
          'key'        => 'tab_label_custom_atts',
          'type'       => 'attributes',
          'group'      => 'omega:setup',
          'label'      => cs_recall( 'label_custom_attributes_with_prefix' ),
          'label_vars' => [ 'prefix' => cs_recall( 'label_label' ) ]
        ],
        [
          'key'        => 'tab_content_custom_atts',
          'type'       => 'attributes',
          'group'      => 'omega:setup',
          'label'      => cs_recall( 'label_custom_attributes_with_prefix' ),
          'label_vars' => [ 'prefix' => cs_recall( 'label_content' ) ]
        ],
        [
          'group'    => 'tab:elements',
          'type' => 'children',
        ],
      ],
      'control_nav' => [
        'tab'       => cs_recall( 'label_primary_control_nav' ),
        'tab:setup' => cs_recall( 'label_setup' ),
        'tab:elements' => cs_recall( 'label_elements' ),
      ]
    ],
    cs_partial_controls( 'omega', [
      'add_custom_atts' => true,
      'add_toggle_hash' => true,
      'add_looper_provider' => true,
      'add_looper_consumer' => true
    ])
  );

}



function x_element_render_tab_elements($data = []) {
  // To match what tab sends
  $data['tab_content'] = cs_render_child_elements($data);

  // Unset modules as they are no longer needed
  $data['_modules'] = [];

  // Preview dropzone support
  if (did_action('cs_element_rendering') && ! apply_filters( 'cs_render_looper_is_virtual', false )) {
    $contentAtts = [];
    $contentAtts['data-cs-dropzone'] = $data['_id'];
    $contentAtts['data-cs-observe'] = 'true';
    $contentAtts['data-cs-observeable-id'] = $data['_id'];

    $data['tab_preview_atts'] = $contentAtts;
  }

  return cs_render_function_as_array($data);
}


// Register Element
// =============================================================================

cs_register_element( 'tab-elements', [
  'title'   => __( 'Tab (Elements)', 'cornerstone' ),
  'values'  => $values,
  'builder' => 'x_element_builder_setup_tab_elements',
  'render'  => 'x_element_render_tab_elements',
  'icon'    => 'native',
  'group'   => 'structure',
  'options' => [
    'valid_children' => '*',
    'shadow_parent' => true,
    'valid_parent'  => 'tabs',
    'label_key'     => 'tab_label_content',

    'dropzone' => [
      'enabled' => true,
    ],

    'default_children' => [
      [
        '_type' => 'text',
        'text_content' => __( 'This is tab content!', 'cornerstone' )
      ],
    ],
  ]
] );
