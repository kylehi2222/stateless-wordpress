<?php

/**
 * Cornerstone Boot
 */

if ( ! function_exists('cornerstone_boot') ) {

  function cornerstone_boot( $args ) {

    if ( function_exists('CS') ) {
      return;
    }

    if ( ! defined( 'ABSPATH' ) ) {
      die();
    }

    if (!defined("CS_ROOT_URL")) {
      define("CS_ROOT_URL", $args['url']);
    }

    if (!defined("CS_ROOT_PATH")) {
      define("CS_ROOT_PATH", $args['path']);
    }

    if (!defined('CS_ROOT_DIRECTORY')) {
      $justDirectory = str_replace(ABSPATH, '', CS_ROOT_PATH);
      define('CS_ROOT_DIRECTORY', '/' . $justDirectory);
    }

    if (!defined("CS_LOCALIZE")) {
      define("CS_LOCALIZE", "cornerstone");
    }

    // FontAwesome Version
    if (!defined('CS_FA_VERSION')) {
      define('CS_FA_VERSION', '6.5.2');
    }

    $path = $args['path'];

    $plugin_setup = apply_filters( '_cs_plugin_setup', $path . '/includes/setup.php', $path );

    if ( file_exists ( $plugin_setup ) ) {
      require_once( $plugin_setup );
    }

    require_once "$path/includes/classes/Plugin.php";
    require_once "$path/includes/classes/Util/IocContainer.php";
    require_once "$path/includes/classes/Util/Factory.php";

    \Themeco\Cornerstone\Plugin::instance()->initialize( apply_filters( 'cs_initialize', array_merge( $args, [

      'includes' => [
        '/includes/tco.php',
        '/includes/functions/helpers.php',
        '/includes/functions/theme-option-helpers.php',
        '/includes/functions/element-api.php',
        '/includes/functions/wordpress-helpers.php',
        '/includes/functions/date.php',
        '/includes/functions/api.php',
        '/includes/functions/icons.php',
        '/includes/functions/stack-api.php',
        [function_exists("curl_init"), '/includes/functions/curl-api.php'],
        '/includes/functions/preferences.php',
        '/includes/functions/globals.php',
        '/includes/functions/looper-api.php',
        '/includes/functions/parameters.php',
        '/includes/dynamiccontent/Random.php',
        '/includes/dynamiccontent/DateLibrary.php',
        '/includes/dynamiccontent/colors.php',
        '/includes/dynamiccontent/users.php',
        [apply_filters('cs_dynamic_content_extension_connection', true), '/includes/dynamiccontent/connection.php'],
        '/includes/functions/shim.php',
        '/includes/functions/filesystem.php',
        [is_admin(), '/includes/vendor/class-tgm-plugin-activation.php'],
        '/includes/integration/cs-tgma.php',
        '/includes/integration/cs-last-save.php',
        '/includes/integration/Max/max.php',
        '/includes/integration/Max/max-courses.php',
        '/includes/integration/Max/max-plugins.php',
        '/includes/integration/WordPress/wordpress.php',
        '/includes/integration/shortcodes.php',
        '/includes/integration/conflict-resolution.php',
        '/includes/integration/cs-sliders.php',
        '/includes/integration/cs-text-type.php',
        '/includes/integration/element-designations.php',
        [get_option("cs_csv_enabled", true), '/includes/integration/csv.php',],
        [function_exists("curl_init"), '/includes/integration/Api/ApiIntegration.php'],
        '/includes/admin/CornerstoneViewDetailsFix.php',

        '/includes/integration/QueryBuilder/Integration.php',

        '/includes/theme-options/typography.php',
        '/includes/theme-options/portfolio.php',
        '/includes/theme-options/fontawesome.php',
        '/includes/theme-options/social.php',
        '/includes/theme-options/layout.php',
        '/includes/theme-options/woocommerce.php',
        '/includes/theme-options/woocommerce-ajax-cart.php',
        '/includes/dynamiccontent/theme-options.php',
        '/includes/integration/GoogleFonts/GoogleFonts.php',
        // Twig needs to be called before init
        [ true, '/includes/integration/Twig/cornerstone-twig-renderer.php' ],
      ],

      // See includes/services/Service.php
      'services' => [
        'Themeco\Cornerstone\Services\Config',
        'Themeco\Cornerstone\Services\I18n',
        'Themeco\Cornerstone\Services\VersionMigration',
        'Themeco\Cornerstone\Services\CodebaseBridge',
        'Themeco\Cornerstone\Services\ThemeManagement',
        'Themeco\Cornerstone\Services\Permissions',
        'Themeco\Cornerstone\Services\ThemeOptions',
        'Themeco\Cornerstone\Services\GlobalColors',
        'Themeco\Cornerstone\Services\GlobalFonts',
        'Themeco\Cornerstone\Services\RemoteAssets',
        'Themeco\Cornerstone\Services\Vm',
        'Themeco\Cornerstone\Services\Elements',
        'Themeco\Cornerstone\Services\LegacyAssignments',
        'Themeco\Cornerstone\Services\Assignments',
        'Themeco\Cornerstone\Services\Resolver',
        'Themeco\Cornerstone\Services\DynamicContent',
        'Themeco\Cornerstone\Services\Theming',
        'Themeco\Cornerstone\Services\Locator',
        'Themeco\Cornerstone\Services\Routes',
        'Themeco\Cornerstone\Services\FrontEnd',
        'Themeco\Cornerstone\Services\AppBoot',
        'Themeco\Cornerstone\Services\Tss',
        'Themeco\Cornerstone\Services\Components',
        'Themeco\Cornerstone\Services\Revisions',
        'Themeco\Cornerstone\Services\Rivet',
        'Themeco\Cornerstone\Services\Admin',
        'Themeco\Cornerstone\Services\Templates',
        'Themeco\Cornerstone\Services\Styling',
        'Themeco\Cornerstone\Services\ShortcodeFinder',
        'Themeco\Cornerstone\Services\EnqueueScripts',
        'Themeco\Cornerstone\Services\Breadcrumbs',
        'Themeco\Cornerstone\Services\WpCli',
        'Themeco\Cornerstone\Services\MenuItemCustomFields',
        'Themeco\Cornerstone\Services\FontAwesome',
        'Themeco\Cornerstone\Services\Preview',
        'Themeco\Cornerstone\Services\Wpml',
        'Themeco\Cornerstone\Services\NavMenu',
        'Themeco\Cornerstone\Services\Validation',
      ],

      // Include integration files. Included during init action
      // Array entries use first index as condition to load the file or not
      'integrations' => function() {
        return [
          '/includes/integration/caching.php',
          '/includes/integration/FontAwesome/FontAwesome.php',
          [ true, '/includes/integration/jetpack.php' ],
          [ defined( 'X_SHORTCODES_VERSION' ), '/includes/integration/x-shortcodes.php'],
          [ function_exists( 'wpforms' ), '/includes/integration/wpforms.php' ],
          [ class_exists( 'WPCF7_ContactForm' ), '/includes/integration/contact-form-7.php' ],
          [ class_exists( 'Essential_Grid' ), '/includes/integration/essential-grid.php' ],
          [ class_exists( 'RGFormsModel' ), '/includes/integration/gravityforms.php' ],
          [ class_exists( 'Vc_Manager' ), '/includes/integration/wp-bakery.php' ],
          [ class_exists( 'ACF' ), '/includes/integration/acf.php' ],
          [ class_exists( 'WooCommerce' ), '/includes/integration/woocommerce.php' ],
          [ class_exists( 'MEC' ), '/includes/integration/mec.php' ],
          [ class_exists( 'bbPress' ), '/includes/integration/bbpress.php' ],
          [ get_option("x_buddypress_enable", true) && class_exists( 'BuddyPress' ), '/includes/integration/buddypress.php' ],
          [ defined( 'WPCF_VERSION' ), '/includes/integration/toolset.php' ],
          [ function_exists( 'as3cf_init' ), '/includes/integration/offload-media.php' ],
          [ class_exists( 'RankMath' ), '/includes/integration/rankmath.php' ],
          [ defined( 'WP_ROCKET_VERSION' ), '/includes/integration/wp-rocket/wp-rocket.php' ],
          [ true, '/includes/integration/yoast.php' ],
          [ true, '/includes/integration/codemirror.php' ],
          [ true, '/includes/integration/tinymce.php' ],
          [ apply_filters("cs_scroll_top_enable", true), '/includes/integration/scroll-top.php' ],
          [ apply_filters("cs_graphql_enabled", true), '/includes/integration/graphql.php' ],
        ];
      },

      // Loaded by the Routes service only during REST API calls
      'controllers' => [
        'Themeco\Cornerstone\Controllers\LateData',
        'Themeco\Cornerstone\Controllers\Save',
        'Themeco\Cornerstone\Controllers\Documents',
        'Themeco\Cornerstone\Controllers\Templates',
        'Themeco\Cornerstone\Controllers\Preferences',
        'Themeco\Cornerstone\Controllers\AdobeFonts',
        'Themeco\Cornerstone\Controllers\Fonts',
        'Themeco\Cornerstone\Controllers\Choices',
        'Themeco\Cornerstone\Controllers\Locator',
        'Themeco\Cornerstone\Controllers\Formatting'
      ],

      // Elements
      // --------

      'control-partials' => [
        'anchor',
        'bg-layer',
        'bg',
        'bg-video',
        'cart',
        'content-area',
        'dropdown',
        'dynamic-rendering',
        'effects',
        'frame',
        'graphic',
        'html-editor',
        'comparison-select',
        'sql-direction',
        'seconds-select',
        'and-or',
        'looper-provider',
        'looper-consumer',
        'icon',
        'image',
        'mejs',
        'menu',
        'modal',
        'off-canvas',
        'omega',
        'products',
        'pagination',
        'particle',
        'range',
        'rating',
        'search',
        'separator',
        'text',
        'toggle',
        'toggleable',
      ],
      'element-definitions' => [
        'button',
        'deprecated-content-area',
        'deprecated-content-area-dropdown',
        'deprecated-content-area-modal',
        'deprecated-content-area-off-canvas',
        'comment-form',
        'comment-list',
        'comment-pagination',
        'form-integration',
        'component-direct',
        'deprecated-responsive-text',
        'accordion',
        'accordion-item',
        'accordion-item-elements',
        'tabs',
        'tab',
        'tab-elements',
        'icon',
        'image',
        'nav-collapsed',
        'nav-dropdown',
        'nav-inline',
        'deprecated-nav-modal',
        'nav-layered',
        'layout-div',
        'layout-row',
        'layout-column',
        'layout-modal',
        'layout-dropdown',
        'layout-off-canvas',
        'layout-slide-container',
        'layout-slide',
        'post-pagination',
        'post-nav',
        'search-inline',
        'deprecated-search-dropdown',
        'deprecated-search-modal',
        'card',
        'creative-cta',
        'map',
        'map-marker',
        'audio',
        'video',
        'social',
        'text',
        'headline',
        'quote',
        'testimonial',
        'breadcrumbs',
        'alert',
        'counter',
        'countdown',
        'rating',
        'raw-content',
        'the-content',
        'statbar',
        'slide-pagination',
        'line',
        'gap',
        'widget-area',
        'tp-wc-add-to-cart-form',
        'tp-wc-cart',
        'deprecated-tp-wc-cart-dropdown',
        'deprecated-tp-wc-cart-modal',
        'deprecated-tp-wc-cart-off-canvas',
        'tp-wc-cross-sells',
        'tp-wc-product-gallery',
        'tp-wc-product-pagination',
        'tp-wc-products',
        'tp-wc-related-products',
        'tp-wc-shop-notices',
        'tp-wc-shop-sort',
        'tp-wc-upsells',
        'section',
        // 'form-input',
        'lottie',
      ]
    ] ) ) );

  }

  function cornerstone($service = '') {
    $plugin = \Themeco\Cornerstone\Plugin::instance();
    return $service ? $plugin->service($service) : $plugin;
  }
}
