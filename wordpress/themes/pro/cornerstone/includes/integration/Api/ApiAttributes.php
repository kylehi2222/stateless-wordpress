<?php

/**
 * Attribute editor request type
 */

namespace Cornerstone\API\Attributes;

// Return type setup
cs_api_register_request_type('attributes', [
  'label' => __('Attributes', CS_LOCALIZE),
  'request_filter' => function($body, $type, $data) {
    // Encode setup
    $encode = cs_get_array_value($data, 'attribute_encode', '');

    // I screwed up and should have made the default value ''
    if ($encode === 'default') {
      $encode = '';
    }

    // Used laster in curl-api
    if (empty($encode)) {
      return $body;
    }

    // No body
    if (empty($body)) {
      $body = [];
    }

    // HTTP Build Query
    if ($encode === 'http_build_query') {
      return '?' . http_build_query($body);
    }

    // Default JSON Encode
    return json_encode($body);
  },
  'values' => [
    'attribute_encode' => '',
  ],
  'controls' => [
    [
      'key' => 'args',
      'type' => 'attributes',
    ],

    // Encode Type
    [
      'key' => 'attribute_encode',
      'type' => 'select',
      'label' => __('Encode', CS_LOCALIZE),
      'description' => __('How to encode before sending the attribute data to the endpoint. Default will build a URL query on GET requests and use JSON on anything else', CS_LOCALIZE),
      'options' => [
        'placeholder' => __('Default', CS_LOCALIZE),
        'choices' => [
          [
            'value' => '',
            'label' => __('Default', CS_LOCALIZE),
          ],

          [
            'value' => 'json',
            'label' => 'JSON',
          ],

          [
            'value' => 'http_build_query',
            'label' => __('URL Encode', CS_LOCALIZE),
          ],
        ],
      ],
    ],
  ],
]);
