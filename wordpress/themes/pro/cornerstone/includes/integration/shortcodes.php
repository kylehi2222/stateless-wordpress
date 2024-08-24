<?php

/**
 * Shortcode support for various places
 */

namespace Themeco\Integration\Shortcode;

// Condition args shortcode process
add_filter('cs_condition_args', function($args) {
  // Loop args
  foreach ($args as $index => $arg) {
    if (!is_string($arg)) {
      continue;
    }

    $args[$index] = do_shortcode($arg);
  }

  return $args;
}, 0);
