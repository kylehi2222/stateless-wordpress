<?php

namespace Cornerstone\TwigIntegration;

class Boot {

  static public function main() {
    // Setup DC Override / extension
    DynamicContentOverride::register();

    require_once(__DIR__ . '/api.php');

    require_once(__DIR__ . '/Cache.php');

    // Cornerstone App data and codemirror integration
    require_once(__DIR__ . '/Cornerstone/App.php');

    // TSS integration
    require_once(__DIR__ . '/Tss.php');

    // Internationalization
    // Requires sudo apt install php-intl
    // https://stackoverflow.com/questions/6242378/fatal-error-class-intldateformatter-not-found
    // @TODO in a plugin
    // class_exists('IntlDateFormatter')

    require_once(__DIR__ . '/TwigExtensions/BuiltinExtension.php');

    // WordPress Extension
    if (cs_stack_get_value('cs_twig_extension_wordpress')) {
      require_once(__DIR__ . '/TwigExtensions/WordPressExtension.php');
    }

    // HTML Extra extension
    if (cs_stack_get_value('cs_twig_extension_html_extra')) {
      require_once(__DIR__ . '/TwigExtensions/HTMLExtra.php');
    }

    // String Extra extension
    if (cs_stack_get_value('cs_twig_extension_string_extra')) {
      require_once(__DIR__ . '/TwigExtensions/StringExtra.php');
    }

    // Child theme directory loader
    if (cs_stack_get_value('cs_twig_extension_directory_loader')) {
      require_once(__DIR__ . '/TwigExtensions/DirectoryLoader.php');
    }

    // Advanced Extension
    if (cs_stack_get_value('cs_twig_extension_advanced')) {
      require_once(__DIR__ . '/TwigExtensions/AdvancedExtension.php');
    }

    // Debug Extension
    if (cs_stack_get_value('cs_twig_extension_debug')) {
      require_once(__DIR__ . '/TwigExtensions/Debug.php');
    }

    // Prefabs
    add_action('cs_register_prefab_elements', function() {
      require_once(__DIR__ . '/Prefabs/Post.php');
    });
  }
}
