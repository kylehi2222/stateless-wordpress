<?php

/**
 * Run twig before processing on Custom CSS
 */
add_filter('cs_tss_post_detect_dynamic_content', function($value) {
  return cs_twig_render($value);
});
