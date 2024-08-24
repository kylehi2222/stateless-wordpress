<?php

namespace Cornerstone\TwigIntegration;

use Throwable;
use Twig\Environment;
use Twig\FilesystemLoader;
use Twig\Extension\StringLoaderExtension;
use Twig\Loader\ArrayLoader;
use Twig\Loader\ChainLoader;
use Twig\TwigFilter;
use Twig\TwigFunction;
use TypeError;

class Renderer {
  static private $_twig;

  static public $CACHE = [];

  static public $ERROR_CACHE = [];

  const HAS_TWIG = '/{[{%#]/';

  // Twig singleton
  /**
   * @return Environment
   */
  static public function twigInstance() : Environment {
    // Not loaded yet
    if (empty(self::$_twig)) {
      // Theme Options loader
      // cs-templates
      $arrayLoaderOut = [];
      $templates = cs_stack_get_value('cs_twig_templates');
      foreach ($templates as $template) {
        $arrayLoaderOut['cs-template:' . $template['id']] = $template['template'];
      }

      $internalLoader = new ArrayLoader($arrayLoaderOut);

      // Setup array of loaders
      $allLoaders = [$internalLoader];

      // Loaders filter
      // Used by DirectoryLoader
      $allLoaders = apply_filters('cs_twig_loaders', $allLoaders);

      // Last loader because this will render anything
      $loader = new StringLoader();

      $allLoaders[] = $loader;

      // Build chain loader
      $chainLoader = new ChainLoader($allLoaders);

      $twig = self::$_twig = new Environment($chainLoader, [
        'cache' => cs_stack_get_value('cs_twig_cache')
          ? static::getCacheDirectory()
          : false,
        'debug' => static::isDebugMode(),
        'autoescape' => static::getAutoescape(),
      ]);

      // CS Twig DC extension
      $twig->addExtension(new TwigExtension());

      // State extension
      $twig->addExtension(new StateExtension());

      // Filters and functions
      static::addFilters($twig);
      static::addFunctions($twig);

      do_action('cs_twig_boot', $twig);
    }

    return self::$_twig;
  }

  /**
   * Get cache directory
   *
   * @return string
   */
  static public function getCacheDirectory() {
    return apply_filters('cs_twig_cache_directory', get_temp_dir() . 'cornerstone/twig/');
  }

  /**
   * Autoescape strategy or disabling
   *
   * @return string|boolean
   */
  static public function getAutoescape() {
    return cs_stack_get_value('cs_twig_autoescape')
      ? apply_filters('cs_twig_autoescape_strategy', 'html')
      : false;
  }

  /**
   * Twig running in debug mode
   *
   * @return boolean
   */
  static public function isDebugMode() {
    // Check if should run in debug mode
    $is_preview = did_action( 'cs_element_rendering' );
    $debugMode = $is_preview || cs_stack_get_value('cs_twig_extension_debug');

    // Filter
    return apply_filters('cs_twig_debug_mode', $debugMode);
  }

  /**
   * Render main
   *
   * @return string
   */
  static public function render($template = '', $vars = []) {
    if (!preg_match(static::HAS_TWIG, $template)) {
      return $template;
    }

    $twig = self::twigInstance();

    try {
      // Try Rendering
      return $twig->render($template, $vars);
    } catch (Throwable $e) {
      // if not twig debug
      if (!static::isDebugMode()) {
        return $template;
      }

      $errorMsg = $e->getMessage();

      // Dup error fix
      //if (!empty(static::$ERROR_CACHE[$errorMsg])) {
      if (!empty(static::$ERROR_CACHE['test'])) {
        return $template;
      }
      static::$ERROR_CACHE['test'] = true;

      $message = '<strong>Could not render Twig template :</strong> <br/><pre style="max-height: 25em; overflow: scroll">' . $errorMsg . '</pre>';

      $message = "<div style='background: #efd5d4; padding: 20px; color: #f5242d;'>{$message}</div>";

      //$template = $message . '<br>' . $template;
      $template = $template . '<br>' . $message;

      return $template;
    }
  }

  /**
   * Adds filters to Twig.
   *
   * @param \Twig\Environment $twig The Twig Environment.
   *
   * @return \Twig\Environment
   */
  static public function addFilters($twig)
  {
    $filters = apply_filters('cs_twig_filters', []);
    foreach ($filters as $name => $function) {
      $twig->addFilter(
        new TwigFilter(
          $name,
          $function['callable'],
          $function['options'] ?? []
        )
      );
    }
  }

  /**
   * Adds functions to Twig.
   *
   * @param \Twig\Environment $twig The Twig Environment.
   *
   * @return \Twig\Environment
   */
  static public function addFunctions($twig)
  {
    $functions = apply_filters('cs_twig_functions', []);

    foreach ($functions as $name => $function) {
      $twig->addFunction(
        new TwigFunction(
          $name,
          $function['callable'],
          $function['options'] ?? []
        )
      );
    }

    return $twig;
  }
}
