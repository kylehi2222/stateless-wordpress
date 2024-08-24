<?php

/**
 * Get page by url for REQUEST_URI
 */
function x_get_page_by_current_path() {
  return x_get_page_by_url($_SERVER['REQUEST_URI']);
}

/**
 * Helper to get a page by url
 */
function x_get_page_by_url($url) {
  $parsedUrl = parse_url($url);

  if (empty($parsedUrl)) {
    return;
  }

  $path = empty($parsedUrl['path'])
    ? "/"
    : $parsedUrl['path'];

  return get_page_by_path($path);
}
