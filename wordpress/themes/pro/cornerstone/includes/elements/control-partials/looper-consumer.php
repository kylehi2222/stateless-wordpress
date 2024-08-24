<?php

namespace Cornerstone\ControlPartials\LooperConsumer;

// SQL Direction choice
// ASC or DESC

cs_register_control_partial( 'looper-consumer', function($settings = []) {
  // Consumer
  $options_consumer_repeat = [
    'choices' => [
      [ 'value' => '-1',         'label' => cs_recall( 'label_all' )  ],
      [ 'value' => '1',          'label' => cs_recall( 'label_one' )  ],
      [ 'value' => '{{custom}}', 'label' => cs_recall( 'label_many' ) ],
    ],
    'custom_value' => 4,
  ];


  // Output
  // ------

  return array_merge(
    [
      'key'         => 'looper_consumer',
      'type'        => 'group',
      'label'       => cs_recall( 'label_looper_consumer' ),
      'options'     => cs_recall( 'options_group_toggle_off_on_bool' ),
      'description' => __( 'Consume data from the closest Looper Provider, or the main query.', '__x__' ),
      'controls'    => [
        [
          'key'     => 'looper_consumer_repeat',
          'type'    => 'choose',
          'label'   => cs_recall( 'label_items' ),
          'options' => $options_consumer_repeat,
        ],
        [
          'key'     => 'looper_consumer_rewind',
          'type'    => 'toggle',
          'label'   => __("Rewind", "cornerstone"),
          'description' => __("After this consumer is finished looping, rewind the data for the NEXT consumer to use from the start. Not always needed depending on the provider type", "cornerstone"),
        ],
      ],
    ],
    $settings
  );
});
