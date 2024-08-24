<?php

/**
 * Twig Renderer integration
 */

// Twig or Symfony requires PHP 8.1
if (!version_compare(phpversion(), '8.1', '>=')) {
  return;
}

// Globals
define('CS_TWIG_RENDERER_ROOT', __DIR__);

// Theme options setup
add_action('cs_theme_options_before_init', function() {
  require_once(__DIR__ . '/src/ThemeOptions.php');
});

// Boot
// Must boot after theme options
add_action('after_setup_theme', function() {
  // Twig not enabled
  if (!cs_stack_get_value('cs_twig_enabled')) {
    return;
  }

  // Autoload
  require_once(__DIR__ . '/vendor/autoload.php');

  // Boot
  Cornerstone\TwigIntegration\Boot::main();

  // Load in DC UI picker additions
  add_action('cs_dynamic_content_setup', function() {
    require_once(__DIR__ . '/src/DynamicContent/DynamicContent.php');
  }, 1000);

  // Load in dynamic options
  add_action('cs_dynamic_content_register', function() {
    require_once(__DIR__ . '/src/DynamicContent/DynamicOptions.php');
  });
}, 1000 );
