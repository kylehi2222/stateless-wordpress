<?php

// Request type
cs_api_register_request_type("raw", [
  'label' => __("Raw", "cornerstone"),
  'controls' => [
    [
      'key' => 'args',
      'type' => 'text-editor',
      'options' => [
        'mode' => 'text',
        'expandable' => true,
        'only_raw' => true,
        'height' => 4,
        'header_label' => __("Edit", "cornerstone"),
      ],
    ],
  ],
]);

// Request type
cs_api_register_return_type("raw", [
  'label' => __("Raw", "cornerstone"),
  'filter' => function($result) {
    return $result;
  },
]);
