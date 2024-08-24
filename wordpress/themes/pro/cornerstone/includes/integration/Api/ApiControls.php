<?php

namespace Cornerstone\Api\Controls;

use const Themeco\Cornerstone\API\METHOD_CHOICES;

function controls($settings = []) {
  $showHTTPControls = cs_get_array_value($settings, 'show_http_controls', true);
  $showEndpoint = cs_get_array_value($settings, 'show_endpoint', true);

  return array_merge(
    [
      [
        'key' => 'run',
        'type' => 'toggle',
        'label' => __("Run", "cornerstone"),
        'description' => __("If you are not ready to run the endpoint uncheck this. This will help to not incur API charges, or do something by accident", "cornerstone"),
      ],

      // Endpoint
      $showEndpoint
        ? [
          'key' => 'endpoint',
          'type' => 'text',
          'label' => __("Endpoint", "cornerstone"),
          'description' => __("URL including the protocol. Can include a path as well", "cornerstone"),
        ]
        : null
      ,

      // Path control
      control_path(),

      // Method
      [
        'key' => 'method',
        'type' => 'select',
        'label' => __("Method", "cornerstone"),
        'options' => [
          'choices' => array_merge(
            [
              [
                'value' => '',
                'label' => __('Default (Global)', 'cornerstone'),
              ],
            ],
            METHOD_CHOICES
          ),
        ],
      ],

      // Headers
      [
        'key' => 'headers',
        'label' => __("Headers", "cornerstone"),
        'type' => 'attributes',
      ],
    ],

    // Request type controls
    cs_api_request_type_controls(),

    // Return type controls
    cs_api_return_type_controls(),

    // Second half of controls
    // after request type controls
    [
      // Data Key
      control_data_key(),
    ],

    // HTTP Controls
    $showHTTPControls ? [

      // Cache Time
      cs_partial_controls('seconds-select', [
        'key' => 'cache_time',
        'label' => __("Cache Time", "cornerstone"),
        'description' => __("Time in seconds to store results of API result. Leave empty to never cache", "cornerstone"),
      ]),

      // Follow Redirect
      [
        'key' => 'follow_redirect',
        'label' => __("Follow Redirect", "cornerstone"),
        'description' => __("If the API sends you to another URL, follow that and get content from the redirect", "cornerstone"),
        'type' => 'toggle',
      ],

      // Timeout
      [
        'key' => 'timeout',
        'label' => __("Timeout", "cornerstone"),
        'description' => __("Time in seconds to wait before giving up on any given request", "cornerstone"),
        'type' => 'text',
      ],

      // HTTP Connect Timeout
      [
        'key' => 'httpconnect_timeout',
        'label' => __("HTTP Timeout", "cornerstone"),
        'description' => __("Time in seconds to wait before giving up on receiving the headers of an HTTP endpoint", "cornerstone"),
        'type' => 'text',
      ],

    ]
    // Or no http controls
    : [],

    // Debug
    [
      [
        'key' => 'debug',
        'label' => __("Debug", "cornerstone"),
        'description' => __("When in debug, will send all response status info in the 'info' key and the actual response in the 'response' key. This requires setting up another Dynamic Content Looper after this one", "cornerstone"),
        'type' => 'toggle',
      ]
    ]
  );
}

// From a global
function controls_global() {

  return array_merge(
    // Global select
    [
      control_global_select(),
    ],

    // Main controls
    controls([
      'show_http_controls' => false,
      'show_endpoint' => false,
    ]),
  );

}

function controls_global_lite() {
  return [
    // Global select
    control_global_select(),

    control_path(),

    control_data_key(),
  ];
}

// Global select
function control_global_select() {
  return [
    'type' => 'select',
    'label' => __("API Global", "cornerstone"),
    'description' => __("Controls will merge or overwrite with your Global in Theme Options depending on the value type", "cornerstone"),
    'key' => 'global_id',
    'options' => [
      'placeholder' => __('Select a Global', "cornerstone"),
      'choices' => 'dynamic:api_global',
    ],
  ];
}

// Path Control
function control_path() {
  return [
    'key' => 'path',
    'type' => 'text',
    'label' => __("Path", "cornerstone"),
    'description' => __("Added directly to the endpoint. Not required if using full path in the endpoint", "cornerstone"),
  ];
}

// Data Key
function control_data_key() {
  return [
    'key' => 'data_key',
    'label' => __("Data Key", "cornerstone"),
    'description' => __("If sent an object from your API, which key in that object would you like to use. Leave empty to ignore", "cornerstone"),
    'type' => 'text',
  ];
}

// Control partial
cs_register_control_partial( 'api-main', __NAMESPACE__ . '\\controls');
