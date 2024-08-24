<?php

namespace Cornerstone\Api\DynamicContent;

use function Cornerstone\Api\Controls\controls_global_lite;

add_action( 'cs_dynamic_content_register', function() {
  // Register Group
  cornerstone_dynamic_content_register_group([
    'name'  => 'api',
    'label' => __('API'),
  ]);

  // {{dc:api:call}}
  //cornerstone_dynamic_content_register_field([
    //'name'  => 'call',
    //'group' => 'api',
    //'type'  => 'mixed',
    //'label' => __('API Call'),
    //'controls' => controls(),
    //'deep' => true,
  //]);

  // {{dc:api:global}}
  cornerstone_dynamic_content_register_field([
    'name'  => 'call',
    'group' => 'api',
    'type'  => 'mixed',
    'label' => __('Global', "cornerstone"),
    'controls' => controls_global_lite(),
    'filter' => function($results, $args) {
      $out = cs_api_global_run($args);

      return empty($out)
        ? ""
        : $out;
    },
    'deep' => true,
  ]);

  // Dynamic Options API Global
  cs_dynamic_content_register_dynamic_option("api_global", [
    'key' => "api_global",
    'type' => "select",
    'label' => __("API Global", CS_LOCALIZE),
    'options' => [
      'choices' => "dynamic:api_global",
      'placeholder' => __("Select a Global", CS_LOCALIZE),
    ],
    'filter' => function() {
      $endpoints = cs_api_global_endpoints();
      $asChoices = [];

      foreach ($endpoints as $endpoint) {
        $asChoices[] = [
          'value' => $endpoint['id'],
          'label' => $endpoint['name'],
        ];
      }

      return $asChoices;
    },
  ]);
}, -1000);
