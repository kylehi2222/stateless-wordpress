<?php

use Themeco\Cornerstone\API\Extension;

use const Themeco\Cornerstone\API\BUILTIN_VALUES;

/**
 * Functions for interaction with cURL
 */

/**
 * Filter a return passed from an endpoint
 * Use extensions and a filter named the same
 *
 * @param string $result
 * @param string $type
 * @param array $args
 *
 * @return mixed
 */
function cs_api_filter_return($result, $type, $args = []) {
  $result = Extension::filterReturn($result, $type, $args);

  return apply_filters("cs_api_filter_return", $result, $type, $args);
}

/**
 * Identical to cs_api_filter_return accept this
 * sends its return to the endpoint
 * uses extensions and a filter named the same
 *
 * @param string $result
 * @param string $type
 * @param array $args
 *
 * @return mixed
 */
function cs_api_filter_request($result, $type, $args = []) {
  $result = Extension::filterRequest($result, $type, $args);

  return apply_filters("cs_api_filter_request", $result, $type, $args);
}

/**
 * Values used by api extensions
 * both return and request extensions values
 * are present in this associative array
 *
 * @return array
 */
function cs_api_extension_values() {
  return Extension::getExtensionValues();
}

/**
 * Using an endpoint and args grab a cache file
 *
 * @param string $endpoint
 * @param array $args
 *
 * @return string
 */
function cs_api_get_cache($endpoint, $args) {
  $filePath = cs_api_cache_file($endpoint, $args);

  // Not good cache
  if (!cs_api_file_passes_cache($filePath, $args)) {
    return null;
  }

  // Return file contents
  return file_get_contents($filePath);
}

/**
 * Does this current file match the cache time requirements
 *
 * @param string $filePath
 * @param array $args
 *
 * @return boolean
 */
function cs_api_file_passes_cache($filePath, $args = []) {
  // File checks
  if (!file_exists($filePath)) {
    return false;
  }

  // Cache time in seconds
  $cacheTime = (int)cs_get_array_value($args, 'cache_time', 10);

  // Only ever cache once
  if ($cacheTime === "once") {
    return true;
  }

  // get modified file time
  $mTime = filemtime($filePath);

  // Cache time expired
  if ((time() - $mTime) >= $cacheTime) {
    return false;
  }

  // Cache fine
  return true;
}

/**
 * Check internal allow list on endpoint
 *
 * @param string $endpoint
 *
 * @return bool
 */
function cs_api_check_allowlist($endpoint) {
  $allowlist = apply_filters("cs_api_allowlist", []);

  // Nothing in allow
  if (empty($allowlist)) {
    return $endpoint;
  }

  // Loop allow list and check
  foreach ($allowlist as $allowed) {
    // Invalid allowed passed
    if (empty($allowed)) {
      continue;
    }

    $allowed = preg_quote($allowed);
    preg_match("~^{$allowed}~i", $endpoint, $matches);

    if (!empty($matches)) {
      return true;
    }
  }

  return false;
}

/**
 * Save curl cache from endpoint and args
 *
 * @param string $endpoint
 * @param array $args
 * @param string $content
 *
 * @return int|false
 */
function cs_api_set_cache($endpoint, $args = [], $content = '') {
  $filePath = cs_api_cache_file($endpoint, $args);
  $content = is_array($content)
    ? json_encode($content)
    : $content;
  return file_put_contents($filePath, $content);
}

/**
 * Cache file create name
 *
 * @param string $endpoint
 * @param array $args
 */
function cs_api_cache_file($endpoint, $args = []) {
  $tmpDir = cs_api_cache_directory();

  // Make CS directory if needed
  wp_mkdir_p($tmpDir);

  return $tmpDir . urlencode($endpoint) . '-' . md5(json_encode($args));
}

/**
 * Get External API Cache directory
 *
 * @return string
 */
function cs_api_cache_directory() {
  $tmpDir = get_temp_dir() . 'cornerstone/';
  $tmpDir = apply_filters("cs_api_cache_directory", $tmpDir);

  return $tmpDir;
}

/**
 * Return type editor functions
 */


/**
 * Main register function for return type extensions
 *
 * @param string $type
 * @param array $config
 *
 * @return void
 */
function cs_api_register_return_type($type, $config = []) {
  Extension::registerReturnType($type, $config);
}

/**
 * Gets return types registered
 *
 * @return array
 */
function cs_api_return_types() {
  return Extension::getReturnTypes();
}

/**
 * An array of { value, label } objects
 * useable in CS select and choose controls
 *
 * @return array
 */
function cs_api_return_types_as_choices() {
  return Extension::getReturnTypesAsChoices();
}

/**
 * Selector and controls for return type extensions
 *
 * @return array
 */
function cs_api_return_type_controls() {
  return Extension::getReturnTypeControls();
}


/**
 * Request type editor functions
 */

/**
 * Main register function for a API request type extension
 *
 * @param string $type
 * @param array $config
 *
 * @return void
 */
function cs_api_register_request_type($type, $config) {
  Extension::registerRequestType($type, $config);
}

/**
 * Selector and controls for request type extensions
 *
 * @return array
 */
function cs_api_request_type_controls() {
  return Extension::getRequestTypeControls();
}

/**
 * Get all Global endpoints
 * uses a filter by the same name
 *
 * @return array
 */
function cs_api_global_endpoints() {
  return apply_filters("cs_api_global_endpoints", []);
}

/**
 * Globals like looper and theme option values
 *
 * @return array
 */
function cs_api_global_values() {
  $values = array_merge(
    BUILTIN_VALUES,
    cs_api_extension_values()
  );

  // Unset so we receive defaults from global
  foreach ($values as $key => $value) {
    $values[$key] = '';
  }

  // Run should still be here
  $values['run'] = true;

  return $values;
}

/**
 * Get endpoint by ID
 *
 * @param string $id
 *
 * @return array|null
 */
function cs_api_global_endpoint($id) {
  $endpoints = cs_api_global_endpoints();

  foreach ($endpoints as $endpoint) {
    if ($endpoint['id'] !== $id) {
      continue;
    }

    return $endpoint;
  }

  return null;
}
