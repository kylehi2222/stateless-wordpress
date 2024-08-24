<?php

/**
 * Upsell looper
 */

use function Cornerstone\WooCommerce\LinkedProduct\linked_product_looper;
use function Cornerstone\WooCommerce\LinkedProduct\linked_product_values;

cs_looper_provider_register('wc-upsell', [
  'label' => __("Upsells", "cornerstone"),

  'controls' => 'Cornerstone\WooCommerce\LinkedProduct\linked_product_controls',

  'values' => linked_product_values(),

  // Main
  'filter' => function($results, $args = []) {
    $product_id = cs_get_array_value($args, 'product', null);

    return linked_product_looper($product_id, 'get_upsell_ids', $args);
  },
]);
