<?php

/**
 * Filter to add typography controls
 */
add_filter("cs_theme_options_woocommerce_group", function() {
  if (!class_exists("WooCommerce")) {
    return null;
  }

  $choices_left_right_positioning = [
    [ 'value' => 'left',  'label' => __( 'Left', '__x__' )  ],
    [ 'value' => 'right', 'label' => __( 'Right', '__x__' ) ],
  ];

  $choices_section_layouts = [
    [ 'value' => 'sidebar',    'label' => __( 'Global', '__x__' )    ],
    [ 'value' => 'full-width', 'label' => __( 'Fullwidth', '__x__' ) ],
  ];

  $choices_shop_columns = [
    [ 'value' => '1', 'label' => '1' ],
    [ 'value' => '2', 'label' => '2' ],
    [ 'value' => '3', 'label' => '3' ],
    [ 'value' => '4', 'label' => '4' ],
  ];

  $choices_woocommerce_navbar_cart_content = [
    [ 'value' => 'icon',  'label' => __( 'Icon', '__x__' )  ],
    [ 'value' => 'total', 'label' => __( 'Total', '__x__' ) ],
    [ 'value' => 'count', 'label' => __( 'Count', '__x__' ) ],
  ];

  $condition_classic_headers_enabled              = [ 'virtual:classic_headers' => true ];

  $condition_woocommerce_header_menu_enabled      = [ 'x_woocommerce_header_menu_enable' => true ];
  $condition_woocommerce_product_tabs_enable      = [ 'x_woocommerce_product_tabs_enable' => true ];
  $condition_woocommerce_related_products_enabled = [ 'x_woocommerce_product_related_enable' => true ];
  $condition_woocommerce_upsells_enabled          = [ 'x_woocommerce_product_upsells_enable' => true ];
  $condition_woocommerce_cross_sells_enabled      = [ 'x_woocommerce_cart_cross_sells_enable' => true ];

  $options_header_cart_alignment = [
    'available_units' => [ 'px' ],
    'fallback_value'  => '30px',
    'ranges'          => [
      'px' => [ 'min' => 0, 'max' => 100, 'step' => 1 ],
    ],
  ];

  $choices_inline_or_stacked = [
    [ 'value' => 'inline',  'label' => __( 'Inline', '__x__' )  ],
    [ 'value' => 'stacked', 'label' => __( 'Stacked', '__x__' ) ],
  ];

  $choices_cart_layout = [
    [ 'value' => 'inner' ,      'label' => __( 'Single (Inner)', '__x__' )         ],
    [ 'value' => 'outer' ,      'label' => __( 'Single (Outer)', '__x__' )         ],
    [ 'value' => 'inner-outer', 'label' => __( 'Double (Inner / Outer)', '__x__' ) ],
    [ 'value' => 'outer-inner', 'label' => __( 'Double (Outer / Inner)', '__x__' ) ],
  ];

  $choices_cart_shape = [
    [ 'value' => 'square',  'label' => __( 'Square', '__x__' )  ],
    [ 'value' => 'rounded', 'label' => __( 'Rounded', '__x__' ) ],
  ];

  $cart_controls = array_merge(
    [
      [
        'key'     => 'x_woocommerce_cart_cross_sells_enable',
        'type'    => 'choose',
        'label'   => __( 'Cross Sells', '__x__' ),
        'options' => cs_recall( 'options_choices_off_on_bool_string' ),
      ],
      [
        'key'        => 'x_woocommerce_cart_cross_sells_columns',
        'type'       => 'choose',
        'label'      => __( 'Cross Sells Columns', '__x__' ),
        'options'    => [ 'choices' => $choices_shop_columns ],
        'conditions' => [ $condition_woocommerce_cross_sells_enabled ],
      ],
      [
        'key'        => 'x_woocommerce_cart_cross_sells_count',
        'type'       => 'text',
        'label'      => __( 'Cross Sells Count', '__x__' ),
        'conditions' => [ $condition_woocommerce_cross_sells_enabled ],
      ],
    ],
    apply_filters("cs_theme_options_woocommerce_ajax_cart_controls", [])
  );


  return [
    'type'  => 'group-sub-module',
    'label' => __( 'WooCommerce', '__x__' ),
    'options' => [ 'tag' => 'woocommerce', 'name' => 'x-theme-options:woocommerce' ],
    'controls' => [
      [
        'key'         => 'x_woocommerce_header_menu_enable',
        'type'        => 'group',
        'label'       => __( 'Navbar Cart', '__x__' ),
        'options'     => cs_recall( 'options_group_toggle_off_on_bool_string' ),
        'conditions'  => [ $condition_classic_headers_enabled ],
        'description' => __( 'Enable a cart in your navigation that you can customize to showcase the information you want your users to see as they add merchandise to their cart (e.g. item count, subtotal, et cetera).', '__x__' ),
        'controls'    => [
          [
            'key'        => 'x_woocommerce_header_hide_empty_cart',
            'type'       => 'choose',
            'label'      => __( 'Hide When Empty', '__x__' ),
            'options'    => cs_recall( 'options_choices_off_on_bool_string' ),
            'conditions' => [ $condition_woocommerce_header_menu_enabled ],
          ],
          [
            'key'        => 'x_woocommerce_header_cart_info',
            'type'       => 'select',
            'label'      => __( 'Information', '__x__' ),
            'options'    => [ 'choices' => $choices_cart_layout ],
            'conditions' => [ $condition_woocommerce_header_menu_enabled ],
          ],
          [
            'key'        => 'x_woocommerce_header_cart_style',
            'type'       => 'choose',
            'label'      => __( 'Style', '__x__' ),
            'options'    => [ 'choices' => $choices_cart_shape ],
            'conditions' => [ $condition_woocommerce_header_menu_enabled ],
          ],
          [
            'key'        => 'x_woocommerce_header_cart_layout',
            'type'       => 'choose',
            'label'      => __( 'Layout', '__x__' ),
            'options'    => [ 'choices' => $choices_inline_or_stacked ],
            'conditions' => [ $condition_woocommerce_header_menu_enabled ],
          ],
          [
            'key'        => 'x_woocommerce_header_cart_adjust',
            'type'       => 'unit-slider',
            'label'      => __( 'Alignment', '__x__' ),
            'options'    => $options_header_cart_alignment,
            'conditions' => [ $condition_woocommerce_header_menu_enabled ],
          ],
          [
            'type'       => 'group',
            'label'      => __( 'Inner Content', '__x__' ),
            'conditions' => [ $condition_woocommerce_header_menu_enabled ],
            'controls'   => [
              [
                'key'     => 'x_woocommerce_header_cart_content_inner',
                'type'    => 'select',
                'options' => [ 'choices' => $choices_woocommerce_navbar_cart_content ],
              ],
              [
                'keys' => [
                  'value' => 'x_woocommerce_header_cart_content_inner_color',
                  'alt'   => 'x_woocommerce_header_cart_content_inner_color_hover',
                ],
                'type'    => 'color',
                'options' => cs_recall( 'options_swatch_base_interaction_labels' ),
              ],
            ],
          ],
          [
            'type'       => 'group',
            'label'      => __( 'Outer Content', '__x__' ),
            'conditions' => [ $condition_woocommerce_header_menu_enabled ],
            'controls'   => [
              [
                'key'     => 'x_woocommerce_header_cart_content_outer',
                'type'    => 'select',
                'options' => [ 'choices' => $choices_woocommerce_navbar_cart_content ],
              ],
              [
                'keys' => [
                  'value' => 'x_woocommerce_header_cart_content_outer_color',
                  'alt'   => 'x_woocommerce_header_cart_content_outer_color_hover',
                ],
                'type'    => 'color',
                'options' => cs_recall( 'options_swatch_base_interaction_labels' ),
              ],
            ],
          ],
        ],
      ], [
        'type'        => 'group',
        'label'       => __( 'Shop', '__x__' ),
        // 'description' => __( 'This section handles all options regarding your WooCommerce setup. Select your content layout, product columns, along with plenty of other options to get your shop up and running.<br><br>The "Shop Layout" option allows you to keep your sidebar on your shop page if you have already selected "Content Left, Sidebar Right" or "Sidebar Left, Content Right" for you "Content Layout" option, or remove the sidebar completely if desired.<br><br>The "Placeholder Thumbnail" will show up for items in your shop that do not yet have a featured image assigned. Make sure that the thumbanil you provide matches the image dimensions you specify in WooCommerce\'s Customizer settings.', '__x__' ),
        'description' => __( 'This section handles all options regarding your WooCommerce setup. The "Placeholder Thumbnail" will show up for items in your shop that do not yet have a featured image assigned. Make sure that the thumbanil you provide matches the image dimensions you specify in WooCommerce\'s Customizer settings.', '__x__' ),
        'controls'    => [
          [
            'key'     => 'x_woocommerce_shop_layout_content',
            'type'    => 'choose',
            'label'   => __( 'Layout', '__x__' ),
            'options' => [ 'choices' => $choices_section_layouts ],
          ],
          [
            'key'     => 'x_woocommerce_shop_columns',
            'type'    => 'choose',
            'label'   => __( 'Columns', '__x__' ),
            'options' => [ 'choices' => $choices_shop_columns ],
          ],
          [
            'key'   => 'x_woocommerce_shop_count',
            'type'  => 'text',
            'label' => __( 'Posts Per Page', '__x__' ),
          ],
          [
            'key'   => 'x_woocommerce_shop_placeholder_thumbnail',
            'type'  => 'image',
            'label' => __( 'Placeholder Thumbnail', '__x__' ),
          ],
        ],
      ], [
        'key'         => 'x_woocommerce_product_tabs_enable',
        'type'        => 'group',
        'label'    => __( 'Single Product (Tabs)', '__x__' ),
        'options'     => cs_recall( 'options_group_toggle_off_on_bool_string' ),
        'description' => __( 'All options available in this section pertain to the layout of your individual product pages. Enable or disable the sections you want to use to achieve the layout you want.', '__x__' ),
        'controls'    => [
          [
            'key'        => 'x_woocommerce_product_tab_description_enable',
            'type'       => 'choose',
            'label'      => __( 'Description Tab', '__x__' ),
            'options'    => cs_recall( 'options_choices_off_on_bool_string' ),
            'conditions' => [ $condition_woocommerce_product_tabs_enable ],
          ],
          [
            'key'        => 'x_woocommerce_product_tab_additional_info_enable',
            'type'       => 'choose',
            'label'      => __( 'Additional Info Tab', '__x__' ),
            'options'    => cs_recall( 'options_choices_off_on_bool_string' ),
            'conditions' => [ $condition_woocommerce_product_tabs_enable ],
          ],
          [
            'key'        => 'x_woocommerce_product_tab_reviews_enable',
            'type'       => 'choose',
            'label'      => __( 'Reviews Tab', '__x__' ),
            'options'    => cs_recall( 'options_choices_off_on_bool_string' ),
            'conditions' => [ $condition_woocommerce_product_tabs_enable ],
          ],
        ],
      ], [
        'key'      => 'x_woocommerce_product_related_enable',
        'type'     => 'group',
        'label' => __( 'Single Product (Related Products)', '__x__' ),
        'options'  => cs_recall( 'options_group_toggle_off_on_bool_string' ),
        'controls' => [
          [
            'key'        => 'x_woocommerce_product_related_columns',
            'type'       => 'choose',
            'label'      => __( 'Related Columns', '__x__' ),
            'options'    => [ 'choices' => $choices_shop_columns ],
            'conditions' => [ $condition_woocommerce_related_products_enabled ],
          ],
          [
            'key'        => 'x_woocommerce_product_related_count',
            'type'       => 'text',
            'label'      => __( 'Related Count', '__x__' ),
            'conditions' => [ $condition_woocommerce_related_products_enabled ],
          ],
        ],
      ], [
        'key'      => 'x_woocommerce_product_upsells_enable',
        'type'     => 'group',
        'label' => __( 'Single Product (Upsells)', '__x__' ),
        'options'  => cs_recall( 'options_group_toggle_off_on_bool_string' ),
        'controls' => [
          [
            'key'        => 'x_woocommerce_product_upsell_columns',
            'type'       => 'choose',
            'label'      => __( 'Upsells Columns', '__x__' ),
            'options'    => [ 'choices' => $choices_shop_columns ],
            'conditions' => [ $condition_woocommerce_upsells_enabled ],
          ],
          [
            'key'        => 'x_woocommerce_product_upsell_count',
            'type'       => 'text',
            'label'      => __( 'Upsells Count', '__x__' ),
            'conditions' => [ $condition_woocommerce_upsells_enabled ],
          ],
        ],
      ], [
        'type' => 'group',
        'label' => __( 'Cart', '__x__' ),
        'description' => __( 'All options available in this section pertain to the layout of your cart page. Enable or disable the sections you want to use to achieve the layout you want.', '__x__' ),
        // 'description' => __( 'If you have the "Enable AJAX add to cart buttons on archives" WooCommerce setting active, you can control the colors of the confirmation overlay here that appears when adding an item on a product index page.', '__x__' ),
        'controls'    => $cart_controls,
      ], [
        'type'        => 'group',
        'label' => __( 'Widgets', '__x__' ),
        'description' => __( 'Select the placement of your product images in the various WooCommerce widgets that provide them. Right alignment is better if your items have longer titles to avoid staggered word wrapping.', '__x__' ),
        'controls'    => [
          [
            'key'     => 'x_woocommerce_widgets_image_alignment',
            'type'    => 'choose',
            'label'   => __( 'Image Alignment', '__x__' ),
            'options' => [ 'choices' => $choices_left_right_positioning ],
          ],
        ],
      ]
    ]
  ];
});
