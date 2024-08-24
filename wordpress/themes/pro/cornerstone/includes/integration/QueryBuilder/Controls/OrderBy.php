<?php

// Order By control

add_filter("cs_query_builder_orderby_control", function($result, $settings = []) {
  $prefix = cs_get_array_value($settings, 'prefix', 'looper_provider_');

  // Conditions
  // remove query-builder part after move to new Looper Provider API
  $conditions = cs_get_array_value($settings, 'conditions', [
    [
      'looper_provider_type' => 'query-builder'
    ]
  ]);

  $options_omega_group_toggle_asc_desc = [
    'toggle' => [
      'always_show' => true,
      'on'          => 'ASC',
      'off'         => 'DESC',
      'on_label'    => cs_recall( 'label_ascending' ),
      'off_label'   => cs_recall( 'label_descending' ),
    ],
  ];

  return [
    'keys' => [
      'direction' => $prefix . 'query_order',
      'field'     => $prefix . 'query_orderby',
    ],
    'type'    => 'group-picker',
    'label'   => cs_recall( 'label_order_by' ),
    'options' => [
      'icon'  => 'order',
      'label' => '{{orderby:field,direction}}',
    ],
    'conditions' => $conditions,
    'controls'   => [
      [
        'key'      => $prefix . 'query_order',
        'type'     => 'group',
        'label'    => cs_recall( 'label_field' ),
        'options'  => $options_omega_group_toggle_asc_desc,
        'controls' => [
          // Order by type
          [
            'key'     => $prefix . 'query_orderby',
            'type'    => 'select',
            'options' => [
              'choices' => cornerstone( 'Locator' )->get_orderby_options()
            ],
          ],

          // Meta Key if order type is valid
          [
            'key' => $prefix . 'query-builder_orderby_meta_key',
            'label' => __('Meta Key', 'cornerstone'),
            'type' => 'select',
            'options' => [
              'choices' => 'dynamic:postmeta:ignore-post',
              'placeholder' => 'Select',
            ],
            'conditions' => [
              [
                'key' => $prefix . 'query_orderby',
                'op' => 'IN',
                'value' => ['meta_value', 'meta_value_num'],
              ]
            ],
          ],
        ],
      ],
    ],
  ];
}, 0, 2);
