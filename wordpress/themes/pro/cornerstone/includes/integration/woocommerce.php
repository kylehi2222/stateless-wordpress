<?php

class Cornerstone_WooCommerce extends Cornerstone_Plugin_Component {

  protected static $instance;

  public static function instance() {
    if ( ! isset( self::$instance ) ) {
      self::$instance = new Cornerstone_WooCommerce();
    }
    return self::$instance;
  }

  public function labels() {
    return [
      'single' => [
        'singular' => __('WooCommerce Single', 'Cornerstone'),
        'plural' => __('WooCommerce Singles', 'Cornerstone'),
        'shortSingular' => __('WC Single', 'Cornerstone'),
        'shortPlural' => __('WC Singles', 'Cornerstone'),
      ],
      'archive' => [
        'singular' => __('WooCommerce Archive', 'Cornerstone'),
        'plural' => __('WooCommerce Archives', 'Cornerstone'),
        'shortSingular' => __('WC Archive', 'Cornerstone'),
        'shortPlural' => __('WC Archives', 'Cornerstone'),
      ]
    ];
  }

  public function setup() {

    add_filter( 'cs_looper_main_query', [ $this, 'setup_main_looper_query' ] );

    add_action( 'cs_layout_begin_single', [ $this, 'maybe_before_product'] );
    add_action( 'cs_layout_end_single', [ $this, 'maybe_after_product'] );
    add_filter( 'cs_layout_output_before_single', [ $this, 'product_wrapper_open'] );
    add_filter( 'cs_layout_output_after_single',  [ $this, 'product_wrapper_close'] );

    add_filter( 'cs_assignment_context_post_types', [$this, 'unset_preview_post_type' ] );
    add_filter( 'cs_preview_context_post_types', [$this, 'unset_preview_post_type' ] );

    add_filter( 'cs_assignment_contexts', [$this, 'assignment_contexts'] );
    add_filter( 'cs_preview_contexts', [$this, 'preview_contexts'] );
    add_filter( 'cs_condition_contexts', [$this, 'condition_contexts'] );

    add_filter( 'cs_condition_rule_single_product', [ $this, 'condition_rule_single_product' ] );
    add_filter( 'cs_condition_rule_archive_shop', [ $this, 'condition_rule_archive_shop' ] );

    add_filter( 'cs_condition_rule_wc_product_has', [ $this, 'condition_rule_wc_product_has' ], 10, 2 );
    add_filter( 'cs_condition_rule_wc_product_is', [ $this, 'condition_rule_wc_product_is' ], 10, 2 );

    add_filter( 'cs_condition_rule_wc_is_shop', [ $this, 'condition_rule_wc_is_shop' ], 10, 2 );
    add_filter( 'cs_condition_rule_wc_product_type', [ $this, 'condition_rule_wc_product_type' ], 10, 2 );
    add_filter( 'cs_condition_rule_wc_is_cart', [ $this, 'condition_rule_wc_is_cart' ], 10, 2 );
    add_filter( 'cs_condition_rule_wc_is_checkout', [ $this, 'condition_rule_wc_is_checkout' ], 10, 2 );
    add_filter( 'cs_condition_rule_wc_is_account', [ $this, 'condition_rule_wc_is_account' ], 10, 2 );

    add_filter( 'cs_detect_layout_type', [ $this, 'detect_layout_type'] );

    add_action('woocommerce_checkout_before_terms_and_conditions', [ $this, 'detect_terms_and_conditions_page' ]);

    add_action('cs_will_output_layout_layout:single-wc', [ $this, 'setupWooStructuredData' ]);

    add_filter( '_cs_document_layout_types', function( $layouts, $permissions, $isApi ) {

      if ( cornerstone('Env')->isSiteBuilder() && ( $isApi || $permissions->userCan('layout') ) ) {

        $labels = $this->labels();

        $layouts[] = [
          'value' => 'layout:single-wc',
          'postType' => 'cs_layout_single_wc',
          'labelSingular' => $labels['single']['singular'],
          'label' => $labels['single']['plural'],
          'labelShortSingular' => $labels['single']['shortSingular'],
          'labelShort' => $labels['single']['shortPlural'],
          'permissionContext' => 'layout',
          'defaultTemplateGroup' => 'shop'
        ];

        $layouts[] = [
          'value' => 'layout:archive-wc',
          'postType' => 'cs_layout_archive_wc',
          'labelSingular' => $labels['archive']['singular'],
          'label' => $labels['archive']['plural'],
          'labelShortSingular' => $labels['archive']['shortSingular'],
          'labelShort' => $labels['archive']['shortPlural'],
          'permissionContext' => 'layout',
          'defaultTemplateGroup' => 'shop'
        ];
      }

      return $layouts;
    },10, 3);

    add_filter( '_cs_document_layout_types_populate', function( $item ) {
      if ($item['value'] === 'layout:single-wc') {
        $posts = get_posts( ['numberposts' => 1, 'post_type' => 'product' ] );
        $item['defaultPreviewUrl'] = empty($posts[0]) ? null : get_permalink( $posts[0]->ID );
      }

      if ($item['value'] === 'layout:archive-wc') {
        $item['defaultPreviewUrl'] = get_permalink( wc_get_page_id( 'shop' ) );
      }
      return $item;
    });

    add_action('init', function() {
      if ( cornerstone('Env')->isSiteBuilder() ) {

        $labels = $this->labels();
        register_post_type( 'cs_layout_single_wc', array(
          'public'          => false,
          'capability_type' => 'page',
          'supports'        => false,
          'labels'          => array(
            'name'          => $labels['single']['plural'],
            'singular_name' => $labels['single']['singular'],
          )
        ) );

        register_post_type( 'cs_layout_archive_wc', array(
          'public'          => false,
          'capability_type' => 'page',
          'supports'        => false,
          'labels'          => array(
            'name'          => $labels['archive']['plural'],
            'singular_name' => $labels['archive']['singular'],
          )
        ) );

      }
    });

  }

  public function setup_main_looper_query( $provider ) {
    if ($this->is_wc_archive()) {
      return new Cornerstone_Looper_Provider_Shop();
    }

    return $provider;
  }




