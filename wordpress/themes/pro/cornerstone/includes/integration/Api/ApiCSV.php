<?php

namespace Cornerstone\API\CSV;


// Return type setup
cs_api_register_return_type("csv", [
  'label' => __("CSV", "cornerstone"),

  // @see integration/csv
  'filter' => function($result, $type, $args = []) {
    $data = cs_csv_parse($result, $args);
    return $data;
  },

  'values' => [
    'content' => '',
    'has_header' => true,
    'delimiter' => ',',
  ],

  // Controls
  // @see csv/controls
  'controls' => cs_csv_controls([
    'has_input' => false,
  ]),
]);
