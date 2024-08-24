<?php

namespace Cornerstone\WordPress\QueryBuilder;


/**
 * Meta values controls
 */
add_filter("cs_query_builder_meta_value_control", function() {
  // Condition
  // @TODO not needed after looperprovider move
  $condition_omega_provider_is_query_builder = [
    'looper_provider_type' => 'query-builder'
  ];

  return [
    'type' => 'group-picker',
    'keys' => [
      'field' => 'looper_provider_query-builder_meta_values',
    ],
    'label'   => __("Meta Values", CS_LOCALIZE),
    'options' => [
      'icon'  => 'dev',
      'label' => __('Meta Values', CS_LOCALIZE) . ' ({{length:field}})',
    ],
    'conditions' => [ $condition_omega_provider_is_query_builder ],

    // Group controls
    'controls'   => [

      // Inside group for styling
      [
        'type' => 'group',
        'controls' => apply_filters("cs_query_builder_meta_value_controls", []),
      ],

    ],
  ];
});

// Meta Query controls inside picker
add_filter("cs_query_builder_meta_value_controls", function() {

  return [
    // Relation
    cs_partial_controls('and-or', [
      'key' => 'looper_provider_query-builder_meta_relation',
      'label' => __('Relation', CS_LOCALIZE),
    ]),

    // Meta value editor
    [
      'key'   => 'looper_provider_query-builder_meta_values',
      'type'  => 'list',
      'label' => __("Meta Values", CS_LOCALIZE),
      'options' => [
        'item_label' => '{{key}} {{compare}} {{value}}',
        'initial' => [
          'key' => '_cornerstone_data',
          'value' => '',
          'compare' => 'EXISTS',
          'orderby' => false,
          'orderby_direction' => 'DESC',
        ],
      ],
      'controls' => [

        // Key
        [
          'key' => 'key',
          'label' => __("Meta Key", CS_LOCALIZE),
          'type' => 'select',
          'options' => [
            // Uses :ignore-post
            'choices' => 'dynamic:postmeta:ignore-post',
          ],
        ],

        // Compare
        cs_partial_controls('comparison-select', [
          'key' => 'compare',
        ]),

        // Value
        [
          'key' => 'value',
          'label' => __("Meta Value", CS_LOCALIZE),
          'type' => 'text',
        ],

        // Order By
        [
          'key' => 'orderby',
          'label' => __("Order By", CS_LOCALIZE),
          'description' => __("You may need to set this queries 'Order By' to 'Ignore' depending on the type of order value", CS_LOCALIZE),
          'type' => 'toggle',
        ],

        // Direction
        cs_partial_controls("sql-direction", [
          'key' => 'orderby_direction',
          'conditions' => [
            [
              'key' => 'orderby',
              'op' => '==',
              'value' => true,
            ]
          ]
        ]),
      ],

    ],
  ];
});