  public function product_wrapper_open( $content ) {
    if ( is_singular( 'product' )) {
      global $product;
      ob_start();
      ?><div id="product-<?php the_ID(); ?>" <?php wc_product_class( '', $product ); ?>><?php
      return ob_get_clean();
    }

    return $content;
  }

  public function product_wrapper_close( $content ) {

    if ( is_singular( 'product' ) ) {
      return '</div>';
    }

    return $content;
  }

  public function maybe_before_product() {
    if ( is_singular( 'product' ) ) {
      remove_action( 'woocommerce_before_single_product', 'woocommerce_output_all_notices', 10 );
      do_action( 'woocommerce_before_single_product' );
    }
  }

  public function maybe_after_product() {
    if ( is_singular( 'product' ) ) {
      do_action( 'woocommerce_after_single_product' );
    }
  }


  public function unset_preview_post_type( $post_types ) {
    unset($post_types['product']);
    return $post_types;
  }

  public function assignment_contexts( $contexts ) {

    $contexts['labels']['single-wc' ] = __( 'WooCommerce Single', 'cornerstone' );
    $contexts['labels']['archive-wc'] = __( 'WooCommerce Archive', 'cornerstone' );

    $contexts['controls']['single-wc' ] = $this->assignment_context_single();
    $contexts['controls']['archive-wc'] = $this->assignment_context_archive();

    return $contexts;
  }

  public function preview_contexts( $contexts ) {

    $contexts['labels']['single-wc' ] = __( 'WooCommerce Single', 'cornerstone' );
    $contexts['labels']['archive-wc'] = __( 'WooCommerce Archive', 'cornerstone' );

    $contexts['controls']['single-wc' ] = $this->preview_context_single();
    $contexts['controls']['archive-wc'] = $this->preview_context_archive();

    return $contexts;
  }

  public function condition_contexts( $contexts ) {

    $contexts['labels']['wc' ] = __( 'WooCommerce', 'cornerstone' );

    $contexts['controls']['wc'] = $this->condition_contexts_wc();

    return $contexts;
  }

  public function condition_contexts_wc() {

    return [
      [
        'key'   => 'wc:product-is',
        'label' => __('Product (is)', 'cornerstone'),
        'toggle' => ['type' => 'boolean'],
        'criteria' => [
          'type'    => 'select',
          'choices' => [
            [ 'value' => 'is-downloadable', 'label' => __('Downloadable', 'cornerstone') ],
            [ 'value' => 'is-featured', 'label' => __('Featured', 'cornerstone') ],
            [ 'value' => 'is-in-stock', 'label' => __('In Stock', 'cornerstone') ],
            [ 'value' => 'is-on-backorder', 'label' => __('On Backorder', 'cornerstone') ],
            [ 'value' => 'is-on-sale', 'label' => __('On Sale', 'cornerstone') ],
            [ 'value' => 'is-purchasable', 'label' => __('Purchasable', 'cornerstone') ],
            [ 'value' => 'is-shipping-taxable', 'label' => __('Shipping Taxable', 'cornerstone') ],
            [ 'value' => 'is-sold-individually', 'label' => __('Sold Individually', 'cornerstone') ],
            [ 'value' => 'is-taxable', 'label' => __('Taxable', 'cornerstone') ],
            [ 'value' => 'is-virtual', 'label' => __('Virtual', 'cornerstone') ],
            [ 'value' => 'is-visible', 'label' => __('Visible', 'cornerstone') ],
            [ 'value' => 'is-variable', 'label' => __('Variable', 'cornerstone') ],
          ]
        ]
      ], [
        'key'   => 'wc:product-has',
        'label' => __('Product (has)', 'cornerstone'),
        'toggle' => [
          'type' => 'boolean',
          'labels' => [
            __('has', 'cornerstone'),
            __('has not', 'cornerstone'),
          ]
        ],
        'criteria' => [
          'type'    => 'select',
          'choices' => [
            [ 'value' => 'has-image', 'label' => __('Image', 'cornerstone') ],
            [ 'value' => 'has-gallery', 'label' => __('Gallery', 'cornerstone') ],
            [ 'value' => 'has-reviews', 'label' => __('Reviews', 'cornerstone') ],
            [ 'value' => 'has-attributes', 'label' => __('Attributes', 'cornerstone') ],
            [ 'value' => 'has-child', 'label' => __('Child', 'cornerstone') ],
            [ 'value' => 'has-default-attributes', 'label' => __('Default Attributes', 'cornerstone') ],
            [ 'value' => 'has-dimensions', 'label' => __('Dimensions', 'cornerstone') ],
            [ 'value' => 'has-options', 'label' => __('Options', 'cornerstone') ],
            [ 'value' => 'has-weight', 'label' => __('Weight', 'cornerstone') ]
          ]
        ]
      ], [
        'key'   => 'wc:product-type',
        'label' => __('Product Type', 'cornerstone'),
        'toggle' => [
          'type' => 'boolean',
        ],
        'criteria' => [
          'type'    => 'select',
          'choices' => apply_filters( 'cs_conditions_wc_product_types', [
            [ 'value' => 'simple',    'label' => __('Simple', 'cornerstone') ],
            [ 'value' => 'external',  'label' => __('External', 'cornerstone') ],
            [ 'value' => 'variable',  'label' => __('Variable', 'cornerstone') ],
            [ 'value' => 'variation', 'label' => __('Variation', 'cornerstone') ],
          ] )
        ]
      ], [
        'key'    => 'wc:is-shop',
        'label'  => __('Shop', 'cornerstone'),
        'toggle' => [
          'type'   => 'boolean',
          'labels' => [
            sprintf(csi18n('app.conditions.is-condition'), csi18n('app.conditions.being-viewed') ),
            sprintf(csi18n('app.conditions.is-not-condition'), csi18n('app.conditions.being-viewed') )
          ]
        ],
        'criteria' => [ 'type' => 'static' ]
      ], [
        'key'    => 'wc:is-cart',
        'label'  => __('Cart', 'cornerstone'),
        'toggle' => [
          'type'   => 'boolean',
          'labels' => [
            sprintf(csi18n('app.conditions.is-condition'), csi18n('app.conditions.being-viewed') ),
            sprintf(csi18n('app.conditions.is-not-condition'), csi18n('app.conditions.being-viewed') )
          ]
        ],
        'criteria' => [ 'type' => 'static' ]
      ], [
        'key'    => 'wc:is-checkout',
        'label'  => __('Checkout', 'cornerstone'),
        'toggle' => [
          'type'   => 'boolean',
          'labels' => [
            sprintf(csi18n('app.conditions.is-condition'), csi18n('app.conditions.being-viewed') ),
            sprintf(csi18n('app.conditions.is-not-condition'), csi18n('app.conditions.being-viewed') )
          ]
        ],
        'criteria' => [ 'type' => 'static' ]
      ], [
        'key'    => 'wc:is-account',
        'label'  => __('Account', 'cornerstone'),
        'toggle' => [
          'type'   => 'boolean',
          'labels' => [
            sprintf(csi18n('app.conditions.is-condition'), csi18n('app.conditions.being-viewed') ),
            sprintf(csi18n('app.conditions.is-not-condition'), csi18n('app.conditions.being-viewed') )
          ]
        ],
        'criteria' => [ 'type' => 'static' ]
      ]
    ];
  }

