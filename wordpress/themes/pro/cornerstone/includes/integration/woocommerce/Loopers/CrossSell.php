<?php

/**
 * Cross Sell looper
 */

use function Cornerstone\WooCommerce\LinkedProduct\linked_product_looper;
use function Cornerstone\WooCommerce\LinkedProduct\linked_product_values;

cs_looper_provider_register('wc-crosssells', [
  'label' => __("Cross Sells", "cornerstone"),

  'controls' => 'Cornerstone\WooCommerce\LinkedProduct\linked_product_controls',

  'values' => linked_product_values(),

  // Main
  'filter' => function($results, $args = [], $element= []) {
    $product_id = cs_get_array_value($args, 'product', null);

    return linked_product_looper($product_id, 'get_cross_sell_ids', $args);
  },
]);
