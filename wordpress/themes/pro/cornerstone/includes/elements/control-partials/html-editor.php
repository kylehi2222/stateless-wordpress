<?php

namespace Themeco\ControlPartials\HTMLEditor;

/**
 * HTML Editor control partial
 */

cs_register_control_partial( 'html-editor', function($settings = []) {

  // Setup
  // -----

  $key = ( isset( $settings['key'] ) ) ? $settings['key'] : '';

  // Output
  // ------

  return [
    'key' => $key,
    'type' => 'text-editor',
    'options' => [
      'mode' => 'html',
      'expandable' => true,
      'height' => 8,
      'is_draggable' => false,
      'no_rich_text' => true,
      'button_label' => cs_recall( 'label_edit' ),
      'header_label' => cs_recall( 'label_html' ),
    ],
  ];
});
