<?php

namespace Cornerstone\ControlPartials\Sql;

// SQL Direction choice
// ASC or DESC

cs_register_control_partial( 'sql-direction', function($settings = []) {

  // Output
  // ------

  return array_merge(
    [
      'label' => __('Direction', CS_LOCALIZE),
      'type' => 'choose',
      'options' => [
        'choices' => [

          [
            'value' => 'DESC',
            'label' => __('DESC', CS_LOCALIZE),
          ],

          [
            'value' => 'ASC',
            'label' => __('ASC', CS_LOCALIZE),
          ],

        ],
      ],
    ],
    $settings
  );
});
