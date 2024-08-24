<?php

namespace Themeco\Cornerstone\Services;

use Themeco\Cornerstone\Plugin;

class Breadcrumbs implements Service {

  protected $plugin;

  public function __construct(Plugin $plugin) {
    $this->plugin = $plugin;
  }

  public function setup() {
    add_filter( 'x_breadcrumbs', [$this, 'outputHtml' ], 10, 2 );
  }

  public function parseArgs($args) {

    $page_for_posts_id = get_option( 'page_for_posts' );

    $args = wp_parse_args( $args, [
      'item_before'               => '<li class="x-crumbs-list-item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">',
      'item_after'                => '</li>',
      'label_before'              => '<span itemprop="name">',
      'label_after'               => '</span>',
      'delimiter_before'          => '<span class="x-crumbs-delimiter">',
      'delimiter_after'           => '</span>',
      'delimiter_ltr'             => '&rarr;',
      'delimiter_rtl'             => '&larr;',
      'current_class'             => 'x-crumbs-current',
      'anchor_atts'               => [ 'class' => 'x-crumbs-link', 'itemtype' => 'http://schema.org/Thing', 'itemprop' => 'item' ],
      'include_meta'              => true,
      'home_label'                => __( 'Home', 'cornerstone' ),
      'search_label'              => __( 'Search Results', 'cornerstone' ),
      '404_label'                 => __( '404 (Page Not Found)', 'cornerstone' ),
      'archive_default_label'     => __( 'Archives', 'cornerstone' ),
      'blog_label'                => $page_for_posts_id ? get_the_title( $page_for_posts_id ) : __( 'Blog', 'cornerstone' ),
      'include_post_ancestry'     => true,
      'include_taxonomies'        => true,
      'include_post_type_archive' => true
    ] );

    // Apply old filters for backwards compatibility, and new x_breadcrumbs_args filter
    return apply_filters( 'x_breadcrumbs_args', apply_filters( 'x_breadcrumbs_items_args', apply_filters( 'x_breadcrumbs_data_args', $args) ) );
  }

  public function getItems( $args = [] ) {

    $args = empty( $args ) ? $this->parseArgs($args) : $args;

    $home = [
      'type'  => 'home',
      'url'   => home_url( '/' ),
      'label' => $args['home_label'],
    ];

    $index = 1;

    $items = array_map(function($item) use (&$index) {
      $item['index'] = $index++;
      return $item;
    }, array_filter( array_merge([ $home ], $this->generateItems( $args )) ));

    return array_values( apply_filters( 'x_breadcrumbs_data', $items, $args ) ); // wrap in array_values to retain pure indexes

  }

  public function outputHtml( $input = '', $args = [] ) {

    $args = $this->parseArgs($args);

    $data = $this->getItems( $args );
    $output    = '';
    $delimiter = ( is_rtl() ) ? $args['delimiter_rtl'] : $args['delimiter_ltr'];

    foreach ( $data as $index => $breadcrumb ) {


      $atts = array_merge( $args['anchor_atts'], [
        'href'    => $breadcrumb['url'],
        'classes' => []
      ]);

      $human_index = $index + 1;

      $is_last = $human_index === count( $data );

      if ( $is_last ) {
        $atts['classes'][] = $args['current_class'];
        $atts['title'] = __( 'You Are Here', 'cornerstone' );
      }

      // Setup classes for atts
      $initialClasses = cs_get_array_value($atts, 'class', '');
      $atts['classes'][] = $initialClasses;
      $atts['class'] = implode(' ', $atts['classes']);
      unset($atts['classes']);

      $crumb_output = [
        $args['item_before'],
        cs_tag('a', $atts, [
          $args['label_before'],
          $breadcrumb['label'],
          $args['label_after']
        ]),
      ];

      if ( ! $is_last && ! empty( $delimiter ) ) {
        $crumb_output[] = $args['delimiter_before'] . $delimiter . $args['delimiter_after'];
      }

      if ( $args['include_meta'] ) {
        $crumb_output [] = '<meta itemprop="position" content="' . $human_index . '">';
      }

      $crumb_output[] = $args['item_after'];
      $output .= implode('', $crumb_output);

    }

    return apply_filters( 'x_breadcrumbs_items', $output, $args );

  }

