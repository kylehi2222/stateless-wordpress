<?php

/**
 * Toggleable shared controls
 */

cs_register_control_partial( 'toggleable', function($settings = []) {
  $prefix = cs_get_array_value($settings, 'prefix', '');

  return [
    cs_partial_controls('rvt:dynamic-rendering', [
      'key' => $prefix . 'content_dynamic_rendering',
    ]),

    [
      'key' => $prefix . 'esc_key_close',
      'label' => __('Esc Key Close', 'cornerstone'),
      'description' => __('When the toggleable is open, pressing the esc key will close the last opened toggleable', 'cornerstone'),
      'type' => 'toggle',
    ],

    [
      'key' => $prefix . 'direct_close',
      'label' => __('Direct Close', 'cornerstone'),
      'description' => __('Clicking or touching outside of the content area will close the toggleable automatically', 'cornerstone'),
      'type' => 'toggle',
    ],
  ];
});
