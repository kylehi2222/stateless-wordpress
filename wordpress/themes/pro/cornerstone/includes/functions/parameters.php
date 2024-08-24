<?php

use Themeco\Cornerstone\Util\ManagedParameters;

/**
 * Add a managed parameter to the list available in Cornerstone
 * Uses filter 'cs_parameters_managed'
 * @see ManagedParameters.php
 *
 * @param string $type
 * @param array $data
 *
 * @return void
 */
function cs_parameters_managed_register($type, $data = []) {
  if (!is_array($data)) {
    trigger_error('The data sent into `cs_parameters_managed_register` is not an array');
    return;
  }

  add_filter('cs_parameters_managed', function($managedParameters) use ($type, $data) {
    $managedParameters[$type] = $data;

    return $managedParameters;
  });
}

/**
 * Get all registered managed Parameters
 *
 * @see ManagedParameters::mangedTypes
 *
 * @return array
 */
function cs_parameters_managed() {
  return ManagedParameters::managedTypes();
}
