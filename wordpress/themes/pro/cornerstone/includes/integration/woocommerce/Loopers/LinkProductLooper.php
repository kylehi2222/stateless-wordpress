<?php

namespace Cornerstone\WooCommerce\LinkedProduct;

const VALUES = [
  'product' => '',
  'query_order' => 'ASC',
  'query_orderby' => 'date',
  'query-builder_orderby_meta_key' => '',
];


/**
 * Cross sells and upsells are very similar
 * aside from the method used to grab the ids
 */
function linked_product_looper(
  $product_id,
  $method = 'get_cross_sell_ids',
  $args = []
) {

  $productToUse = null;

  // Use current product as upsell
  if (empty($product_id)) {
    global $product;

    if (!empty($product)) {
      $productToUse = $product;
    }
  }

  // Product to find upsells from
  $productToUse = empty($productToUse)
    ? wc_get_product($product_id)
    : $productToUse;

  // No valid product
  if (empty($productToUse)) {
    return [];
  }

  // Bad method or bad setup
  if (!method_exists($productToUse, $method)) {
    trigger_error("WC Linked Product Looper. Product does not have method " . $method);
    return [];
  }

  // Get ids of upsell
  $ids = $productToUse->{$method}();

  // No upsells
  if (empty($ids)) {
    return [];
  }

  $orderby = cs_get_array_value($args, 'query_orderby', 'date');
  $order = cs_get_array_value($args, 'query_order', 'ASC');

  $queryArgs = [
    'post_type' => 'product',
    'include' => $ids,
    'orderby' => $orderby,
    'order' => $order,
  ];

  // Meta value type
  if (in_array($orderby, \Cornerstone_Looper_Provider_User_Query::$META_ORDER_TYPES )) {
    $orderByMetaKey = cs_get_array_value($args, 'query-builder_orderby_meta_key', '');
    $queryArgs['meta_key'] = $orderByMetaKey;
  }

  // Uses get posts to integrate with setup_postdata
  $posts = get_posts($queryArgs);

  return $posts;
}

function linked_product_values() {
  return apply_filters("cs_wc_linked_product_default_values", VALUES);
}

function linked_product_controls() {
  return [
    cs_partial_controls('wc-product-select'),
    apply_filters("cs_query_builder_orderby_control", [], [
      'prefix' => '',
      'conditions' => [],
    ]),
  ];
}
