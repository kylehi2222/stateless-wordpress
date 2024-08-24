<?php

require_once(__DIR__ . "/../../classes/Services/IconRepository.php");

use \Themeco\Cornerstone\Services;

// =============================================================================
// CORNERSTONE/INCLUDES/ELEMENTS/DEFINITIONS/RAW-CONTENT.PHP
// -----------------------------------------------------------------------------
// V2 element definitions.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Values
//   02. Render
//   03. Builder Setup
//   04. Register Element
// =============================================================================

// Values
// =============================================================================

$values = cs_compose_values(
  [
    'disable_preview' => cs_value( false, 'markup', true ),
    'raw_content'     => cs_value( '', 'markup:seo', true ),
    'render_blocks'   => cs_value( false, 'markup', true ),
  ],
  'omega:show-condition',
  'omega:looper-consumer',
  'omega:looper-provider'
);



// Render
// =============================================================================

function x_element_render_raw_content( $data ) {
  if ( $data['disable_preview'] && ( did_action( 'cs_element_rendering' ) || apply_filters( 'cs_is_preview', false ) || did_action( 'cs_element_rendering' ) ) ) {
    $icon = Themeco\Cornerstone\Services\IconRepository::getWithCSSClass("shared-cube", "tco-svg tco-svg-elements");
    return "<div><div class='tco-empty-element'><div class='tco-empty-element-icon'>{$icon}</div></div></div>";
  }

  $content = ( isset( $data['raw_content'] ) )
    ? $data['raw_content']
    : '';

  $content = !empty($data['render_blocks']) && !empty($content)
    ? do_blocks($content)
    : $content;

  // Raw content in preview
  // Add in builder atts so it works with loopers better
  if (did_action( 'cs_element_rendering' )) {
    return cs_tag('div', $data['_builder_atts'], $content);
  }

  return $content;
}



// Builder Setup
// =============================================================================

function x_element_builder_setup_raw_content() {
  return cs_compose_controls(
    [
      'controls' => [
        [
          'type'     => 'group',
          'label'    => cs_recall( 'label_setup' ),
          'group'    => 'raw_content:setup',
          'controls' => [
            [
              'key'     => 'disable_preview',
              'type'    => 'choose',
              'label'   => cs_recall( 'label_preview' ),
              'options' => [
                'choices' => [
                  [ 'value' => false, 'label' => cs_recall( 'label_allow' )   ],
                  [ 'value' => true,  'label' => cs_recall( 'label_disable' ) ],
                ]
              ],
            ],
            [
              'key'     => 'render_blocks',
              'type'    => 'toggle',
              'description' => __('This will render blocks when this element is rendered, as opposed to after all rendering has occurred', 'cornerstone'),
              'label'   => __('Render Blocks', 'cornerstone'),
            ],

            // HTML Editor for raw_content
            cs_partial_controls("html-editor", [
              'key' => 'raw_content',
            ]),

          ],
        ],
      ],
      'control_nav' => [
        'raw_content'       => cs_recall( 'label_primary_control_nav' ),
        'raw_content:setup' => '',
      ]
    ],
    cs_partial_controls( 'omega', [
      'add_hide_during_breakpoints' => false,
      'add_looper_consumer' => true,
      'add_looper_provider' => true,
      'hide_dom' => true,
    ])
  );
}



// Register Element
// =============================================================================

cs_register_element( 'raw-content', [
  'title'   => __( 'Raw Content', 'cornerstone' ),
  'values'  => $values,
  'builder' => 'x_element_builder_setup_raw_content',
  'render'  => 'x_element_render_raw_content',
  'icon'    => 'native',
  'group'   => 'advanced',
] );





/*

The following could be used to prevent the_content from running on raw content
It shouldn't be needed after the cs_expand_content refactor though

$raw_content = cs_expand_content( $data['raw_content'] );

if ( doing_filter('the_content') ) {

  $tag = '%%%preserve:' . uniqid() . '%%%';
  $raw_content = $data['raw_content'];
  $preserve_content = function ($content) use (&$preserve_content, $raw_content) {
    remove_filter( 'the_content', $preserve_content, '100' );
    return str_replace($tag, $raw_content, $content);
  };

  add_filter( 'the_content', $preserve_content, '100' );
  return $tag;
}

*/
