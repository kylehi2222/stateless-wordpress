<?php

/**
 * RVT reset or dynamic rendering
 */

cs_register_control_partial( 'rvt:dynamic-rendering', function($settings = []) {
  $key = cs_get_array_value($settings, 'key', 'dynamic_rendering');

  return [
    'key' => $key,
    'label' => __("Dynamic Rendering", "cornerstone"),
    'type' => 'toggle',
    'description' => __("Will reset the state of video and other supported elements inside the element", "cornerstone"),
  ];
});
