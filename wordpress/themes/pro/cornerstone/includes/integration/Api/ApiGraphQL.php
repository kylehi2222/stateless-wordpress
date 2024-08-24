<?php

namespace Cornerstone\API\JSON;


// Request type
cs_api_register_request_type("graphql", [
  'label' => __("GraphQL", "cornerstone"),
  'controls' => [
    // Operation Name
    [
      'key' => 'graphql_operationName',
      'label' => __("Operation", "cornerstone"),
      'description' => __("Query or Mutation name. This is not always required, but can help debug on the other servers end", "cornerstone"),
      'type' => 'text',
    ],

    // Query
    [
      'key' => 'graphql_query',
      'type' => 'code-editor',
      'options' => [
        'mode' => 'graphql',
        'height' => 4,
        'is_draggable' => false,
        'expandable' => true,
        'header_label' => __("GraphQL Query", "cornerstone"),
      ],
    ],

    // Variables
    [
      'key' => 'args',
      'type' => 'code-editor',
      'options' => [
        'mode' => 'json',
        'height' => 4,
        'is_draggable' => false,
        'expandable' => true,
        'header_label' => __("Variables JSON", "cornerstone"),
      ],
    ],
  ],

  'values' => [
    'graphql_query' => '',
    'graphql_operationName' => '',
  ],

  // Filter request prior to sending
  'request_filter' => function($body, $type, $data) {
    // Output in graphql style
    $out = [
      'variables' => $body,
      'query' => (string)$data['graphql_query'],
    ];

    // Operation fails in some api
    // if sent as null or empty
    $operationName = trim(cs_get_array_value($data, 'graphql_operationName', ''));
    if (!empty($operationName) && $operationName !== '') {
      $out['operationName'] = $operationName;
    }

    return $out;
  },
]);
