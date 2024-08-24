<?php

// =============================================================================
// CORNERSTONE/INCLUDES/ELEMENTS/DEFINITIONS/GLOBAL-BLOCK.PHP
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
    'global_block_id' => cs_value( '', 'markup', true ),
    'no_wrap' => cs_value( false, 'markup', true ),
  ],
  'omega',
  'omega:custom-atts'
);


// Render
// =============================================================================

function x_element_render_component( $data ) {

  $content = '';

  $global_block_id = apply_filters( 'cs_global_block_id', $data['global_block_id'] );

  $is_preview = did_action( 'cs_element_rendering' );

  // Prepare Attr Values
  // -------------------

  $classes = [ 'cs-content', 'x-global-block', "x-global-block-$global_block_id", ];

  // Prepare Atts
  // ------------

  $atts = [ 'class' => array_merge( $classes, $data['classes'] ) ];

  if ( isset( $data['id'] ) && ! empty( $data['id'] ) ) {
    $atts['id'] = $data['id'];
  }

  if ( isset( $data['style'] ) && ! empty( $data['style'] ) ) {
    $atts['style'] = $data['style'];
  }


  // Validation
  // ----------

  $error = false;

  if ( ! $global_block_id ) {
    return;
  } else {
    $global_block_post = get_post( $global_block_id );
    if ( ! is_a($global_block_post, 'WP_Post' ) ) {
      $error = 'Unable to locate Global Block : ' . ((int)$global_block_id);
    }
  }

  // Prepare Content
  // ---------------
  // 01. Start Rendering Isolation.
  // 02. End Rendering Isolation.

  if ( ! $error ) {

    $gb_top_level = false; // 01

    if ( ! apply_filters( '_cs_rendering_global_block', false ) ) {
      $gb_top_level = true;
      add_filter('_cs_rendering_global_block', '__return_true' );
    }


    $resolver = cornerstone('Resolver');
    $global_block_document = $resolver->getDocument( $global_block_id );

    // Use individual component in document
    if (!empty($data['component_id'])) {
      $global_block_document->setComponentOutputId($data['component_id']);

      // Set parameters if passed
      if (!empty($data['_p_data'])) {
        $global_block_document->setComponentOutputParameters($data['_p_data']);
      }
    }

    $content = '';

    if ($global_block_document) {
      try {
        $content = $resolver->renderContentFromDocument($global_block_document);

        // Needed if grabbing again with different params
        // and component
        if (!empty($data['component_id'])) {
          $resolver->clearDocumentCache($global_block_id);
        }
      } catch( Exception $e ) {
        $error = $e->getMessage();
      }
    } else {
      // No global block
      $error = "This Global Block is not found {$global_block_id}.";
    }

    if ( strpos( $content, '<!-- the_content loop -->' ) === 0 ) {
      $error = 'Global Blocks can not reference themselves';
    }
    if ( ! $content && $is_preview && empty($error) ) {
      $error = 'This Global Block does not have any content.';
    }

    if ( $gb_top_level ) { // 02
      remove_filter('_cs_rendering_global_block', '__return_true' );

      if ( $is_preview ) {
        $atts['data-cs-nav-btn'] = cs_prepare_json_att( array(
          'action' => array(
            'route'   => "edit/$global_block_id",
            'context' => csi18n( 'common.document.component' )
          ),
          'label' => sprintf( csi18n( 'common.edit' ), csi18n( 'common.document.component' ) ),
          'icon' => 'edit'
        ) );
      }

    }
  }

  if ( $error ) {
    $content = apply_filters( 'cs_global_block_error', "<div style=\"padding: 35px; line-height: 1.5; text-align: center; color: #000; background-color: #fff;\">$error</div>");
  }


  // Output
  // ------

  // No wrapping div
  if (!empty($data['no_wrap'])) {
    return $content;
  }

  return cs_tag('div', apply_filters( 'cs_global_block_atts', $atts, $global_block_id ), isset( $data['custom_atts'] ) ? $data['custom_atts'] : [], $content );

}



// Builder Setup
// =============================================================================

function x_element_builder_setup_component() {

  return cs_compose_controls(
    [
      'controls' => [
        [
          'type'     => 'group',
          'group'    => 'global_block:setup',
          'controls' => [
            [
              'key'     => 'global_block_id',
              'type'    => 'post-picker',
              'label'   => cs_recall( 'label_select' ),
              'options' => [
                'post_type'         => 'cs_global_block',
                'post_status'       => 'tco-data',
                'empty_placeholder' => cs_recall( 'label_no_components' ),
                'placeholder'       => __( 'Component', 'cornerstone' )
              ],
            ],
          ],
        ],
      ],
      'control_nav'          => [
        'global_block'       => cs_recall( 'label_primary_control_nav' ),
        'global_block:setup' => cs_recall( 'label_setup' )
      ],
    ],
    cs_partial_controls( 'omega', [
      'add_custom_atts' => true,
      'add_presets' => false,
    ])
  );

}

// Without this it will not run DC on custom CSS
function x_element_tss_component_direct() {
  return [
    'modules' => [
      'global-block',
    ]
  ];
}

// Register Element
// =============================================================================

cs_register_element( 'global-block', [
  'title'      => __( 'Component', 'cornerstone' ),
  'values'     => $values,
  'builder'    => 'x_element_builder_setup_component',
  'render'     => 'x_element_render_component',
  'tss'        => 'x_element_tss_component_direct',
  'icon'       => 'native',
  'group'      => 'content',
  'options'    => [
    'preview_nav' => true,
  ],
] );
