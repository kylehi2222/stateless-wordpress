<?php

// Register Connection DC
add_action('cs_dynamic_content_setup', function() {
  // Register group
  cornerstone_dynamic_content_register_group([
    'name'  => 'connection',
    'label' => __( 'Connection', CS_LOCALIZE ),
  ]);

  // IP Address
  cornerstone_dynamic_content_register_field([
    'name'  => 'ip',
    'group' => 'connection',
    'type' => 'scalar',
    'label' => __( 'IP Address', CS_LOCALIZE ),
  ]);

}, 220);

add_filter( 'cs_dynamic_content_connection', function($result, $field, $args = []) {

  switch ($field) {
    case 'ip':
      $result = cs_get_array_value($_SERVER, 'REMOTE_ADDR', '');
      break;
  }

  return $result;

}, 10, 3 );
