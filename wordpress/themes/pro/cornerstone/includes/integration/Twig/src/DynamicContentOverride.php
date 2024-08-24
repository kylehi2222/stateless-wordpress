<?php

/**
 * Overrides the built in cornerstone dynamic content rendering
 */

namespace Cornerstone\TwigIntegration;

class DynamicContentOverride {

  /**
   * Cornerstone filters
   */
  static public function register() {

    add_filter('cs_dynamic_content_after_render', [ self::class, 'overrideExpandString' ]);

  }


  /**
   * Override dynamic content with twig
   */
  static public function overrideExpandString($template = '') {
    return Renderer::render($template);
  }

}
