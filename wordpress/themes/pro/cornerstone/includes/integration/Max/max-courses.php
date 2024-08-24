<?php


/**
 * Get available max courses
 */
function cs_max_get_courses($refresh = false) {
  if (!apply_filters("cs_max_enabled", true)) {
    return [];
  }

  $cache = get_option("cs_max_courses", []);

  if (!$refresh && !empty($cache)) {
    return $cache;
  }

  $code = get_option( 'cs_product_validation_key' );
  if (empty($code)) {
    $code = 'no-code';
  }

  $url = defined("THEMECO_DOMAIN")
    ? \THEMECO_DOMAIN
    : 'https://theme.co';
  $url .= '/api-v2/max-packages/' . $code;

  $request = wp_remote_get( $url, array( 'timeout' => 15 ) );

  if (is_a($request, \WP_Error::class)) {
    return [
      'errors' => [
        'api' => $request->errors
      ]
    ];
  }

  $data = @json_decode($request["body"], true);

  update_option("cs_max_courses", $data);

  return $data;
}

/**
 * Has standalone support
 */
function cs_max_has_standalone() {
  $courses = cs_max_get_courses();

  return !empty($courses['hasMaxSupport']);
}


// Refresh courses on API update
add_action("themeco_update_api_response", function($data) {
  cs_max_get_courses(true);
});