  public function assignment_context_single() {

    $conditions = [
      [
        'key'    => "single:product",
        'label'  => __('All Products'),
      ]
    ];

    $post_type = 'product';
    $post_type_obj = get_post_type_object( $post_type );

    $conditions[] = [
      'key'    => "single:specific-post-of-type|$post_type",
      'label'  => sprintf(__('%s (Specific)', 'cornerstone'), $post_type_obj->labels->singular_name),
      'toggle' => ['type' => 'boolean'],
      'criteria' => [
        'type'    => 'select',
        'choices' => "posts:$post_type"
      ]
    ];

    $post_type_taxonomies = get_object_taxonomies($post_type);

    foreach ($post_type_taxonomies as $taxonomy) {
      if ($taxonomy === 'post_format') {
        continue;
      }

      $taxonomy_obj = get_taxonomy($taxonomy);

      $conditions[] = [
        'key'    => "single:post-type-with-term|$post_type|$taxonomy",
        'label'  => sprintf(_x('%s %s', '[Post Type] [Post Taxonomy]', 'cornerstone'), $post_type_obj->labels->singular_name, $taxonomy_obj->labels->singular_name),
        'toggle' => ['type' => 'boolean'],
        'criteria' => [
          'type'    => 'select',
          'choices' => "terms:$taxonomy"
        ]
      ];
    }

    if ($post_type_obj->hierarchical) {

      $conditions[] = [
        'key'    => "single:parent|$post_type",
        'label'  => sprintf(__('%s Parent', 'cornerstone'), $post_type_obj->labels->singular_name),
        'toggle' => ['type' => 'boolean'],
        'criteria' => [
          'type'    => 'select',
          'choices' => "posts:$post_type"
        ]
      ];

      $conditions[] = [
        'key'    => "single:ancestor|$post_type",
        'label'  => sprintf(__('%s Ancestor', 'cornerstone'), $post_type_obj->labels->singular_name),
        'toggle' => ['type' => 'boolean'],
        'criteria' => [
          'type'    => 'select',
          'choices' => "posts:$post_type"
        ]
      ];

      if (post_type_supports($post_type, 'page-attributes')) {
        $conditions[] = [
          'key'    => "single:page-template|$post_type",
          'label'  => sprintf(__('%s Template', 'cornerstone'), $post_type_obj->labels->singular_name),
          'toggle' => ['type' => 'boolean'],
          'criteria' => [
            'type'    => 'select',
            'choices' => cs_get_page_template_options($post_type)
          ]
        ];
      }
    }

    if (post_type_supports($post_type, 'post-formats')) {
      $conditions[] = [
        'key'    => "single:format|$post_type",
        'label'  => sprintf(__('%s Format', 'cornerstone'), $post_type_obj->labels->singular_name),
        'toggle' => ['type' => 'boolean'],
        'criteria' => [
          'type'    => 'select',
          'choices' => cs_get_post_format_options()
        ]
      ];
    }

    $conditions[] = [
      'key'    => "single:publish-date|$post_type",
      'label'  => sprintf(__('%s Publish Date', 'cornerstone'), $post_type_obj->labels->singular_name),
      'toggle' => [
        'type'   => 'boolean',
        'labels' => [csi18n('app.conditions.before'), csi18n('app.conditions.after')]
      ],
      'criteria' => ['type' => 'date-picker'],
    ];

    $conditions[] = [
      'key'    => "single:status|$post_type",
      'label'  => sprintf(__('%s Status', 'cornerstone'), $post_type_obj->labels->singular_name),
      'toggle' => ['type' => 'boolean'],
      'criteria' => [
        'type'    => 'select',
        'choices' => cs_get_post_status_options()
      ]
    ];

    return array_merge( $conditions, [
      [
        'key'    => 'wc:is-cart',
        'label'  => __('Cart', 'cornerstone'),
        'toggle' => [
          'type'   => 'boolean',
          'labels' => [
            sprintf(csi18n('app.conditions.is-condition'), csi18n('app.conditions.being-viewed') ),
            sprintf(csi18n('app.conditions.is-not-condition'), csi18n('app.conditions.being-viewed') )
          ]
        ],
        'criteria' => [ 'type' => 'static' ]
      ], [
        'key'    => 'wc:is-checkout',
        'label'  => __('Checkout', 'cornerstone'),
        'toggle' => [
          'type'   => 'boolean',
          'labels' => [
            sprintf(csi18n('app.conditions.is-condition'), csi18n('app.conditions.being-viewed') ),
            sprintf(csi18n('app.conditions.is-not-condition'), csi18n('app.conditions.being-viewed') )
          ]
        ],
        'criteria' => [ 'type' => 'static' ]
      ], [
        'key'    => 'wc:is-account',
        'label'  => __('Account', 'cornerstone'),
        'toggle' => [
          'type'   => 'boolean',
          'labels' => [
            sprintf(csi18n('app.conditions.is-condition'), csi18n('app.conditions.being-viewed') ),
            sprintf(csi18n('app.conditions.is-not-condition'), csi18n('app.conditions.being-viewed') )
          ]
        ],
        'criteria' => [ 'type' => 'static' ]
      ]
    ]) ;
  }

