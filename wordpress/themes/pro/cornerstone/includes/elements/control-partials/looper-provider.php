<?php

/**
 * Looper provider output
 *
 * @TODO every builtIn control and choice should be moved to Looper API
 */

namespace Cornerstone\ControlPartials\LooperProvider;

// Built control group
function controls($settings = []) {
  $group = cs_get_array_value($settings, 'group', '');
  $conditions = cs_get_array_value($settings, 'conditions', []);

  return [
    'keys'     => [
      'toggle' => 'looper_provider',
      'select' => 'looper_provider_type'
    ],
    'type'    => 'group',
    'label'   => cs_recall( 'label_looper_provider' ),
    'group'   => $group,
    'options' => [
      'toggle' => [
        'on'        => true,
        'off'       => false,
        'off_value' => cs_recall( 'label_off' ),
        'choices'   => choices(),
      ],
    ],
    'conditions'  => $conditions,
    'description' => __( 'Begin a new dynamic content data source to loop over.', '__x__' ),
    'controls'    => controlArray(),
  ];
}

function controlArray() {

  // Conditions
  // ----------

  $condition_omega_provider_is_taxonomy                = [ 'looper_provider_type' => 'taxonomy' ];
  $condition_omega_provider_is_page_children           = [ 'looper_provider_type' => 'page-children' ];
  $condition_omega_provider_is_terms                   = [ 'looper_provider_type' => 'terms' ];
  $condition_omega_provider_is_query_string            = [ 'looper_provider_type' => 'query-string' ];
  $condition_omega_provider_is_query_builder           = [ 'looper_provider_type' => 'query-builder' ];
  $condition_omega_provider_is_custom                  = [ 'looper_provider_type' => 'custom' ];
  $condition_omega_provider_is_key_array               = [ 'looper_provider_type' => 'key-array' ];
  $condition_omega_provider_is_dc                      = [ 'looper_provider_type' => 'dc' ];
  $condition_omega_provider_is_json                    = [ 'looper_provider_type' => 'json' ];
  $condition_omega_provider_is_string                  = [ 'looper_provider_type' => 'string' ];
  $condition_omega_provider_is_recent_or_query_builder = [ 'key' => 'looper_provider_type', 'op' => 'IN', 'value' => [ 'query-builder', 'query-recent' ] ];
  $condition_omega_provider_query_term_ids             = [ 'key' => 'looper_provider_query_term_ids', 'op' => 'MORE THAN ONE' ];
  $condition_omega_provider_has_offset                 = [ 'key' => 'looper_provider_type', 'op' => 'IN', 'value' => [ 'taxonomy', 'terms', 'page-children', 'dc', 'key-array', 'json', 'custom' ] ];


  // Options
  // -------

  $options_omega_group_toggle_include_exclude = [
    'toggle' => [
      'always_show' => true,
      'on'          => true,
      'off'         => false,
      'on_label'    => cs_recall( 'label_include' ),
      'off_label'   => cs_recall( 'label_exclude' ),
    ],
  ];

  $options_provider_query_count = [
    'choices' => [
      [ 'value' => '',           'label' => cs_recall( 'label_default' ) ],
      [ 'value' => '{{custom}}', 'label' => cs_recall( 'label_custom' )  ],
    ],
    'custom_value' => 4,
    'placeholder'  => get_option( 'posts_per_page' ),
  ];

  $options_provider_query_offset = [
    'choices' => [
      [ 'value' => '',           'label' => cs_recall( 'label_off' )   ],
      [ 'value' => 'by_page', 'label' => __('By Page', 'cornerstone') ],
      [ 'value' => '{{custom}}', 'label' => cs_recall( 'label_count' ) ],
    ],
    'custom_value' => get_option( 'posts_per_page' ),
    'placeholder'  => get_option( 'posts_per_page' ),
  ];

  $options_provider_json = [
    'mode'         => 'json',
    'button_label' => cs_recall( 'label_edit' ),
    'header_label' => cs_recall( 'label_json' ),
  ];

  $options_provider_string_delim = [
    'choices' => [
      [ 'value' => "\n",         'label' => cs_recall( 'label_newline' ) ],
      [ 'value' => '{{custom}}', 'label' => cs_recall( 'label_string' )  ],
    ],
    'custom_value' => '',
  ];


  $options_provider_array_offset = [
    'choices' => [
      [ 'value' => '',           'label' => cs_recall( 'label_off' )   ],
      [ 'value' => '{{custom}}', 'label' => cs_recall( 'label_count' ) ],
    ],
    'custom_value' => 0,
    'placeholder'  => 0,
  ];

  $builtIn = [


      // Query String
      // ------------

      [
        'key'        => 'looper_provider_query_string',
        'type'       => 'text',
        'label'      => cs_recall( 'label_wp_query' ),
        'options'    => [ 'placeholder' => 'category_name=uncategorized&posts_per_page=2' ],
        'conditions' => [ $condition_omega_provider_is_query_string ],
      ],


      // Posts
      // -----

      [
        'keys'    => [
          'types' => 'looper_provider_query_post_types',
          'in'    => 'looper_provider_query_post_in',
          'ids'   => 'looper_provider_query_post_ids'
        ],
        'type'    => 'group-picker',
        'label'   => cs_recall( 'label_posts' ),
        'options' => [
          'icon'  => 'database',
          'label' => '{{remote:query-builder-posts:types,in,ids}}',
        ],
        'conditions' => [ $condition_omega_provider_is_query_builder ],
        'controls'   => [
          [
            'key'     => 'looper_provider_query_post_types',
            'type'    => 'select-many',
            'label'   => cs_recall( 'label_types' ),
            'options' => [
              'require_one' => true,
              'choices'     => cornerstone( 'Locator' )->get_post_type_options()
            ],
          ],
          [
            'key'      => 'looper_provider_query_post_in',
            'type'     => 'group',
            'label'    => cs_recall( 'label_specific_posts' ),
            'options'  => $options_omega_group_toggle_include_exclude,
            'controls' => [
              [
                'key'     => 'looper_provider_query_post_ids',
                'type'    => 'select-many',
                'options' => [ 'choices' => 'posts:all' ],
              ],
              [
                'keys'       => [ 'sticky' => 'looper_provider_query_include_sticky' ],
                'type'       => 'checkbox-list',
                'conditions' => [ $condition_omega_provider_is_query_builder ],
                'options'    => [
                  'list' => [
                    [ 'key' => 'sticky', 'label' => cs_recall( 'label_include_sticky_posts' ), 'full' => true ],
                  ],
                ],
              ],
            ],
          ],
        ],
      ],


      // Taxonomies
      // ----------

      [
        'keys' => [
          'in'  => 'looper_provider_query_term_in',
          'ids' => 'looper_provider_query_term_ids'
        ],
        'type'    => 'group-picker',
        'label'   => cs_recall( 'label_taxonomies' ),
        'options' => [
          'icon'  => 'tag',
          'label' => '{{remote:query-builder-terms:in,ids}}',
        ],
        'conditions' => [ $condition_omega_provider_is_query_builder ],
        'controls'   => [
          [
            'key'      => 'looper_provider_query_term_in',
            'type'     => 'group',
            'label'    => cs_recall( 'label_specific_terms' ),
            'options'  => $options_omega_group_toggle_include_exclude,
            'controls' => [
              [
                'key'     => 'looper_provider_query_term_ids',
                'type'    => 'select-many',
                'options' => [ 'choices' => 'taxonomy-terms:all' ],
              ],
              [
                'keys'       => [ 'require_all' => 'looper_provider_query_term_and' ],
                'type'       => 'checkbox-list',
                'conditions' => [ $condition_omega_provider_query_term_ids ],
                'options'    => [
                  'list' => [
                    [ 'key' => 'require_all', 'label' => cs_recall( 'label_must_have_all_selected_terms' ), 'full' => true ],
                  ],
                ],
              ],
            ],
          ],
        ],
      ],


      // Authors
      // -------

      [
        'keys' => [
          'in'  => 'looper_provider_query_author_in',
          'ids' => 'looper_provider_query_author_ids'
        ],
        'type'    => 'group-picker',
        'label'   => cs_recall( 'label_authors' ),
        'options' => [
          'icon'  => 'user',
          'label' => '{{remote:query-builder-authors:in,ids}}',
        ],
        'conditions' => [ $condition_omega_provider_is_query_builder ],
        'controls'   => [
          [
            'key'      => 'looper_provider_query_author_in',
            'type'     => 'group',
            'label'    => cs_recall( 'label_authors' ),
            'options'  => $options_omega_group_toggle_include_exclude,
            'controls' => [
              [
                'key'     => 'looper_provider_query_author_ids',
                'type'    => 'select-many',
                'options' => [ 'choices' => 'user:all' ],
              ],
            ],
          ],
        ],
      ],

      // Meta Values
      apply_filters("cs_query_builder_meta_value_control", null),

      // Date
      // ----

      [
        'type' => 'group-picker',
        'keys' => [
          'before' => 'looper_provider_query_before',
          'after'  => 'looper_provider_query_after',
        ],
        'label'   => cs_recall( 'label_date' ),
        'options' => [
          'icon'  => 'date',
          'label' => '{{date-range:before,after}}',
        ],
        'conditions' => [ $condition_omega_provider_is_query_builder ],
        'controls'   => [
          [
            'key'   => 'looper_provider_query_after',
            'type'  => 'date-time',
            'label' => cs_recall( 'label_published_after' ),
          ],
          [
            'key'   => 'looper_provider_query_before',
            'type'  => 'date-time',
            'label' => cs_recall( 'label_published_before' ),
          ],
        ],
      ],


      // Order By
      apply_filters("cs_query_builder_orderby_control", null),


      // Count & Offset
      // --------------

      [
        'key'        => 'looper_provider_query_count',
        'type'       => 'choose',
        'label'      => cs_recall( 'label_count' ),
        'options'    => $options_provider_query_count,
        'conditions' => [ $condition_omega_provider_is_recent_or_query_builder ],
      ],

      [
        'key'        => 'looper_provider_query_offset',
        'type'       => 'choose',
        'label'      => cs_recall( 'label_offset' ),
        'options'    => $options_provider_query_offset,
        'description' => __('Where to start the user query loop. \'By Page\' calculates the offset based on the count and the value of the ?paged query var', 'cornerstone'),
        'conditions' => [ $condition_omega_provider_is_recent_or_query_builder ],
      ],


      // All Terms (Taxonomy)
      // --------------------

      [
        'key'         => 'looper_provider_tax',
        'type'        => 'select',
        'label'       => cs_recall( 'label_taxonomy' ),
        'options'     => [ 'choices' => cornerstone( 'Locator' )->get_taxonomy_options() ],
        'conditions'  => [ $condition_omega_provider_is_taxonomy ],
        'description' => cs_recall( 'label_uses_get_the_terms_to_find_terms_associated' ),
      ],

      [
        'key'        => 'looper_provider_tax_hide_empty',
        'type'       => 'choose',
        'label'      => cs_recall( 'label_empty_terms' ),
        'conditions' => [ $condition_omega_provider_is_taxonomy ],
        'options'    => [
          'choices' => [
            [ 'value' => false, 'label' => cs_recall( 'label_include' ) ],
            [ 'value' => true,  'label' => cs_recall( 'label_exclude' ) ],
          ],
        ],
      ],


      // Current Post Terms
      // ------------------

      [
        'key'         => 'looper_provider_terms_tax',
        'type'        => 'select',
        'label'       => cs_recall( 'label_taxonomy' ),
        'conditions'  => [ $condition_omega_provider_is_terms ],
        'description' => cs_recall( 'label_uses_get_the_terms_to_find_terms_associated' ),
        'options'     => [ 'choices' => cornerstone( 'Locator' )->get_taxonomy_options() ],
      ],


      // Custom
      // ------

      [
        'key'         => 'looper_provider_custom',
        'type'        => 'text',
        'label'       => cs_recall( 'label_hook' ),
        'conditions'  => [ $condition_omega_provider_is_custom ],
        'description' => __( 'Custom PHP must be used to integrate with cs_looper_custom_my_data hook.', '__x__' ),
        'options'     => [
          'placeholder' => 'my_data'
        ],
      ],

      [
        'key'         => 'looper_provider_json',
        'type'        => 'code-editor',
        'label'       => cs_recall( 'label_params' ),
        'conditions'  => [ $condition_omega_provider_is_custom ],
        'description' => __( 'Content must be valid JSON. It will be decoded and passed as $params into your hook.', '__x__' ),
        'options'     => $options_provider_json,
      ],


      // JSON
      // ----

      [
        'key'         => 'looper_provider_json',
        'type'        => 'code-editor',
        'label'       => cs_recall( 'label_content' ),
        'conditions'  => [ $condition_omega_provider_is_json ],
        'description' => __( 'Content must be valid JSON with the top level being an array of objects. The object keys will be available in Dynamic Content.', '__x__' ),
        'options'     => array_merge( $options_provider_json, [ 'valid' => 'array' ] )
      ],



      // String
      // ----

      [
        'key'         => 'looper_provider_string_content',
        'type'        => 'textarea',
        'label'       => cs_recall( 'label_string' ),
        'options'     => [ 'height' => 2 ],
        'conditions'  => [ $condition_omega_provider_is_string ]
      ],

      [
        'key'         => 'looper_provider_string_delim',
        'type'        => 'choose',
        'label'       => cs_recall( 'label_delimiter' ),
        'options'     => $options_provider_string_delim,
        'conditions'  => [ $condition_omega_provider_is_string ]
      ],


      // Key Array
      // ---------

      [
        'key'        => 'looper_provider_key_array',
        'type'       => 'text',
        'label'      => cs_recall( 'label_key' ),
        'conditions' => [ $condition_omega_provider_is_key_array ],
        'options'    => [
          'placeholder' => 'array_key'
        ],
      ],


      // Dynamic Content
      // ---------------

      [
        'key'        => 'looper_provider_dc',
        'type'       => 'text',
        'label'      => cs_recall( 'label_input' ),
        'conditions' => [ $condition_omega_provider_is_dc ],
        'options'    => [
          'placeholder'     => '',
          'dynamic_content' => 'array'
        ],
      ],


      // Array Offset
      // ------------

      [
        'key'        => 'looper_provider_array_offset',
        'type'       => 'choose',
        'label'      => cs_recall( 'label_offset' ),
        'conditions' => [ $condition_omega_provider_has_offset ],
        'options'    => $options_provider_array_offset,
      ],

  ];

  // Return with api controls
  return array_merge(
    $builtIn,
    cs_looper_provider_controls(),
    global_provider_controls()
  );
}

