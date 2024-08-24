<?php

namespace Themeco\ControlPartials\AndOr;

/**
 * And Or Choose
 */

cs_register_control_partial( 'and-or', function($settings = []) {

  // Output

  return array_merge(
    [
      'label' => __('And Or', CS_LOCALIZE),
      'type' => 'choose-single',
      'options' => [
        'choices' => [
          [
            'value' => 'AND',
            'label' => __('AND', CS_LOCALIZE),
          ],
          [
            'value' => 'OR',
            'label' => __('OR', CS_LOCALIZE),
          ],
        ],
      ],
    ],
    $settings
  );
});
