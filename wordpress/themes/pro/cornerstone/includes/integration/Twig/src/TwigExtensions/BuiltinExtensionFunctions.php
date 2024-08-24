<?php

// rel url
function cs_twig_get_rel_url($url, $force = false) {
  $url_info = \parse_url($url);
  if (isset($url_info['host']) && $url_info['host'] != cs_get_host() && !$force) {
    return $url;
  }
  $link = '';
  if (isset($url_info['path'])) {
    $link = $url_info['path'];
  }
  if (isset($url_info['query']) && \strlen($url_info['query'])) {
    $link .= '?' . $url_info['query'];
  }
  if (isset($url_info['fragment']) && \strlen($url_info['fragment'])) {
    $link .= '#' . $url_info['fragment'];
  }

  return $link;
}


/**
 * Converts an array to common oxford comma sentence
 *
 * @param array $arr
 * @param string $first_delimiter
 * @param string $second_delimiter
 * @return string
 */
function cs_twig_add_list_separators($arr, $first_delimiter = ',', $second_delimiter = ' and')
{
    $length = \count($arr);
    $list = '';
    foreach ($arr as $index => $item) {
        if ($index < $length - 2) {
            $delimiter = $first_delimiter . ' ';
        } elseif ($index == $length - 2) {
            $delimiter = $second_delimiter . ' ';
        } else {
            $delimiter = '';
        }
        $list = $list . $item . $delimiter;
    }
    return $list;
}

/**
 * Removes pretags adder and entity helper
 *
 *
 * @param string  $content
 * @return string
 */
function cs_twig_filter_pretags($content) {
  return \preg_replace_callback('|<pre.*>(.*)</pre|isU', 'cs_twig_filter_convert_pre_entities', $content);
}

/**
 * Html entities replacement from pretags
 *
 * @param array   $matches
 * @return string
 */
function cs_twig_filter_convert_pre_entities($matches)
{
  return \str_replace($matches[1], \htmlentities($matches[1]), $matches[0]);
}