  public function assignment_context_archive() {

    $conditions = [
      [
        'key'    => "archive:shop",
        'label'  => __('Shop'),
      ]
    ];

    $post_type = 'product';
    $post_type_obj = get_post_type_object( $post_type );
    $post_type_taxonomies = get_object_taxonomies($post_type);
    $taxonomy_options = [];
    $taxonomy_conditions = [];

    foreach ($post_type_taxonomies as $taxonomy) {

      $taxonomy_obj = get_taxonomy($taxonomy);
      if ( ! $taxonomy_obj->public ) {
        continue;
      }

      $taxonomy_options[] = ['value' => $taxonomy, 'label' => $taxonomy_obj->labels->singular_name];

      $taxonomy_conditions[] = [
        'key'    => "archive:post-type-with-term|$post_type|$taxonomy",
        'label'  => sprintf(_x('%s %s', '[Post Type] [Post Taxonomy]', 'cornerstone'), $post_type_obj->labels->singular_name, $taxonomy_obj->labels->singular_name),
        'criteria' => [
          'type'    => 'select',
          'choices' => "terms:$taxonomy"
        ]
      ];
    }

    $conditions[] = [
      'key'    => "archive:taxonomy|$post_type",
      'label'  => sprintf(_x('%s Taxonomy', 'cornerstone'), $post_type_obj->labels->singular_name),
      'toggle' => ['type' => 'boolean'],
      'criteria' => [
        'type'    => 'select',
        'choices' => $taxonomy_options
      ]
    ];

    return array_merge( $conditions, $taxonomy_conditions );
  }

  public function preview_context_single() {

    $post_type_obj = get_post_type_object( 'product' );

    return [
      [
        'key'    => "single:post-type|product",
        'label'  => $post_type_obj->labels->singular_name,
        'criteria' => [
          'type'    => 'select',
          'choices' => "posts:product"
        ]
      ], [
        'key'      => 'wc:is-cart',
        'label'    => __('Cart', 'cornerstone'),
        'criteria' => [
          'url' => wc_get_page_permalink( 'cart' )
        ]
      ], [
        'key'      => 'wc:is-checkout',
        'label'    => __('Checkout', 'cornerstone'),
        'criteria' => [
          'url' => wc_get_page_permalink( 'checkout' )
        ]
      ], [
        'key'      => 'wc:is-account',
        'label'    => __('Account', 'cornerstone'),
        'criteria' => [
          'url' => wc_get_page_permalink( 'account' )
        ]
      ]
    ];

  }

  public function preview_context_archive() {

    $archive = [
      [
        'key'      => 'archive:shop',
        'label'    => __('Shop', 'cornerstone'),
        'criteria' => [
          'url' => wc_get_page_permalink( 'shop' )
        ]
      ]
    ];

    $post_type = 'product';
    $post_type_obj = get_post_type_object( 'product' );
    $post_type_taxonomies = get_object_taxonomies($post_type);

    foreach ($post_type_taxonomies as $taxonomy) {
      if ($taxonomy === 'post_format') {
        continue;
      }

      $taxonomy_obj = get_taxonomy($taxonomy);

      $archive[] = [
        'key'    => "archive:post-type-with-term|$post_type|$taxonomy",
        'label'  => sprintf(_x('%s %s', '[Post Type] [Post Taxonomy]', 'cornerstone'), $post_type_obj->labels->singular_name, $taxonomy_obj->labels->singular_name),
        'criteria' => [
          'type'    => 'select',
          'choices' => "terms:$taxonomy"
        ]
      ];
    }

    return $archive;
  }

  public function is_wc_archive() {
    return is_shop() || is_product_tag() || is_product_category() || is_product_taxonomy();
  }

  public function detect_layout_type( $type ) {

    // WC Archive
    if ( is_woocommerce() && $this->is_wc_archive() ) {
      // Search for shop can be set to is_shop if post_type=product
      // This checks that it is in fact the shop page not the another archive
      if (is_search() && is_shop() && !apply_filters("cs_wc_archive_overwrite_search", false)) {
        // Check current page
        $urlPage = x_get_page_by_current_path();
        $post = get_post();

        // Top level id
        $id = !empty($urlPage)
          ? $urlPage->ID
          : $post->ID;

        // If it's a search this can also be an archive
        $shop_id = wc_get_page_id ('shop');

        // Shop is not the current page
        // And it's a search
        if ( $id !== $shop_id) {
          return $type;
        }
      }

      // Normal assignments
      return 'layout:archive-wc';
    }

    if ( is_woocommerce() || is_cart() || is_checkout() || is_account_page() ) {
      return 'layout:single-wc';
    }

    return $type;

  }

  // Generate WooCommerce structured data
  public function setupWooStructuredData() {
    // Backup filter
    if (!apply_filters('cs_woocommerce_structured_data_enabled', true)) {
      return;
    }

    // Add to footer where this is normally outputted
    // Action happens before priority 10
    // @see woocommerce/includes/class-wc-structured-data.php
    add_action('wp_footer', function() {
      // Check product
      global $product;

      // No product on this page
      if (!is_product() || empty($product)) {
        return;
      }

      // Generated structured data
      // WooCommerce outputs this later
      wc()->structured_data->generate_product_data($product);
    }, -10);
  }

  public function condition_rule_single_product() {
    return is_singular('product');
  }

  public function condition_rule_archive_shop() {
    return $this->is_wc_archive();
  }

  public function condition_rule_wc_product_is( $result, $args ) {

    list($type) = $args;

    global $product;

    if (empty($product)) {
      return false;
    }

    switch ($type) {
      case 'is-downloadable': {
        return $product->is_downloadable();
      }
      case 'is-featured': {
        return $product->is_featured();
      }
      case 'is-in-stock': {
        return $product->is_in_stock();
      }
      case 'is-on-backorder': {
        return $product->is_on_backorder();
      }
      case 'is-on-sale': {
        return $product->is_on_sale();
      }
      case 'is-purchasable': {
        return $product->is_purchasable();
      }
      case 'is-shipping-taxable': {
        return $product->is_shipping_taxable();
      }
      case 'is-sold-individually': {
        return $product->is_sold_individually();
      }
      case 'is-taxable': {
        return $product->is_taxable();
      }
      case 'is-virtual': {
        return $product->is_virtual();
      }
      case 'is-visible': {
        return $product->is_visible();
      }
      case 'is-variable': {
        return $product->is_type('variable');
      }
    }

    return $result;

  }

