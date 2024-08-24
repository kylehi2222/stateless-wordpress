<?php

const X_ICON_POST_FORMATS = [
  'standard' => 'f15c',
  'audio' => 'f001',
  'image' => 'f083',
  'gallery' => 'f03e',
  'quote' => 'f10d',
  'video' => 'f008',
  'link' => 'f0c1',
  'portfolio' => 'f067',
];

// Icon Helpers
function x_icon_get($icon, $className = '', $content = '', $type = 's') {
  // Not running CS 7.4+ or
  if (
    function_exists("cs_fa_icon_tag_from_unicode")
  ) {
    return cs_fa_icon_tag_from_unicode($icon, $className, $content, $type);
  }

  return "<i class='x-framework-icon $className' data-x-icon-$type='&#x$icon;' aria-hidden=true>{$content}</i>";
}

function x_icon_get_brand($icon, $className = '') {
  return x_icon_get($icon, $className, '', 'b');
}

// outputter of above
function x_icon_output($icon, $className = '', $content = '', $type = 's') {
  echo x_icon_get($icon, $className, $content, $type);
}

// Icon getter from FA name
function x_icon_fa($icon, $className, $fallbackUnicode = '') {
  if (!function_exists('cs_get_partial_view')) {
    return x_icon_get($fallbackUnicode, $className);
  }

  $icon_base = [
    'icon' => $icon,
    'atts' => [ 'class' => 'x-framework-icon ' . $className ],
  ];

  return cs_get_partial_view( 'icon', $icon_base );
}

function x_icon_post_format($className = '') {
  $format = get_post_format();
  $format = empty($format) ? 'standard' : $format;

  $icon = empty(X_ICON_POST_FORMATS[$format])
    ? X_ICON_POST_FORMATS['standard']
    : X_ICON_POST_FORMATS[$format];

  x_icon_output($icon, $className);
}

// Icon subindicator used in menus
function x_icon_subindicator($className = 'x-framework-icon-menu') {
  return x_icon_fa(x_get_option('x_navbar_subindicator_icon'), $className, "f103");
}


// Setup icons on xJsData
add_filter("x_site_jsdata", function($data) {
  $data['icons'] = [
    'down' => x_icon_get('f103', "x-icon-angle-double-down"),
    'subindicator' => x_icon_subindicator('x-icon-angle-double-down'),
    'previous' => x_icon_get('f053', "x-icon-previous"),
    'next' => x_icon_get('f054', "x-icon-next"),
    'star' => x_icon_get('f005', "x-icon-star"),
  ];

  return $data;
});
