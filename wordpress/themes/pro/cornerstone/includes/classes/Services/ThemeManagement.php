<?php

namespace Themeco\Cornerstone\Services;

use Themeco\Cornerstone\Plugin;

class ThemeManagement implements Service {

  protected $plugin;
  protected $settings;

  public function __construct(Plugin $plugin, Settings $settings) {
    $this->plugin = $plugin;
    $this->settings = $settings;
  }

  public function setup() {
    add_action(
      'after_setup_theme',
      [$this, 'afterThemeSetup'],
      apply_filters('cs_after_theme_setup_priority', 1000)
    );

    // This is running before any of the X theme code runs
    // This is an idea we want to do for cornerstone standalone
    // it will completely disable the theme and run all routing through itself
    // @TODO run this code if wanted AFTER x loads
    // to ensure the function x_bootstrap exists
    //if ( $this->isThemeless() ) {
      //$this->setupThemeLessMode();
    //}
  }

  public function getTemplateTypes() {
    return [ '404', 'archive', 'attachment', 'author', 'category', 'date', 'embed', 'frontpage', 'home', 'index', 'page', 'paged', 'privacypolicy', 'search', 'single', 'singular', 'tag', 'taxonomy' ];
  }

  public function isThemeless() {
    return $this->settings->get('themeless') && ! function_exists('x_bootstrap');
  }

  public function afterThemeSetup () {
    do_action( 'cs_detect_theme_support' );

    if ( current_theme_supports( 'cornerstone-legacy-portfolio' ) ) {
      require_once( $this->plugin->path . '/includes/extend/portfolio.php' );
    }

    if ( current_theme_supports( 'cornerstone-legacy-sidebars' ) ) {
      if (! function_exists( 'ups_options_init' ) ) {
        require_once( $this->plugin->path . '/includes/extend/custom-sidebars.php' );
      }
    }

    if ($this->isStandalone()) {
      // Addons for support
      require_once(__DIR__ . "/../../../includes/standalone/Standalone.php");
    }
  }

  public function isClassic() {
    if ( ! did_action('cs_detect_theme_support' ) ) {
      trigger_error('Unable to check if classic mode is enabled this early', E_USER_WARNING );
    }
    return current_theme_supports( 'cornerstone-managed' );
  }

  /**
  * Classic Elements have been enabled
  */
  public function isClassicElementsEnabled() {
    return cornerstone("Permissions")->adminHasPermission("element-library.classic");
  }

  public function compatibilityMode() {
    return ! $this->isThemeless() && ! current_theme_supports('cornerstone');
  }

  public function allowTheming() {
    return ! $this->compatibilityMode();
  }

  public function isStandalone() {
    if ( ! did_action('cs_detect_theme_support' ) ) {
      trigger_error('Unable to check if classic mode is enabled this early', E_USER_WARNING );
    }

    return !current_theme_supports( 'cornerstone-managed' );
  }

  public function setupThemeLessMode() {

    $themeless = function() {
      return '_cs-themeless_';
    };

    add_filter("validate_current_theme", "__return_false");
    add_filter("template", $themeless);
    add_filter("template_directory", $themeless, 0);
    add_filter("stylesheet_directory", $themeless, 0);

    add_action( 'init', function() {
      add_theme_support("menus");
      add_theme_support("post-thumbnails");
      add_theme_support("title-tag");
    });

    add_action( 'after_setup_theme', function() {
      add_theme_support("woocommerce");
      add_theme_support( 'wc-product-gallery-zoom' );
      add_theme_support( 'wc-product-gallery-lightbox' );
      add_theme_support( 'wc-product-gallery-slider' );
    });

    add_action('admin_menu', function() {
      remove_submenu_page( 'themes.php', 'site-editor.php' );
      remove_submenu_page( 'themes.php', 'themes.php' );
    });

    add_action('admin_head', function() {
      if ( get_current_screen()->id === "themes") {
        echo '<style>#wpbody-content .wrap > *:not(.notice) { display: none; }</style>';
      }
    });

    add_action('admin_notices', function() {

      if ( get_current_screen()->id === "themes") { ?>
        <div class="notice notice-warning">
          <p><?php _e( 'Cornerstone Themeless mode is enabled.', 'cornerstone' ); ?></p>
        </div>
      <?php }
    });

    $templateTypes = [ '404', 'archive', 'attachment', 'author', 'category', 'date', 'embed', 'frontpage', 'home', 'index', 'page', 'paged', 'privacypolicy', 'search', 'single', 'singular', 'tag', 'taxonomy' ];

    foreach ($templateTypes as $type) {
      add_filter( $type . '_template', [ $this, 'resolveTemplate' ], 10, 3);
    }

  }

  public function resolveTemplate( $template, $type, $templates) {

    if ($type === 'index' && ! $template || strpos($template, 'template-canvas.php') !== false) {
      $template = $this->plugin->path . '/includes/views/theming/layout-archive.php';
    }

    if ($type === 'singular' && ! $template) {
      $template = $this->plugin->path . '/includes/views/theming/layout-single.php';
    }
    return $template;
  }

}