  public function condition_rule_wc_product_has( $result, $args ) {

    list($type) = $args;

    global $product;

    if (empty($product)) {
      return false;
    }

    switch ($type) {
      case 'has-image': {
        return !empty( $product->get_image_id() );
      }
      case 'has-gallery': {
        return !empty( $product->get_gallery_image_ids() );
      }
      case 'has-reviews': {
        return ! empty( get_comments_number( $product->get_id()) );
      }
      case 'has-attributes': {
        return $product->has_attributes();
      }
      case 'has-child': {
        return $product->has_child();
      }
      case 'has-default-attributes': {
        return $product->has_default_attributes();
      }
      case 'has-dimensions': {
        return $product->has_dimensions();
      }
      case 'has-options': {
        return $product->has_options();
      }
      case 'has-weight': {
        return $product->has_weight();
      }
    }

    return $result;

  }

  public function condition_rule_wc_is_shop( $result, $args ) {
    return is_shop();
  }

  public function condition_rule_wc_is_cart( $result, $args  ) {
    return is_cart();
  }

  public function condition_rule_wc_is_checkout( $result, $args  ) {
    return is_checkout();
  }

  public function condition_rule_wc_is_account( $result, $args  ) {
    return is_account_page();
  }

  public function condition_rule_wc_product_type( $result, $args  ) {
    list($type) = $args;

    global $product;

    if (empty($product)) {
      return false;
    }

    return $type === $product->get_type();

  }

  public function detect_terms_and_conditions_page() {
    $terms_page = get_post( wc_terms_and_conditions_page_id() );

    if ( $terms_page && cs_uses_cornerstone( $terms_page ) ) {
      remove_action('woocommerce_checkout_terms_and_conditions', 'wc_terms_and_conditions_page_content', 30);
      add_action('woocommerce_checkout_terms_and_conditions', [$this, 'format_terms_and_conditions_page_content'], 30);
    }
  }

  public function format_terms_and_conditions_page_content() {

    $terms_page_id = wc_terms_and_conditions_page_id();

    if ( ! $terms_page_id ) {
      return;
    }

    $page = get_post( $terms_page_id );

    if ( $page && 'publish' === $page->post_status && $page->post_content && ! has_shortcode( $page->post_content, 'woocommerce_checkout' ) ) {
      echo '<div class="woocommerce-terms-and-conditions" style="display: none; max-height: 200px; overflow: auto;">' . wc_format_content( wp_kses_post( do_shortcode( str_replace( '[cs_content]','[cs_content _p="' . $page->ID . '"]', $page->post_content ) ) ) ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
  }

}

Cornerstone_WooCommerce::instance()->setup();



// Dynamic Content
// =============================================================================


add_action( 'cs_dynamic_content_register', function() {
  Cornerstone_Dynamic_Content_WooCommerce::instance();
});

add_action( 'tco_routes', function() {
  Cornerstone_Dynamic_Content_WooCommerce::instance();
});

// Controls
require_once(__DIR__ . '/woocommerce/Controls/ProductSelect.php');

// Loopers
require_once(__DIR__ . '/woocommerce/Loopers/LinkProductLooper.php');
require_once(__DIR__ . '/woocommerce/Loopers/Upsell.php');
require_once(__DIR__ . '/woocommerce/Loopers/CrossSell.php');

class Cornerstone_Dynamic_Content_WooCommerce {

  protected static $instance;

  public function __construct() {
    add_filter( 'cs_dynamic_content_woocommerce', array( $this, 'supply_field' ), 10, 4 );
    add_action( 'cs_dynamic_content_setup', array( $this, 'register' ) );
    add_filter( 'cs_dynamic_content_post_id', array($this, 'shop_page_id') );
    add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'add_to_cart_fragment' ) );

    add_filter( 'cs_dynamic_options_product', [ $this, 'getProductChoices' ]);
    add_filter( 'cs_dynamic_options_wc_product_attributes', [ $this, 'getProductAttributes' ], 10, 2);
  }

  public static function instance() {
    if ( ! isset( self::$instance ) ) {
      self::$instance = new Cornerstone_Dynamic_Content_WooCommerce();
    }
    return self::$instance;
  }

