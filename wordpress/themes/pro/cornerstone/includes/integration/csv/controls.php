<?php

function cs_csv_controls($settings = []) {
  $hasInput = (bool) cs_get_array_value($settings, 'has_input', true);

  $out = $hasInput
    ? cs_csv_input_controls($settings)
    : [];

  $out = array_merge($out, [
    // Has Headers
    [
      'key' => 'has_header',
      'label' => __('Has Header', 'cornerstone'),
      'description' => __('Is the first row a list of header / column titles? Will use integers as the keys of the CSV if not, and the first row will be looped over', 'cornerstone'),
      'type' => 'toggle',
    ],

    // Delimiter
    [
      'key' => 'delimiter',
      'label' => __('Delimiter', 'cornerstone'),
      'description' => __('Which character delimits each column of data. This is typically a comma (,) or pipe (|)', 'cornerstone'),
      'type' => 'text',
    ],
  ]);

  return $out;
}

/**
 * CSV Input control
 * file, raw
 */
function cs_csv_input_controls($settings = []) {

  return [
    // Type of CSV content
    [
      'key' => 'type',
      'label' => __('Type', 'cornerstone'),
      'type' => 'choose',
      'options' => [
        'choices' => [
          [
            'value' => 'file',
            'label' => __('File', 'cornerstone'),
          ],
          [
            'value' => 'content',
            'label' => __("Content", "cornerstone"),
          ],
        ]
      ],
    ],

    // File Input
    [
      'key' => 'file',
      'type' => 'file',
      'label' => __('File', 'cornerstone'),
      'options' => [
        'file_types' => ['text/csv'],
      ],
      'conditions' => [
        [
          'key' => 'type',
          'op' => '==',
          'value' => 'file',
        ],
      ],
    ],

    // Content / Text
    [
      'key' => 'content',
      //'type' => 'textarea',
      'type' => 'text-editor',
      'options' => [
        'mode' => 'text',
        'expandable' => true,
        'only_raw' => true,
        'height' => 6,
      ],
      'conditions' => [
        [
          'key' => 'type',
          'op' => '==',
          'value' => 'content',
        ]
      ],
    ],

  ];
}
