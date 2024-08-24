<?php

use Themeco\Cornerstone\Services\LooperProviders;

/**
 * Register looper provider
 *
 * @param string $name
 * @param array $config
 */
function cs_looper_provider_register($name, $config = []) {
  return LooperProviders::add($name, $config);
}

/**
 * Get as choices of array for a Cornerstone select box
 *
 * @return array
 */
function cs_looper_provider_choices() {
  return LooperProviders::getChoices();
}

/**
 * Get controls of registered providers
 *
 * @return array
 */
function cs_looper_provider_controls() {
  return LooperProviders::getControls();
}

/**
 * Return Providers with loopable key types
 *
 * @return array of strings
 */
function cs_looper_provider_loop_key_types() {
  $types = array_merge(
    [
      'json', 'key-array',
      'dc', 'custom',
    ],
    LooperProviders::loopKeyTypes()
  );

  return apply_filters("cs_looper_provider_loop_key_types", $types);
}
