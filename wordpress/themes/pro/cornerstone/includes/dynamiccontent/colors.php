<?php

namespace Cornerstone\DynamicContent\Colors;

// For reference in option fields
const GLOBAL_COLOR_TYPE = "globalcolor";


// Register Theme option DC UI
add_action('cs_dynamic_content_setup', function() {
  // Global Color
  cornerstone_dynamic_content_register_field([
    'name'  => 'color',
    'group' => 'global',
    'type' => 'mixed',
    'label' => __( 'Color', CS_LOCALIZE ),
    'controls' => [

      // Type
      [
        'key' => 'id',
        'type' => 'select',
        'label' => __('Color', CS_LOCALIZE),
        'options' => [
          'choices' => 'dynamic:' . GLOBAL_COLOR_TYPE,
          'placeholder' => __('Enter Color ID', CS_LOCALIZE),
        ],
      ]

    ],
    'deep' => true,
  ]);

  // Dynamic Option for Global Color
  cs_dynamic_content_register_dynamic_option(GLOBAL_COLOR_TYPE, [
    'key' => GLOBAL_COLOR_TYPE,
    'type' => "select",
    'label' => __("Global Color", CS_LOCALIZE),
    'options' => [
      'choices' => "dynamic:" . GLOBAL_COLOR_TYPE,
      'placeholder' => __("Enter Color ID", CS_LOCALIZE),
    ],
  ]);

}, 200);

/**
 * Dynamic Option for all colors
 */
add_filter('cs_dynamic_options_' . GLOBAL_COLOR_TYPE, function($output = []) {
  $colors = cs_color_get_all();

  $choices = [];

  foreach ($colors as $color) {
    $choices[] = [
      'label' => $color['title'],
      'value' => $color['_id'],
    ];
  }

  return $choices;
});
