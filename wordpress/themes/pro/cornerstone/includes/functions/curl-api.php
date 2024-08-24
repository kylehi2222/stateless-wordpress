<?php

/**
 * Make request via
 *
 * @param string $url
 * @param string $method
 * @param array $args
 *
 * @return mixed either raw string or if in debug mode
 * { info, errors, response } object
 */
function cs_curl_request($url, $method = "GET", $allArgs = []) {
  $args = cs_get_array_value($allArgs, 'args', []);
  $headers = cs_get_array_value($allArgs, 'headers', []);

  // Curl Args
  $curlOpts = cs_get_array_value($allArgs, 'curlOpts', []);
  $timeout = (int)cs_get_array_value($curlOpts, 'timeout', 7);
  $httpTimeout = (int)cs_get_array_value($curlOpts, 'httpconnect_timeout', 7);

  $curlDebug = !empty($curlOpts['debug']);

  // Checks
  if (!$url) {
    trigger_error("No URL passed to cs_curl_request");
    return;
  }

  $url = trim($url);

  $method = strtoupper($method);

  // GET Args
  if (!empty($args) && $method === "GET") {
    $url .=  is_array($args)
      ? '?' . http_build_query($args)
      : $args;
  }

  $session = curl_init();

  //options
  curl_setopt($session, CURLOPT_URL, $url);
  curl_setopt($session, CURLOPT_CUSTOMREQUEST, $method);
  curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($session, CURLOPT_FAILONERROR, true);

  // Add headers to response
  if ($curlDebug) {
    curl_setopt($session, CURLOPT_HEADER, true);
  }

  // Timeouts
  curl_setopt($session, CURLOPT_CONNECTTIMEOUT, $httpTimeout);
  curl_setopt($session, CURLOPT_TIMEOUT, $timeout);

  // Post body
  $data_string = "";
  if (!empty($args) && $method !== "GET") {
    $data_string = is_array($args)
      ? json_encode($args)
      : (string)$args;
    $headers['Content-Length'] = strlen($data_string);
  }

  // Headers
  curl_setopt($session, CURLOPT_HTTPHEADER, cs_curl_array_to_header_string($headers));

  // Follow Redirect
  curl_setopt($session, CURLOPT_FOLLOWLOCATION, !empty($curlOpts['follow_redirect']));

  //curl_setopt($session, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');

  if (!empty($data_string)) {
    curl_setopt($session, CURLOPT_POSTFIELDS, $data_string);
  }

  //exec
  $response = curl_exec($session);

  // Status / HTTP code
  $info = curl_getinfo($session);
  cs_delete_empty($info);

  // Parse headers to info
  if ($curlDebug) {
    // Cut header from response
    $header_size = curl_getinfo($session, CURLINFO_HEADER_SIZE);
    $header = substr($response, 0, $header_size);

    // Fix response data which also contains header
    $response = substr($response, $header_size);

    // Add parsed headers to info
    $info['headers'] = cs_curl_parse_header_to_array($header);
  }

  // Error
  if (curl_errno($session)) {
    $error_msg = curl_error($session);
    curl_close($session);
    return $curlDebug?
      [ 'errors' => $error_msg, 'info' => $info ]
      : $error_msg;
  }

  // Send Debug as well
  curl_close($session);

  // Return response now
  // if in debug mode send info curl object
  return !$curlDebug
    ? $response
    : [
      'response' => $response,
      'info' => $info
    ];
}

/**
 * Converts an array to valid header syntax for cURL
 * 'Content' => 'json'
 * to
 * 'Content: json'
 *
 * @param array $headers
 *
 * @return array
 */
function cs_curl_array_to_header_string($headers = []) {
  $out = [];

  if (empty($headers)) {
    return $out;
  }

  foreach ($headers as $key => $value) {
    // Regular header string assumed
    if (empty($value)) {
      $out[] = $key;
      continue;
    }

    // Safety for accidental :
    $header_key = str_replace(":", "", $key);

    // Add to headers as header proper string
    $out[] = $header_key . ': ' . $value;
  }

  return $out;
}

/**
 * Parse the raw header to an array more useable
 *
 * @param string $header
 *
 * @return array
 */
function cs_curl_parse_header_to_array($header) {
  $header = explode("\n", $header);
  $headerBuilt = [];

  foreach ($header as $headerField) {
    // Not a field like the initial statement
    // HTTP/2 200
    if (strpos($headerField, ':') === false) {
      continue;
    }

    $fieldSplit = explode(':', $headerField);
    $headerKey = array_shift($fieldSplit);
    $headerBuilt[$headerKey] = trim(join('', $fieldSplit));
  }

  return $headerBuilt;
}
