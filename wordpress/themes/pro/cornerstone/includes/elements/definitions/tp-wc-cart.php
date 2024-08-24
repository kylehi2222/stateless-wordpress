<?php

// =============================================================================
// CORNERSTONE/INCLUDES/ELEMENTS/DEFINITIONS/TP-WC-CART.PHP
// -----------------------------------------------------------------------------
// V2 element definitions.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Values
//   02. Style
//   03. Render
//   04. Builder Setup
//   05. Register Element
// =============================================================================

// Values
// =============================================================================

$values = cs_compose_values(
  'cart-button',
  'omega',
  'omega:custom-atts'
);



// Style
// =============================================================================

function x_element_tss_tp_wc_cart() {
  return [
    'require' => [ 'elements-wc' ],
    'modules' => [
      'effects',
      'mini-cart',
      ['anchor', [
        'args' => [
          'selector' => '.buttons .x-anchor'
        ],
        'remap' => [ 'cart_anchor' => 'anchor' ]
      ]]
    ]
  ];
}


// Render
// =============================================================================

function x_element_render_tp_wc_cart( $data ) {
  // Woo 7.8 workaround
  // @TODO move to mini-cart block?
  // https://developer.woocommerce.com/2023/06/13/woocommerce-7-8-released/
  wp_enqueue_script( 'wc-cart-fragments' );

  return cs_get_partial_view( 'mini-cart', array_merge( cs_extract( $data, [ 'cart' => '', 'effects' => '' ] ), [
    'id'          => $data['id'],
    'classes'     => $data['classes'],
    'style' => $data['style'],
    'custom_atts' => $data['custom_atts'],
  ] ));
}

// Woo 7.8 for in preview
add_action("cs_before_preview_frame", function() {
  if (class_exists( 'WooCommerce' )) {
    wp_enqueue_script( 'wc-cart-fragments' );
  }
});

// Builder Setup
// =============================================================================

function x_element_builder_setup_tp_wc_cart() {
  return cs_compose_controls(
    cs_partial_controls( 'cart', [ 'is_nested' => false ] ),
    cs_partial_controls( 'anchor', cs_recall( 'settings_anchor:cart-button' ) ),
    cs_partial_controls( 'effects' ),
    cs_partial_controls( 'omega', [ 'add_custom_atts' => true ] )
  );
}



// Register Element
// =============================================================================

cs_register_element( 'tp-wc-cart', [
  'title'      => __( 'Mini-Cart', 'cornerstone' ),
  'values'     => $values,
  'includes'   => [ 'cart', 'effects' ],
  'builder'    => 'x_element_builder_setup_tp_wc_cart',
  'tss'        => 'x_element_tss_tp_wc_cart',
  'render'     => 'x_element_render_tp_wc_cart',
  'icon'       => 'native',
  'options'    => [ 'wc_fragments' => true, 'empty_placeholder' => false ],
  'active'     => class_exists( 'WooCommerce' ),
  'group'      => 'woocommerce',
] );
