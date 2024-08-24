<?php

/**
 * Filter to add typography controls
 */
add_filter("cs_theme_options_woocommerce_ajax_cart_controls", function() {
  if (!class_exists("WooCommerce")) {
    return [];
  }

  return [
    [
      'keys' => [
        'value' => 'x_woocommerce_ajax_add_to_cart_color',
        'alt'   => 'x_woocommerce_ajax_add_to_cart_color_hover',
      ],
      'type'    => 'color',
      'label'   => __( 'AJAX<br/>Color', '__x__' ),
      'options' => cs_recall( 'options_swatch_base_interaction_labels' ),
    ],
    [
      'keys' => [
        'value' => 'x_woocommerce_ajax_add_to_cart_bg_color',
        'alt'   => 'x_woocommerce_ajax_add_to_cart_bg_color_hover',
      ],
      'type'    => 'color',
      'label'   => __( 'AJAX Background', '__x__' ),
      'options' => cs_recall( 'options_swatch_base_interaction_labels' ),
    ],
  ];
});

add_filter("cs_theme_options_woocommerce_ajax_cart_group", function() {
  if (!class_exists("WooCommerce")) {
    return null;
  }


  return [
    'type' => 'group',
    'label' => __( 'Cart', '__x__' ),
    'controls' => apply_filters("cs_theme_options_woocommerce_ajax_cart_controls", []),
  ];

});


add_filter("cs_theme_options_woocommerce_ajax_cart_group_module", function() {
  if (!class_exists("WooCommerce")) {
    return null;
  }

  return [
    'type'  => 'group-sub-module',
    'label' => __( 'WooCommerce', '__x__' ),
    'options' => [ 'tag' => 'woocommerce', 'name' => 'x-theme-options:woocommerce' ],
    'controls' => [
      apply_filters("cs_theme_options_woocommerce_ajax_cart_group", null),
    ],
  ];
});
