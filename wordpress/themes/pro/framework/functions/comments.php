<?php

// Adds link out icon to author link
add_filter( 'get_comment_author_link', function($html, $author) {

  $icon = x_icon_get("f35d", "x-comment-author-icon");
  $html = str_replace($author, $icon . '<span>' . $author . '</span>', $html);

  return $html;
}, 10, 2);

add_filter("widget_nav_menu_args", function($args) {
  $unicode = is_rtl() ? "f054" : "f053";
  $args['link_after'] = x_icon_get($unicode, "x-framework-icon-menu")
    . (
        !empty($args['link_after'])
          ? $args['link_after']
          : ''
    );

  $args['link_before'] = x_icon_get('f0da', "x-framework-icon-initial", '', 'l')
    . (
        !empty($args['link_before'])
          ? $args['link_before']
          : ''
    );

  return $args;
}, 10, 1);
