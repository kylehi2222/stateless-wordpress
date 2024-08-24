<?php

/**
 * Main runner from DC or looper data to either
 * cache grab or makes curl request
 *
 * @param array args
 *
 * @return mixed
 */
function cs_api_run($args = []) {
  // Run not setup yet
  if (empty($args['run'])) {
    return [];
  }

  // Not needed any longer
  unset($args['run']);

  // Config
  $endpoint = cs_get_array_value($args, 'endpoint', '');
  $path = cs_get_array_value($args, 'path', '');
  $method = cs_get_array_value($args, 'method', 'GET');
  $body = cs_get_array_value($args, 'args', []);
  $headers = cs_get_array_value($args, 'headers', []);
  $returnType = cs_get_array_value($args, 'return_type', 'json');
  $requestType = cs_get_array_value($args, 'request_type', 'json');

  // Debug mode
  $debug = cs_get_array_value($args, 'debug', false);

  // Add path
  $endpoint .= $path;

  $cacheTime = (int)cs_get_array_value($args, 'cache_time', '');

  $dataKey = cs_get_array_value($args, 'data_key', '');

  $data = null;
  $isCache = false;

  // Check if in allowlist
  if (!cs_api_check_allowlist($endpoint)) {
    return [
      'errors' => [
        'Endpoint not in allowlist'
      ],
    ];
  }


  // Get from cache
  if (!empty($cacheTime)) {
    $cache = cs_api_get_cache($endpoint, $args);

    // Needed if was an error
    $cache = $debug
      ? cs_maybe_json_decode($cache)
      : $cache;

    if (!is_null($cache)) {
      $data = $cache;
      $isCache = true;
    }
  }

  // No data make request
  if ($data === null) {
    // this is encoded to us by CS
    $body = cs_maybe_json_decode($body);
    $headers = cs_maybe_json_decode($headers);

    // Filter body, used by extensions
    $body = cs_api_filter_request($body, $requestType, $args);

    // CurlOpts
    $timeout = cs_get_array_value($args, 'timeout', 7);
    $httpTimeout = cs_get_array_value($args, 'httpconnect_timeout', 7);

    // Make Request
    $data = cs_curl_request($endpoint, $method, [
      'args' => $body,
      'headers' =>  $headers,
      'curlOpts' => [
        'httpconnect_timeout' => $httpTimeout,
        'timeout' => $timeout,
        'debug' => $debug,
        'follow_redirect' => !empty($args['follow_redirect']),
      ],
    ]);

    // Setup cache
    if (!empty($cacheTime) && !is_null($data)) {
      cs_api_set_cache($endpoint, $args, $data);
    }
  }

  // Dont sent errors to filters
  if (!empty($data['errors'])) {
    return $data;
  }

  // Special filters for return types
  $curlReturnedData = $data;

  // Data to filter
  $toFilter = $debug
    ? $data['response']
    : $data;

  $data = cs_api_filter_return($toFilter, $returnType, $args);

  // If looping through key
  if ($dataKey) {
    $data = cs_get_path($data, $dataKey);
  }

  if ($debug) {
    $curlReturnedData['response'] = $data;
    $curlReturnedData['info']['is_cache'] = $isCache
      ? 'true'
      : 'false';
    return $curlReturnedData;
  }

  return $data;

}

/**
 * Using global_id as an arg merge with other arguments
 *
 * @param array $args
 *
 * @return mixed
 */
function cs_api_global_run($args = []) {
  // Global setup
  $globalId = cs_get_array_value($args, 'global_id', '');

  // No Global yet
  if (empty($globalId)) {
    return [];
  }

  // Find endpoint by id
  $endpoint = cs_api_global_endpoint($globalId);

  // No global setup yet or invalid
  if (empty($endpoint)) {
    trigger_error("Global endpoint not setup with ID : " . $globalId);
    return [];
  }

  // Merge valid keys together
  $args = cs_api_global_merge_with_args($args, $endpoint, 'args');
  $args = cs_api_global_merge_with_args($args, $endpoint, 'headers');

  // Prep so it doesnt overwrite
  cs_delete_empty($args);
  unset($args['global_id']);

  // @TODO merge headers and args if both JSON

  // Merge args
  $args = array_merge($endpoint, $args);

  $results = cs_api_run($args);

  return $results;

}

/**
 * Merge key if likewise array values
 *
 * @param array $args
 * @param array $endpoint
 * @param string $key
 *
 * @return array
 */
function cs_api_global_merge_with_args($args, $endpoint, $key) {
  // Get body or header data from endpoint
  $endpointBody = cs_get_array_value($endpoint, $key, []);
  $body = cs_get_array_value($args, $key, []);

  $endpointBody = cs_maybe_json_decode($endpointBody);
  $body = cs_maybe_json_decode($body);

  // If both set and bot are arrays
  // Otherwise itll use whatever is set
  if (
    !empty($body) && !empty($endpointBody)
    && is_array($body) && is_array($endpointBody)
  ) {
    $args[$key] = array_merge($endpointBody, $body);
  }

  return $args;
}
