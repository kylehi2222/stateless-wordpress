<?php

require_once(__DIR__ . '/BuiltinExtensionFunctions.php');

/**
 * Get Wordpress default filters
 */
add_filter('cs_twig_filters', function($results) {
  $filters = [
    'array' => [
      'callable' => 'cs_to_array',
    ],

    // Array push
    'push' => [
      'callable' => function() {
        $args = func_get_args();
        $array = array_shift($args);

        foreach ($args as $value) {
          $array[] = $value;
        }

        return $array;
      },
    ],

    // Base64
    'base64_encode' => [
      'callable' => 'base64_encode',
    ],

    'base64_decode' => [
      'callable' => 'base64_decode',
    ],

    // Math ceiling and flooring
    'ceiling' => [
      'callable' => 'ceil',
    ],

    'floor' => [
      'callable' => 'floor',
    ],

    // File based
    'file_basename' => [
      'callable' => 'basename',
    ],

    'file_dirname' => [
      'callable' => 'dirname',
    ],

    // Array randomize / shuffle
    'shuffle' => [
      'callable' => function($array) {
        shuffle($array);

        return $array;
      },
    ],

    // MD5
    'md5' => [
      'callable' => 'md5',
    ],

    // Phone Number formatter
    'phone' => [
      'callable' => function($phoneNumber) {
        if (substr($phoneNumber, 0, 2) == "+1") {
          return preg_replace("/(\+1)(\d{3})(\d{3})(\d{4})/", "$1 ($2) $3-$4", $phoneNumber);
        }
        return preg_replace("/(\d{3})(\d{3})(\d{4})/", "($1) $2-$3", $phoneNumber);
      },
    ],

    // Readtime
    'readtime' => [
      'callable' => function($content, $wordsPerMinute = 0, $imageReadTimeSeconds = 0) {
        // Divide by zero check
        // And default wordsPerMinute setup
        if (empty($wordsPerMinute)) {
          $wordsPerMinute = apply_filters('cs_twig_readtime_words_per_minute', 200);
        }

        if (empty($imageReadTimeSeconds)) {
          $imageReadTimeSeconds = apply_filters('cs_twig_readtime_image_seconds', 7);
        }

        $words = str_word_count(strip_tags($content));
        $min = floor($words / $wordsPerMinute);

        $imageCount = preg_match_all('/<img/', $content);

        $imageMinutes = round( ($imageCount * $imageReadTimeSeconds) / 60 );

        $min += $imageMinutes;

        $return = max(1, $min);
        $return = $return > 1
          ? $return . ' ' . __('minutes', CS_LOCALIZE)
          : $return . ' ' . __('minute', CS_LOCALIZE);

        return $return;
      },
    ],

    'json_decode' => [
      'callable' => function($toDecode, $isArray = true) {
        return json_decode($toDecode, $isArray);
      },
    ],

    'time_ago' => [
      'callable' => 'cs_date_time_ago',
    ],

    'list' => [
      'callable' => 'cs_twig_add_list_separators',
    ],

    'relative' => [
      'callable' => 'cs_twig_get_rel_url'
    ],

    'pretags' => [
      'callable' => 'cs_twig_filter_pretags',
    ],

    'cs_font_family' => [
      'callable' => function($family) {
        return apply_filters('cs_css_post_process_font-family', $family);
      },
    ],

    'cs_font_weight' => [
      'callable' => function($weight) {
        return apply_filters('cs_css_post_process_font-weight', $weight);
      },
    ],

  ];

  return array_merge($results, $filters);
});