  public function register() {
    cornerstone_dynamic_content_register_group(array(
      'name'  => 'woocommerce',
      'label' => csi18n('app.dc.group-title-woocommerce')
    ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'page_title',
      'group' => 'woocommerce',
      'label' => csi18n( 'app.dc.wc.page-title' )
    ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'shop_url',
      'group' => 'woocommerce',
      'label' => csi18n( 'app.dc.wc.shop-url' )
    ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'cart_url',
      'group' => 'woocommerce',
      'label' => csi18n( 'app.dc.wc.cart-url' )
    ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'checkout_url',
      'group' => 'woocommerce',
      'label' => csi18n( 'app.dc.wc.checkout-url' )
    ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'account_url',
      'group' => 'woocommerce',
      'label' => csi18n( 'app.dc.wc.account-url' )
    ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'terms_url',
      'group' => 'woocommerce',
      'label' => csi18n( 'app.dc.wc.terms-url' )
    ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'fallback_image',
      'type'  => 'image',
      'group' => 'woocommerce',
      'label' => csi18n( 'app.dc.wc.fallback-image' )
    ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'cart_items_raw',
      'group' => 'woocommerce',
      'label' => csi18n( 'app.dc.wc.cart-item-count-raw' )
    ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'cart_items',
      'group' => 'woocommerce',
      'label' => csi18n( 'app.dc.wc.cart-item-count' )
    ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'cart_total_raw',
      'group' => 'woocommerce',
      'label' => csi18n( 'app.dc.wc.cart-total-raw' )
    ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'cart_total',
      'group' => 'woocommerce',
      'label' => csi18n( 'app.dc.wc.cart-total' )
    ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'cart_subtotal_raw',
      'group' => 'woocommerce',
      'label' => csi18n( 'app.dc.wc.cart-subtotal-raw' )
    ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'cart_subtotal',
      'group' => 'woocommerce',
      'label' => csi18n( 'app.dc.wc.cart-subtotal' )
    ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'currency_symbol',
      'group' => 'woocommerce',
      'label' => csi18n( 'app.dc.wc.currency-symbol' )
    ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'product_price',
      'group' => 'woocommerce',
      'label' => csi18n( 'app.dc.wc.product-price' ),
      'controls' => array( 'product' )
    ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'product_regular_price',
      'group' => 'woocommerce',
      'label' => csi18n( 'app.dc.wc.product-regular-price' ),
      'controls' => array( 'product' )
    ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'product_sale_price',
      'group' => 'woocommerce',
      'label' => csi18n( 'app.dc.wc.product-sale-price' ),
      'controls' => array( 'product' )
    ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'product_price_html',
      'group' => 'woocommerce',
      'label' => csi18n( 'app.dc.wc.product-price-html' ),
      'controls' => array( 'product' )
    ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'product_sale_percentage_off',
      'group' => 'woocommerce',
      'label' => csi18n( 'app.dc.wc.product-sale-percentage-off' ),
      'controls' => array( 'product' )
    ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'product_id',
      'group' => 'woocommerce',
      'label' => csi18n( 'app.dc.wc.product-id' ),
      'controls' => array( 'product' )
    ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'product_class',
      'group' => 'woocommerce',
      'label' => csi18n( 'app.dc.wc.product-class' ),
      'controls' => array( 'product' )
    ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'product_sku',
      'group' => 'woocommerce',
      'label' => csi18n( 'app.dc.wc.product-sku' ),
      'controls' => array( 'product' )
    ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'product_title',
      'group' => 'woocommerce',
      'label' => csi18n( 'app.dc.wc.product-title' ),
      'controls' => array( 'product' )
    ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'product_url',
      'group' => 'woocommerce',
      'label' => csi18n( 'app.dc.wc.product-url' ),
      'controls' => array( 'product' )
    ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'product_short_description',
      'group' => 'woocommerce',
      'label' => csi18n( 'app.dc.wc.product-short-description' ),
      'controls' => array( 'product' )
    ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'product_image_id',
      'type'  => 'image',
      'group' => 'woocommerce',
      'label' => csi18n( 'app.dc.wc.product-image-id' ),
      'controls' => array( 'product' )
    ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'product_image',
      'type'  => 'image',
      'group' => 'woocommerce',
      'label' => csi18n( 'app.dc.wc.product-image' ),
      'controls' => array( 'product' )
    ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'product_gallery_ids',
      'type'  => 'array',
      'group' => 'woocommerce',
      'label' => csi18n( 'app.dc.wc.product-gallery-ids' ),
      'controls' => array( 'product' )
    ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'product_stock',
      'group' => 'woocommerce',
      'label' => csi18n( 'app.dc.wc.product-stock' ),
      'controls' => array( 'product' )
    ));

    // {{dc:woocommerce:product_variation_stock}}
    cornerstone_dynamic_content_register_field([
      'name'  => 'product_variation_stock',
      'group' => 'woocommerce',
      'label' => __('Variable Product Stock', CS_LOCALIZE),
      'controls' => [ 'product' ]
    ]);

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'product_rating_count',
      'group' => 'woocommerce',
      'label' => csi18n( 'app.dc.wc.product-rating-count' ),
      'controls' => array( 'product' )
    ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'product_average_rating',
      'group' => 'woocommerce',
      'label' => csi18n( 'app.dc.wc.product-average-rating' ),
      'controls' => array( 'product' )
    ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'product_review_count',
      'group' => 'woocommerce',
      'label' => csi18n( 'app.dc.wc.product-review-count' ),
      'controls' => array( 'product' )
    ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'product_description',
      'group' => 'woocommerce',
      'label' => csi18n( 'app.dc.wc.product-description' ),
      'controls' => array( 'product' )
    ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'product_additional_information',
      'group' => 'woocommerce',
      'label' => csi18n( 'app.dc.wc.product-additional-information' ),
      'controls' => array( 'product' )
    ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'product_reviews',
      'group' => 'woocommerce',
      'label' => csi18n( 'app.dc.wc.product-reviews' ),
      'controls' => array( 'product' )
    ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'product_weight',
      'group' => 'woocommerce',
      'label' => csi18n( 'app.dc.wc.product-weight' ),
      'controls' => array( 'product' )
    ));


    cornerstone_dynamic_content_register_field(array(
      'name'  => 'product_length',
      'group' => 'woocommerce',
      'label' => csi18n( 'app.dc.wc.product-length' ),
      'controls' => array( 'product' )
    ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'product_width',
      'group' => 'woocommerce',
      'label' => csi18n( 'app.dc.wc.product-width' ),
      'controls' => array( 'product' )
    ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'product_height',
      'group' => 'woocommerce',
      'label' => csi18n( 'app.dc.wc.product-height' ),
      'controls' => array( 'product' )
    ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'product_dimensions',
      'group' => 'woocommerce',
      'label' => csi18n( 'app.dc.wc.product-dimensions' ),
      'controls' => array( 'product' )
    ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'product_shipping_class',
      'group' => 'woocommerce',
      'label' => csi18n( 'app.dc.wc.product-shipping-class' ),
      'controls' => array( 'product' )
    ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'product_shipping_class_slug',
      'group' => 'woocommerce',
      'label' => csi18n( 'app.dc.wc.product-shipping-class-slug' ),
      'controls' => array( 'product' )
    ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'product_type',
      'group' => 'woocommerce',
      'label' => csi18n( 'app.dc.wc.product-type' ),
      'controls' => array( 'product' )
    ));

    // Product Attribute
    cornerstone_dynamic_content_register_field(array(
      'name'  => 'product_attribute',
      'group' => 'woocommerce',
      'label' => __('Product Attribute', CS_LOCALIZE),
      'deep' => true,
      'controls' => [ 'product', [
        'key' => 'attribute',
        'type' => 'select',
        'label' => __('Attribute', CS_LOCALIZE),
        'options' => [
          'choices' => 'dynamic:wc_product_attributes',
        ],
      ]]
    ));

    // Attributes
    cornerstone_dynamic_content_register_field(array(
      'name'  => 'product_attributes',
      'type'  => 'array',
      'group' => 'woocommerce',
      'label' => __('Product Attributes', CS_LOCALIZE),
      'controls' => [ 'product' ],
    ));


    // Dynamic Options
    require_once(__DIR__ . '/woocommerce/ProductDynamicOptions.php');
  }

  public function get_image_for_product( $product ) {
    $image_id = $product->get_image_id();

    if (!$image_id &&  $product->get_parent_id()) {
      $parent_product = wc_get_product( $product->get_parent_id() );
      if ( $parent_product ) {
        $image_id = $product->get_image_id();
      }
    }

    return $image_id;
  }

  public function supply_field( $result, $field, $args = array() ) {
    global $product;
    $og_product = $product;

    if ( 0 === strpos( $field, 'product') ) {
      $product = cornerstone('DynamicContent')->get_contextual_product( $args );

      if ( ! $product ) {
        $product = $og_product;
        return $result;
      }
    }

    if ( 0 === strpos( $field, 'cart') && ! WC()->cart ) {
      return $result;
    }

    switch ( $field ) {
      case 'page_title': {
        $result = woocommerce_page_title( false );
        break;
      }
      case 'shop_url': {
        $result = wc_get_page_permalink( 'shop' );
        break;
      }
      case 'cart_url': {
        $result = wc_get_page_permalink( 'cart' );
        break;
      }
      case 'checkout_url': {
        $result = wc_get_page_permalink( 'checkout' );
        break;
      }
      case 'account_url': {
        $result = wc_get_page_permalink( 'myaccount' );
        break;
      }
      case 'terms_url': {
        $result = wc_get_page_permalink( 'terms' );
        break;
      }
      case 'fallback_image':
        $result = wc_placeholder_img_src( isset( $args['size'] ) ? $args['size'] : 'woocommerce_thumbnail' );
        break;
      case 'cart_items_raw': {
        $result = WC()->cart->get_cart_contents_count();
        break;
      }
      case 'cart_items': {
        $result = $this->render_cart_items();
        break;
      }
      case 'cart_total_raw': {
        $result = WC()->cart->get_cart_total();
        break;
      }
      case 'cart_total': {
        $result = $this->render_cart_total();
        break;
      }
      case 'cart_subtotal_raw': {
        $result = WC()->cart->get_cart_subtotal();
        break;
      }
      case 'cart_subtotal': {
        $result = $this->render_cart_subtotal();
        break;
      }
      case 'currency_symbol': {
        $result = get_woocommerce_currency_symbol();
        break;
      }
      case 'product_price_html': {
        $result = $product->get_price_html();
        break;
      }
      case 'product_price': {
        $result = $this->format_price( $product->get_price(), $args, $product );
        break;
      }
      case 'product_regular_price': {
        // Variation products returns the sale price
        // from get regular price
        // so we check if it's a variation product, by checking
        // if the variation specific method exists
        $price = method_exists($product, 'get_variation_regular_price')
          ? $product->get_variation_regular_price()
          : $product->get_regular_price();
        $result = $this->format_price( $price, $args, $product );
        break;
      }
      case 'product_sale_price': {
        $result = $this->format_price( $product->get_sale_price(), $args, $product );
        break;
      }
      case 'product_sale_percentage_off': {

        $sale_price = $product->get_sale_price();

        if ($sale_price) {
          $result =  round(100 - ( (float) $sale_price / (float) $product->get_regular_price() * 100));
        }

        break;
      }
      case 'product_id': {
        $result = $product->get_id();
        break;
      }
      case 'product_sku': {
        $result = $product->get_sku();
        break;
      }
      case 'product_title': {
        $result = $product->get_title();
        break;
      }
      case 'product_url': {
        $result = $product->get_permalink();
        break;
      }
      case 'product_short_description': {
        $result = $product->get_short_description();
        break;
      }
      case 'product_image_id':
        $size = isset( $args['size'] ) ? $args['size'] : 'woocommerce_thumbnail';
        $image_id = $this->get_image_for_product( $product );
        $result = $image_id ? "$image_id:$size" : '';
        break;
      case 'product_image': {
        $size = isset( $args['size'] ) ? $args['size'] : 'woocommerce_thumbnail';
        $image_id = $this->get_image_for_product( $product );

        if ( $image_id ) {
          $result = cs_resolve_image_source( $image_id, $size );
        } else {
          $placeholder = wc_placeholder_img_src( $size );
          if ($placeholder) {
            $result = $placeholder;
          }
        }

        break;
      }
      case 'product_stock': {
        $result = $product->get_stock_quantity();
        break;
      }
      case 'product_variation_stock': {
        // https://stackoverflow.com/questions/30514596/how-get-variations-stock-quantity-woocommerce
        // Get variations and combine their stock
        $stock = 0;

        // Is variable product
        if (method_exists($product, 'get_available_variations')) {
          $variations = $product->get_available_variations();
          foreach($variations as $variation){
            $variation_id = $variation['variation_id'];
            $variation_obj = new WC_Product_variation($variation_id);
            $stock += $variation_obj->get_stock_quantity();
          }
        }

        // Changed results
        $result = $stock;
        break;
      }
      case 'product_rating_count': {
        $result = $product->get_rating_count();
        break;
      }
      case 'product_average_rating': {
        $result = $product->get_average_rating();
        break;
      }
      case 'product_review_count': {
        $result = $product->get_review_count();
        break;
      }
      case 'product_class': {
        $result = implode( ' ', wc_get_product_class( '', $product ) );
        break;
      }
      case 'product_description': {
        ob_start();
        the_content();
        $result = ob_get_clean();
        break;
      }
      case 'product_additional_information': {
        $result = '';
        if ( isset( $product ) && ( $product->has_attributes() || apply_filters( 'wc_product_enable_dimensions_display', $product->has_weight() || $product->has_dimensions() ) ) ) {
          ob_start();
          do_action( 'woocommerce_product_additional_information', $product );
          $result = ob_get_clean();
        }
        break;
      }
      case 'product_reviews': {
        ob_start();
        comments_template();
        $result = ob_get_clean();
        break;
      }
      case 'product_weight': {
        $result = $product->has_weight() ? $product->get_weight() : '';
        break;
      }
      case 'product_length': {
        $result = $product->get_virtual() ? '' :  $product->get_length();
        break;
      }
      case 'product_width': {
        $result = $product->get_virtual() ? '' :  $product->get_width();
        break;
      }
      case 'product_height': {
        $result = $product->get_virtual() ? '' :  $product->get_height();
        break;
      }
      case 'product_dimensions': {
        $result = apply_filters( 'woocommerce_product_dimensions', wc_format_dimensions( $product->get_dimensions( false ) ), $product );
        break;
      }
      case 'product_shipping_class': {
        $term_id = $product->get_shipping_class_id();
        if ( $term_id ) {
          $term = get_term( $product->get_shipping_class_id(), 'product_shipping_class' );
          if (is_a( $term, 'WP_Term' ) ) {
            $result = $term->name;
          }
        }
        break;
      }
      case 'product_shipping_class_slug': {
        $result = $product->get_shipping_class();
        break;
      }
      case 'product_type': {
        $result = $product->get_type();
        break;
      }
      case 'product_gallery_ids': {
        $attachment_ids = $product->get_gallery_image_ids();
        $result = is_array( $attachment_ids ) ? $attachment_ids : [];
        break;
      }
      case 'product_attributes': {
        $result = [];
        $attributes = $product->get_attributes();
        foreach ($attributes as $attribute) {
          // These are all protected keys
          $result[] = [
            'id' => $attribute->get_id(),
            'name' => $attribute->get_name(),
            'options' => $attribute->get_options(),
            'position' => $attribute->get_position(),
            'visible' => $attribute->get_visible(),
            'variation' => $attribute->get_variation(),
          ];
        }
        break;
      }
      case 'product_attribute': {
        $attribute = cs_get_array_value($args, 'attribute', '');
        $result = $product->get_attribute($attribute);
        break;
      }
    }

    // If changed dont change the global product
    $product = $og_product;

    return $result;

  }

  public function render_cart_items() {
    return sprintf('<span data-csdc-wc="cart-items">%d</span>', WC()->cart->get_cart_contents_count());
  }

  public function render_cart_total() {
    return sprintf('<span data-csdc-wc="cart-total">%s</span>', WC()->cart->get_cart_total());
  }

  public function render_cart_subtotal() {
    return sprintf('<span data-csdc-wc="cart-subtotal">%s</span>', WC()->cart->get_cart_subtotal());
  }

  public function add_to_cart_fragment( $fragments ) {

    $fragments['[data-csdc-wc="cart-items"]']    = $this->render_cart_items();
    $fragments['[data-csdc-wc="cart-total"]']    = $this->render_cart_total();
    $fragments['[data-csdc-wc="cart-subtotal"]'] = $this->render_cart_subtotal();
    return $fragments;

  }

  public function shop_page_id ( $id ) {
    if (
      is_shop()
      && ! in_the_loop()
      && is_main_query()
      && apply_filters( 'cs_looper_at_top_level', true )
    ) {
      return wc_get_page_id ('shop');
    }

    return $id;

  }

  // Variation of WC price that doesn't generate additional HTML tags and omits the currency symbol

  public function format_price( $price, $args, $product ) {

    if (isset( $args['format'] ) && $args['format'] === "raw") {
      return $price;
    }

    // Follow global setting for tax inclusive/exclusive
    $is_tax_inclusive = get_option( 'woocommerce_tax_display_shop' ) === 'incl';

    // Allow overwriting
    if ( isset( $args['tax_inclusive'] ) ) {
      if ( $is_tax_inclusive && $args['tax_inclusive'] === 'false' ) {
        $is_tax_inclusive = false;
      }
      if ( ! $is_tax_inclusive && $args['tax_inclusive'] === 'true' ) {
        $is_tax_inclusive = true;
      }
    }

    $price = floatval( $is_tax_inclusive ? wc_get_price_including_tax( $product, [ 'price' => $price ] ) : wc_get_price_excluding_tax( $product, [ 'price' => $price ] ) );

    $args = wp_parse_args( $args, [
      'decimal_separator'  => wc_get_price_decimal_separator(),
      'thousand_separator' => wc_get_price_thousand_separator(),
      'decimals'           => wc_get_price_decimals(),
      'price_format'       => get_woocommerce_price_format(),
    ] );

    $negative = $price < 0;
    $price    = apply_filters( 'raw_woocommerce_price', $negative ? $price * -1 : $price );
    $price    = apply_filters( 'formatted_woocommerce_price', number_format( $price, $args['decimals'], $args['decimal_separator'], $args['thousand_separator'] ), $price, $args['decimals'], $args['decimal_separator'], $args['thousand_separator'] );

    if ( apply_filters( 'woocommerce_price_trim_zeros', false ) && $args['decimals'] > 0 ) {
      $price = wc_trim_zeros( $price );
    }

    return ( $negative ? '-' : '' ) . $price;

  }

  /**
   * Products as a list of choices for a select box
   * Dynamic option
   */
  public function getProductChoices($args) {
    // Grab products
    $products = wc_get_products([
      'limit' => apply_filters( 'cs_locator_limit', 100 )
    ]);

    $options = [];

    // Safety
    if (empty($products)) {
      return $options;
    }

    // Convert to select box format
    foreach ($products as $product) {
      $options[] = [
        'value' => $product->get_id(),
        'label' => $product->get_title(),
      ];
    }

    return $options;
  }

  /**
   * Product attributes dynamic options
   */
  public function getProductAttributes($results = [], $args = []) {
    if (empty($args['context']['post'])) {
      return [];
    }

    $product = get_product($args['context']['post']);

    if (empty($product)) {
      return [];
    }

    // Convert attributes array objects to choices
    $attributes = $product->get_attributes();

    if (empty($attributes)) {
      return [];
    }

    $attributes = array_keys($attributes);
    return cs_array_as_choices($attributes);
  }
}


// Breadcrumb Label
// =============================================================================

add_filter( 'x_breadcrumb_post_type_archive', function( $item, $post_type_obj, $args ) {

  if ($post_type_obj->name === 'product' ) {
    if ( isset( $args['shop_label'] ) ) {
      $item['label'] = $args['shop_label'];
    } else {
      $item['label'] = get_the_title( wc_get_page_id( 'shop' ) );
    }
  }

  return $item;

}, 10, 3 );

// WooCommerce JS
add_action('wp_enqueue_scripts', function() {
  $asset = cs_js_asset_get('assets/js/site/cs-woocommerce');
  wp_register_script('cs-woocommerce', $asset['url'], ['cs'], $asset['version'], true);
  wp_enqueue_script('cs-woocommerce');
}, -1);
