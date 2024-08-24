<?php

/**
 * CSV Related integrations
 */
require_once(__DIR__ . '/csv/controls.php');


/**
 * Ready to setup
 */
add_action("after_setup_theme", function() {

  /**
   * CSV Looper
   */
  cs_looper_provider_register("csv", [
    'type' => 'csv',
    'label' => __('CSV', 'cornerstone'),

    // Controls
    // @see csv/controls
    'controls' => cs_csv_controls(),

    // Values
    'values' => [
      'type' => 'file',
      'file' => '',
      'content' => '',
      'has_header' => true,
      'delimiter' => ',',
    ],

    /**
     * Main
     */
    'filter' => function($result, $args = []) {
      // Get file contents
      // or raw content
      $isFile = empty($args['type']) || $args['type'] === 'file';

      $content = $isFile
        ? cs_get_array_value($args, 'file', '')
        : cs_get_array_value($args, 'content', '');

      // Parse with dynamic content too
      $content = cs_dynamic_content($content);
      $content = $isFile
        ? cs_resolve_attachment_source($content, true)
        : $content;

      return cs_csv_parse($content, $args);
    },
  ]);
});

/**
 * Parses CSV to an array of arrays
 *
 * @param string $content
 * @param array $args
 *
 * @return array
 */
function cs_csv_parse($content, $args = []) {
  // Get as file or considered raw CSV
  $content = file_exists($content) || filter_var($content, FILTER_VALIDATE_URL)
    ? file_get_contents($content)
    : $content;

  $content = cs_csv_format_header($content);
  $csvLines = explode("\n", $content);

  // Setup configs
  $hasHeader = cs_get_array_value($args, 'has_header', true);
  $delimiter = cs_get_array_value($args, 'delimiter', ',');

  // State and headers
  $first = true;
  $headers = [];

  $out = [];

  // Loop lines of CSV
  foreach ($csvLines as $line) {
    // Bad lines
    if (empty($line)) {
      continue;
    }

    // Parse CSV to array
    $csvLine = str_getcsv($line, $delimiter);

    // Setup headers
    if ($hasHeader && $first) {
      $headers = array_map("trim", $csvLine);
      $first = false;
      continue;
    }

    // Build into either keyed array
    // or into index based results
    $valuesOfLine = [];

    foreach ($csvLine as $index => $value) {
      $key = $hasHeader && !empty($headers[$index])
        ? $headers[$index]
        : $index;

      $valuesOfLine[$key] = $value;
    }

    // Push to results
    $out[] = $valuesOfLine;
  }

  return $out;
}

// Some CSV sends zero width unicode characters
// this removes them
function cs_csv_format_header($header) {
  return trim($header, "\xEF\xBB\xBF");
}