  public function generateItems( $args = array() ) {

    global $wp;

    // Begin Breadcrumbs
    // -----------------

    if ( is_front_page() ) {
      return [];
    }

    $q_obj = get_queried_object();

    // Allow plugin integrations to overwrite the breadcrumbs
    $integrated = apply_filters( 'x_breadcrumbs_integrate', [], $args );

    if ( ! empty( $integrated ) ) {
      return $integrated;
    }

    // Add Breadcrumbs
    // ---------------

    if ( is_home() ) {

      return [[
        'type'  => 'blog',
        'url'   => get_permalink( get_option( 'page_for_posts' ) ),
        'label' => $args['blog_label'],
      ]];

    }

    if ( is_search() ) {

      return [[
        'type'  => 'search',
        'url'   => add_query_arg( $wp->query_string, '', home_url( '/' ) ),
        'label' => $args['search_label'],
      ]];

    }

    if ( is_404() ) {

      return [[
        'type'  => '404',
        'url'   => home_url( $wp->request . '/' ),
        'label' => $args['404_label'],
      ]];

    }

    if ( is_author() ) {

      return [[
        'type'  => 'author',
        'url'   => get_author_posts_url( $q_obj ),
        'label' => __( 'Posts by ', 'cornerstone' ) . '&#8220;' . get_the_author() . '&#8221;',
      ]];

    }

    if ( empty( $q_obj ) ) {
      if ( is_date() ) {

        $crumbs = [];
        $y = get_query_var( 'year' );
        $m = get_query_var( 'monthnum' );
        $d = get_query_var( 'day' );

        if ( $y != 0 ) {
          $crumbs[] = array(
            'type'  => 'year',
            'url'   => get_year_link( $y ),
            'label' => $y,
          );
        }

        if ( $m != 0 ) {
          $crumbs[] = array(
            'type'  => 'month',
            'url'   => get_month_link( $y, $m ),
            'label' => get_the_date( 'F' ),
          );
        }

        if ( $d != 0 ) {
          $crumbs[] = array(
            'type'  => 'day',
            'url'   => get_day_link( $y, $m, $d ),
            'label' => $d,
          );
        }

        return $crumbs;

      }

      if ( is_archive() ) {

        return [[
          'type'  => 'archive_default',
          'url'   => home_url( $wp->request . '/' ),
          'label' => $args['archive_default_label'],
        ]];

      }

      return [];

    }

    // Notes
    // -----
    // Each block checks for and adds an archive index link (if present),
    // ancestor links (if present), and the current page.
    //
    // 01. Post Type Archive
    // 02. Posts
    // 03. Taxonomies

    if ( is_a( $q_obj, 'WP_Post_Type' ) ) {
      return [ $this->postTypeArchiveCrumb( $q_obj, $args ) ];
    }

    if ( is_a( $q_obj, 'WP_Post' ) ) { // 02

      $crumbs = $args['include_post_type_archive'] ? [ $this->postTypeArchiveCrumb( get_post_type_object( $q_obj->post_type ), $args ) ] : [];

      if ( $args['include_taxonomies'] ) {

        $term = null;
        $taxonomies = get_object_taxonomies($q_obj->post_type);

        foreach ($taxonomies as $taxonomy) {
          $query_var = $taxonomy === 'category' ? 'category_name' : $taxonomy;
          if (isset( $wp->query_vars[ $query_var ] )) {
            $term_path = explode('/', $wp->query_vars[ $query_var ]);
            $term_slug = array_pop( $term_path );
            $term = get_term_by( 'slug', $term_slug, $taxonomy );
            break;
          }
        }

        if ( is_a( $term, 'WP_Term' ) ) {
          $crumbs = array_merge( $crumbs, $this->termAncestryCrumbs( $term, $args ) );
        }

      }

      if ( $args['include_post_ancestry'] ) {
        $post_ancestors = array_reverse( get_ancestors( $q_obj->ID, $q_obj->post_type, 'post_type' ) );

        foreach ( $post_ancestors as $ancestor_id ) {
          $crumbs[] = array(
            'type'  => $q_obj->post_type,
            'url'   => get_permalink( $ancestor_id ),
            'label' => get_the_title( $ancestor_id ),
          );
        }
      }

      $crumbs[] = array(
        'type'  => $q_obj->post_type,
        'url'   => get_permalink( $q_obj->ID ),
        'label' => $q_obj->post_title,
      );

      return $crumbs;

    }

    if ( is_a( $q_obj, 'WP_Term' ) ) { // 03

      $crumbs = [];

      if ( $args['include_post_type_archive'] ) {

        $archive_tax = get_taxonomy( $q_obj->taxonomy );

        if ( $archive_tax && isset( $archive_tax->object_type[0] ) ) {
          $crumbs[] = $this->postTypeArchiveCrumb( get_post_type_object( $archive_tax->object_type[0] ), $args );
        }

      }


      return array_merge( $crumbs, $this->termAncestryCrumbs( $q_obj, $args ) );
    }

    return [];

  }

  /**
  * Post types archive breadcrumb
  *
  * @return array|false
  */
  public function postTypeArchiveCrumb( $post_type_obj, $args ) {
    $url = get_post_type_archive_link( $post_type_obj->name );

    // Default post type label
    $label = $post_type_obj->label;

    // Grab the Cornerstone page from URL and use
    // the page title if found if not use post type object
    if (!empty($url)) {
      $parsedUrlLabel = $this->getPostTypeLabelFromURL($url);

      if (!empty($parsedUrlLabel)) {
        $label = $parsedUrlLabel;
      }
    }

    return $url ? array_merge( [ 'type' => 'archive' ], apply_filters( 'x_breadcrumb_post_type_archive', [
      'url' => $url,
      'label' => $label,
    ], $post_type_obj, $args )) : false;
  }


  /**
  * Get archive page from URL, use page name
  * as Breadcrumb label
  */
  private function getPostTypeLabelFromURL($url) {
    $parsedUrl = parse_url($url);

    if (empty($parsedUrl)) {
      return;
    }

    $path = empty($parsedUrl['path'])
      ? "/"
      : $parsedUrl['path'];

    $urlPage = get_page_by_path($path);

    return !empty($urlPage)
      ? $urlPage->post_title
      : null;

  }

  public function termAncestryCrumbs($term, $args) {

    $crumbs = [];

    $tax_ancestors = array_reverse( get_ancestors( $term->term_id, $term->taxonomy, 'taxonomy' ) );

    foreach ($tax_ancestors as $ancestor_id ) {
      $ancestor = get_term( $ancestor_id );
      $crumbs[] = array(
        'type'  => $ancestor->taxonomy . '_' . $ancestor->slug,
        'url'   => get_term_link( $ancestor->term_id ),
        'label' => $ancestor->name,
      );
    }

    $crumbs[] = array(
      'type'  => $term->taxonomy . '_' . $term->slug,
      'url'   => get_term_link( $term->term_id ),
      'label' => $term->name,
    );


    return $crumbs;

  }


}
