<?php

/**
 * Background video helper
 */

cs_register_control_partial( 'bg-video', function($settings = []) {

  $k_pre = cs_get_array_value($settings, 'prefix', '');
  $conditions = cs_get_array_value($settings, 'conditions', []);

  return [
    // Enabled source
    [
      'key'       => $k_pre,
      'type'      => 'text',
      'label'     => cs_recall( 'label_source' ),
      'condition' => $conditions,
      'options'   => [
        'placeholder' => 'http://example.com/a.mp4'
      ],
    ],


    // Poster
    [
      'keys' => [
        'img_source' => $k_pre . '_poster',
      ],
      'type'      => 'image',
      'label'     => cs_recall( 'label_poster' ),
      'condition' => $conditions,
      'options'   => [
        'height' => 3
      ],
    ],

    // Loop
    [
      'key'       => $k_pre . '_loop',
      'type'      => 'choose',
      'label'     => cs_recall( 'label_loop' ),
      'condition' => $conditions,
      'options'   => cs_recall( 'options_choices_off_on_bool' ),
    ],

    // Pause on Out of View
    [
      'key'       => $k_pre . '_pause_out_of_view',
      'type'      => 'choose',
      'label'     => __("Pause out of View", "cornerstone"),
      'condition' => $conditions,
      'options'   => cs_recall( 'options_choices_off_on_bool' ),
    ],
  ];
});
