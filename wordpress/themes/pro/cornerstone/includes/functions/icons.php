<?php

const CS_ICON_POST_FORMATS = [
  'standard' => 'f15c',
  'audio' => 'f001',
  'image' => 'f083',
  'gallery' => 'f03e',
  'quote' => 'f10d',
  'video' => 'f008',
  'link' => 'f0c1',
  'portfolio' => 'f067',
];


/**
 * Gets an icon based on post format
 */
function cs_icon_post_format($className = '', $shouldEcho = false) {
  $format = get_post_format();
  $format = empty($format) ? 'standard' : $format;

  $icon = empty(CS_ICON_POST_FORMATS[$format])
    ? CS_ICON_POST_FORMATS['standard']
    : CS_ICON_POST_FORMATS[$format];

  $iconHTML = cs_fa_icon_tag_from_unicode($icon, $className);

  if ($shouldEcho) {
    echo $iconHTML;
    return;
  }

  return $iconHTML;
}