function choices() {

  $builtIn = [
    [
      'value' => 'query-recent',
      'label' => cs_recall( 'label_recent_posts' ),
    ],
    [
      'value' => 'query-builder',
      'label' => cs_recall( 'label_query_builder' ),
    ],
    [
      'value' => 'query-string',
      'label' => cs_recall( 'label_query_string' ),
    ],
    [
      'value' => 'taxonomy',
      'label' => cs_recall( 'label_all_terms' ),
    ],
    [
      'value' => 'terms',
      'label' => cs_recall( 'label_current_post_terms' ),
    ],
    [
      'value' => 'page-children',
      'label' => cs_recall( 'label_current_page_children' ),
    ],
    [
      'value' => 'dc',
      'label' => cs_recall( 'label_dynamic_content' ),
    ],
    [
      'value' => 'json',
      'label' => cs_recall( 'label_json' ),
    ],
    [
      'value' => 'string',
      'label' => cs_recall( 'label_string' ),
    ],
    [
      'value' => 'key-array',
      'label' => cs_recall( 'label_array' ),
    ],
    [
      'value' => 'custom',
      'label' => cs_recall( 'label_custom' ),
    ],
  ];

  // Build with registered looper choices
  return array_merge(
    $builtIn,
    cs_looper_provider_choices()
  );
}

function global_provider_controls() {
  return [
    [
      'key' => 'looper_provider_array_loop_keys',
      'type' => 'toggle',
      'label' => __("Loop Keys", "cornerstone"),
      'description' => __("If true, this will loop over each key using the key as the index", "cornerstone"),

      // Conditions to show Loop Keys
      'conditions' => [
        [
          'key' => 'looper_provider_type',
          'op' => 'IN',
          'value' => cs_looper_provider_loop_key_types(),
        ]
      ],
    ]
  ];
}

cs_register_control_partial("looper-provider", __NAMESPACE__ . '\controls');
