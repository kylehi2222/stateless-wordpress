<?php

namespace Cornerstone\TwigIntegration;

use Twig\Environment;


/**
 * Twig functions
 */
add_filter('cs_twig_functions', function($results) {
  $functions = [
    // Post functions
    'get_post' => [
      'callable' => 'get_post',
    ],
    'get_attachment' => [
      'callable' => 'get_attachment',
    ],
    'get_posts' => [
      'callable' => 'get_posts',
    ],
    'get_attachment_by' => [
      'callable' => 'get_attachment_by',
    ],
    'get_term' => [
      'callable' => 'get_term',
    ],
    'get_terms' => [
      'callable' => 'get_terms',
    ],
    'get_user_by' => [
      'callable' => 'get_user_by',
    ],
    'get_users' => [
      'callable' => 'get_users',
    ],
    'wp_get_current_user' => [
      'callable' => 'wp_get_current_user',
    ],
    'get_comment' => [
      'callable' => 'get_comment',
    ],
    'get_comments' => [
      'callable' => 'get_comments',
    ],
    'get_the_excerpt' => [
      'callable' => 'get_the_excerpt',
    ],

    'shortcode' => [
      'callable' => 'do_shortcode',
    ],
    'do_shortcode' => [
      'callable' => 'do_shortcode',
    ],
    'bloginfo' => [
      'callable' => 'bloginfo',
    ],

    // Translation functions.
    '__' => [
      'callable' => '__',
    ],
    'translate' => [
      'callable' => 'translate',
    ],
    '_e' => [
      'callable' => '_e',
    ],
    '_n' => [
      'callable' => '_n',
    ],
    '_x' => [
      'callable' => '_x',
    ],
    '_ex' => [
      'callable' => '_ex',
    ],
    '_nx' => [
      'callable' => '_nx',
    ],
    '_n_noop' => [
      'callable' => '_n_noop',
    ],
    '_nx_noop' => [
      'callable' => '_nx_noop',
    ],
    'translate_nooped_plural' => [
      'callable' => 'translate_nooped_plural',
    ],
    'admin_url' => [
      'callable' => 'admin_url',
    ],
  ];

  return array_merge($results, $functions);
});


/**
 * Get Wordpress default filters
 */
add_filter('cs_twig_filters', function($results) {
  $filters = [
    /* image filters */
    //'resize' => [
    //'callable' => ['Timber\ImageHelper', 'resize'],
    //],
    //'retina' => [
    //'callable' => ['Timber\ImageHelper', 'retina_resize'],
    //],
    //'letterbox' => [
    //'callable' => ['Timber\ImageHelper', 'letterbox'],
    //],
    //'tojpg' => [
    //'callable' => ['Timber\ImageHelper', 'img_to_jpg'],
    //],
    //'towebp' => [
    //'callable' => ['Timber\ImageHelper', 'img_to_webp'],
    //],

    // Other filters.
    'strip_shortcodes' => [
      'callable' => 'strip_shortcodes',
    ],
    'excerpt' => [
      'callable' => 'wp_trim_words',
    ],
    //'excerpt_chars' => [
    //'callable' => ['Timber\TextHelper', 'trim_characters'],
    //],
    'sanitize' => [
      'callable' => 'sanitize_title',
    ],
    'shortcodes' => [
      'callable' => 'do_shortcode',
    ],
    'wpautop' => [
      'callable' => 'wpautop',
    ],
    //'pluck' => [
    //'callable' => ['Timber\Helper', 'pluck'],
    //],
    //'wp_list_filter' => [
    //'callable' => ['Timber\Helper', 'wp_list_filter'],
    //],

    //@TODO
    //'truncate' => [
    //'callable' => function ($text, $len) {
    //return TextHelper::trim_words($text, $len);
    //},
    //],

    // Numbers filters
    'size_format' => [
      'callable' => 'size_format',
    ],

    // Actions and filters.
    'apply_filters' => [
      'callable' => function () {
        $args = \func_get_args();
        $tag = \current(\array_splice($args, 1, 1));

        return \apply_filters_ref_array($tag, $args);
      },
    ],
  ];

  return array_merge($results, $filters);
});

/**
 * Adds escapers.
 *
 * @param \Twig\Environment $twig The Twig Environment.
 * @return \Twig\Environment
 */
add_action('cs_twig_boot', function($twig) {
  $esc_url = function (Environment $env, $string) {
    return \esc_url($string);
  };

  $wp_kses_post = function (Environment $env, $string) {
    return \wp_kses_post($string);
  };

  $esc_html = function (Environment $env, $string) {
    return \esc_html($string);
  };

  $esc_js = function (Environment $env, $string) {
    return \esc_js($string);
  };

  $esc_attr = function (Environment $env, $string) {
    return \esc_attr($string);
  };

  if (\class_exists('Twig\Extension\EscaperExtension')) {
    $escaper_extension = $twig->getExtension('Twig\Extension\EscaperExtension');
    $escaper_extension->setEscaper('esc_url', $esc_url);
    $escaper_extension->setEscaper('wp_kses_post', $wp_kses_post);
    $escaper_extension->setEscaper('esc_html', $esc_html);
    $escaper_extension->setEscaper('esc_js', $esc_js);
    $escaper_extension->setEscaper('esc_attr', $esc_attr);
  }

  return $twig;
});
