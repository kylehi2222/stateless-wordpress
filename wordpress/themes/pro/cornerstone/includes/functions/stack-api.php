<?php

/**
 * Register custom stack
 *
 * @param array $config
 *
 * @return void
 */
function cs_stack_register($config = []) {
  // Add custom stack
  cornerstone('ThemeOptions')->addCustomStack($config);
}

/**
 * Register from array of stacks
 * Helper function of cs_stack_register
 *
 * @param array $stacks
 */
function cs_stacks_register($stacks = []) {
  foreach ($stacks as $stack) {
    cs_stack_register($stack);
  }
}


/**
 * Get all registered stacks
 *
 * @return array
 */
function cs_stacks_get_all() {
  return cornerstone('ThemeOptions')->getAllStacks();
}

/**
 * Get all registered stacks with built out controls
 * used specifically by the App
 *
 * @return array
 */
function cs_stacks_with_controls() {
  return cornerstone('ThemeOptions')->getAllStacksWithControls();
}

/**
 * Get registered custom stacks
 *
 * @return array
 */
function cs_stacks_get_custom() {
  return cornerstone('ThemeOptions')->getCustomStacks();
}

/**
 * Get available stack key options
 *
 * @return string[]
 */
function cs_stack_keys() {
  return cornerstone('ThemeOptions')->getKeys();
}

/**
 * Get current stack
 *
 * @return array
 */
function cs_stack_current() {
  return cornerstone('ThemeOptions')->getStack();
}

/**
 * Is current stack custom stack
 *
 * @return mixed
 */
function cs_stack_is_custom($stackName = null) {
  return cornerstone('ThemeOptions')->isCustomStack($stackName);
}

/**
 * Get individual theme value
 *
 * @return mixed
 */
function cs_stack_get_value($name = '') {
  return cornerstone('ThemeOptions')->get_value($name);
}

/**
 * Return the stacks custom CSS
 *
 * @return string
 */
function cs_stack_custom_css() {
  return cornerstone('ThemeOptions')->getCustomStackCSS();
}

/**
 * Register singular option
 *
 * @param string $name
 * @param string $value
 *
 * @return void
 */
function cs_stack_register_option($name, $value = '') {
  cs_stack_register_option([
    $name => $value,
  ]);
}

/**
 * Register stack options
 *
 * @param array $options
 *
 * @return void
 */
function cs_stack_register_options($options) {
  cornerstone_options_register_options($options);
}

// Other includes
require_once(__DIR__ . '/CustomStacks/comments.php');
