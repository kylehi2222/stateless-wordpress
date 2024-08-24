<?php

use Cornerstone\TwigIntegration\Renderer;

use const Cornerstone\TwigIntegration\TWIG_TEMPLATES;

/**
 * Templates grabber from both theme options and directory templates
 *
 * @return array
 */
function cs_twig_templates() {
  $templates = cs_stack_get_value(TWIG_TEMPLATES, []);

  $templates = apply_filters(TWIG_TEMPLATES, $templates);

  return $templates;
}

/**
 * Get directories to search for templates in twig
 * Uses filter 'cs_twig_directory_templates'
 *
 * @return array
 */
function cs_twig_directory_templates() {
  return apply_filters('cs_twig_directory_templates', []);
}

/**
 * Twig cache directory
 * uses filter 'cs_twig_cache_directory'
 * Defaults to WordPress temp directory + cornerstone/twig
 *
 * @return string
 */
function cs_twig_cache_directory() {
  return Renderer::getCacheDirectory();
}

/**
 * Render a twig string
 *
 * @param string
 *
 * @return string
 */
function cs_twig_render($str) {
  return Renderer::render($str);
}

/**
 * Get twig instance environment
 *
 * @return Twig\Environment
 */
function cs_twig_environment() {
  return Renderer::twigInstance();
}
