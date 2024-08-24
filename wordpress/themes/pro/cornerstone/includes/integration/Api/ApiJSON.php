<?php

namespace Cornerstone\API\JSON;

const NAME = "JSON";


// Return type setup
cs_api_register_return_type("json", [
  'label' => NAME,
  'filter' => function($result) {
    $decoded = cs_maybe_json_decode($result);

    // Error
    if ($decoded === null) {
      return [
        'errors' => $result
      ];
    }

    return $decoded;
  },
]);

// Request type
cs_api_register_request_type("json", [
  'label' => NAME,
  //'request_filter' => function($result) {
    //return json_encode($result);
  //},
  'controls' => [
    [
      'key' => 'args',
      'type' => 'code-editor',
      'options' => [
        'mode' => 'json',
        'height' => 4,
        'expandable' => true,
        'is_draggable' => false,
        'header_label' => __("Edit", "cornerstone"),
      ],
    ],
  ],
]);
