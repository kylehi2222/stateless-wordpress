<?php

/**
 * Advanced extension
 * features like running any function or doing actions
 */

use Twig\Extension\StringLoaderExtension;

/**
 * Exec function helper
 */
function cs_twig_exec_function($function_name) {
    $args = \func_get_args();
    \array_shift($args);
    if (\is_string($function_name)) {
        $function_name = \trim($function_name);
    }
    return \call_user_func_array($function_name, ($args));
}


/**
 * Twig functions
 */
add_filter('cs_twig_functions', function($results) {
  $functions = [
    // Advanced
    'action' => [
      'callable' => function ($action_name, ...$args) {
        \do_action_ref_array($action_name, $args);
      },
    ],
    'function' => [
      'callable' => 'cs_twig_exec_function',
    ],
    'fn' => [
      'callable' => 'cs_twig_exec_function',
    ],
    'get_option' => [
      'callable' => 'get_option',
    ],
  ];

  return array_merge($results, $functions);
});


/**
 * Get Advanced filters
 */
add_filter('cs_twig_filters', function($results) {
  $filters = [
    'function' => [
      'callable' => 'cs_twig_exec_function',
    ],
  ];

  return array_merge($results, $filters);
});

/**
 * String loader extension
 */
add_action('cs_twig_boot', function($twig) {
  $twig->addExtension(new StringLoaderExtension());
});
