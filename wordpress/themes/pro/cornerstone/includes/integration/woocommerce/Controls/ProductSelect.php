<?php

/**
 * Product select box
 */
cs_register_control_partial( 'wc-product-select', function($extension = []) {

  return array_merge(
    [
      'key' => 'product',
      'label' => __("Product", "cornerstone"),
      'type' => 'select',
      'options' => [
        'choices' => 'dynamic:product',
        'placeholder' => __('Current Product', "cornerstone"),
      ],
    ],
    $extension
  );

});
