<?php

// Grabs theme option by field
// {{dc:theme:*}}
add_filter('cs_dynamic_content_theme', function( $result, $field, $args) {
  if (!empty($args['destination']) && $args['destination'] === "font-family") {
    $value = cs_stack_get_value($field);

    $postProcessed = apply_filters('cs_css_post_process_font-family', $value);
    return $postProcessed;
  }

  $processed = cornerstone_post_process_value(
    cs_stack_get_value($field)
  );

  // Suffix unit value
  // If changed from float to string it probably means there is a unit
  // somewhere
  if (!empty($args['unit']) && (string)((float)$processed) === (string)$processed) {
    $processed .= $args['unit'];
  }

  return $processed;
}, 10, 3 );

// Register Theme option DC UI
add_action('cs_dynamic_content_setup', function() {
  // Register group
  cornerstone_dynamic_content_register_group([
    'name'  => 'theme',
    'label' => __( 'Theme Option', CS_LOCALIZE ),
  ]);

  cornerstone_dynamic_content_register_field([
    'name'  => 'option',
    'group' => 'theme',
    'type' => 'mixed',
    'label' => __( 'Option', CS_LOCALIZE ),
    'controls' => ['themeoption',],
    'deep' => true,
    'twig_format' => '{{ theme.$themeoption }}',
    'format' => '{{dc:theme:$themeoption}}'
  ]);

  cs_dynamic_content_register_dynamic_option("themeoption", [
    'key' => "themeoption",
    'type' => "select",
    'label' => __("Option", CS_LOCALIZE),
    'options' => [
      'customLabel' => __("Custom Theme Option", CS_LOCALIZE),
      'choices' => "dynamic:themeoption",
      'placeholder' => __("Enter Theme Option", CS_LOCALIZE),
    ],
  ]);

}, 200);


/**
 * Get theme options select
 */
add_filter( 'cs_dynamic_options_themeoption', function($output = []) {

  $keys = cs_stack_keys();
  sort($keys);

  foreach ($keys as $key) {
    $output[] = [
      'value' => $key,
      'label' => $key,
    ];
  }

  return $output;
}, 10, 2);
