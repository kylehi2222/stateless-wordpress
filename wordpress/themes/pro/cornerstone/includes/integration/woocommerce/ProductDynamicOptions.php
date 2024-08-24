<?php


// Dynamic Choices for Product
// dynamic:product
cs_dynamic_content_register_dynamic_option("product", [
  'key' => "product",
  'type' => "select",
  'label' => __("WooCommerce Product", CS_LOCALIZE),
  'options' => [
    'choices' => "dynamic:product",
    'placeholder' => __("Enter Product ID", CS_LOCALIZE),
  ],

  // Product graber to select
  'filter' => function() {
    // Product choices setup
    $out = [
      [
        'value' => "",
        'label' => "Current Product",
      ],
    ];

    // Grab products for options
    $products = wc_get_products([
      'limit' => apply_filters( 'cs_locator_limit', 100 ),
      'orderby' => 'date',
      'order' => 'DESC',
      'return' => 'objects',
    ]);

    // Create select choices from products
    foreach ($products as $product) {
      $out[] = [
        'value' => $product->id,
        'label' => $product->get_title(),
      ];
    }

    return $out;
  }
]);
