<?php

namespace Themeco\Cornerstone\Controllers;

use Themeco\Cornerstone\Services\Routes;
use Themeco\Cornerstone\Services\Locator;

class Choices {
  private $routes;
  private $locator;

  public function __construct(Routes $routes, Locator $locator) {
    $this->routes = $routes;
    $this->locator = $locator;
  }

  public function setup() {
    $this->routes->add_route('get', 'dynamic-choices', [$this, 'get_choices']);
    $this->routes->add_route('get', 'dynamic-choices', [$this, 'get_choices']);
    $this->routes->add_route('get', 'dynamic-content', [$this, 'get_dynamic_options']);
    add_filter('cornerstone_app_choices_menus', array( $this, 'get_menus' ) );
    add_filter('cornerstone_app_choices_sidebars', array( $this, 'get_sidebars' ) );
    add_filter('cornerstone_app_choices_documents-layout-header', array( $this, 'get_documents_layout_header' ) );
    add_filter('cornerstone_app_choices_documents-layout-single', array( $this, 'get_documents_layout_single' ) );
    add_filter('cornerstone_app_choices_documents-layout-footer', array( $this, 'get_documents_layout_footer' ) );
  }

  public function get_choices( $params ) {
    if ( ! isset( $params['type'] ) ) {
      return new \WP_Error( 'cornerstone', 'type not set' );
    }

    if ( $params['type'] === 'post-picker' ) {
      return $this->post_picker( $params );
    }

    // Needed to load in dynamic options
    do_action( 'cs_dynamic_content_setup' );

    $args = [
      'context' => cs_get_array_value($params, 'context', []),
    ];

    return apply_filters('cornerstone_app_choices_' . $params['type'], [], $args );
  }

  public function get_dynamic_options( $params ) {
    if ( ! isset( $params['type'] ) ) {
      return new \WP_Error( 'cornerstone', 'type not set' );
    }

    $type = $params['type'];
    return cs_dynamic_options_get($type, $params);
  }

  public function post_picker( $params ) {

		if ( ! current_user_can('edit_posts') ) {
      return new \WP_Error( 'cornerstone', 'Unauthorized' );
    }

    if ( ! isset( $params['post_type'] ) ) {
      return new \WP_Error( 'cornerstone', 'post_type not set' );
    }

    $posts = get_posts( apply_filters('cs_dynamic_content_post_picker_args', array(
      'post_type' => $params['post_type'],
      'post_status' => isset( $params['post_status'] ) ? $params['post_status'] : array( 'any', 'tco-data' ),
      'orderby' => 'title',
      'order' => 'ASC',
      'posts_per_page' => apply_filters( 'cs_query_limit', 2500 )
    ) ) );

    $options = array();

    foreach ($posts as $post) {
      $options[] = array(
        'value' => $post->ID,
        'label' => $post->post_title,
      );
    }

    return $options;

  }

  public function get_menus() {

    $locations = get_registered_nav_menus();
    $menus = get_terms('nav_menu');
    $choices = array();

    if ( ! is_wp_error( $menus ) ) {

      foreach ( $menus as $menu ) {
        $choices[] = array(
          'value' => 'menu:' . $menu->term_id,
          'label' => sprintf( csi18n('app.choices.menu-named'), $menu->name )
        );
      }

    }

    foreach ( $locations as $location => $label ) {
      $choices[] = array(
        'value' => 'location:' . $location,
        'label' => sprintf( csi18n('app.choices.menu-location'), $label )
      );
    }

    $samples = array(
      array(
        'value' => 'sample:default',
        'label' => csi18n('app.choices.menu-sample-default'),
      ),
      array(
        'value' => 'sample:default_no_dropdowns',
        'label' => csi18n('app.choices.menu-sample-no-dropdowns')
      ),
      array(
        'value' => 'sample:default_split_1',
        'label' => csi18n('app.choices.menu-sample-split-1')
      ),
      array(
        'value' => 'sample:default_split_2',
        'label' => csi18n('app.choices.menu-sample-split-2')
      ),
    );

    return apply_filters('cornerstone_menu_choices', array_merge( $choices, $samples ) );
  }

  public function get_sidebars() {

    global $wp_registered_sidebars;

    $choices = array();

    foreach ($wp_registered_sidebars as $sidebar) {
      $choices[] = array(
        'label' => $sidebar['name'],
        'value' => $sidebar['id'],
      );
    }

    return $choices;
  }

  public function get_documents_layout_single() {
    return $this->get_documents_choices( 'layout:single');
  }

  public function get_documents_layout_header() {
    return $this->get_documents_choices( 'layout:header');
  }

  public function get_documents_layout_footer() {
    return $this->get_documents_choices( 'layout:footer');
  }

  public function get_documents_choices( $type ) {
    $items = array_map(function($doc) {
      return [
        'value' => $doc['id'],
        'label' => $doc['title'],
      ];
    }, $this->locator->queryPostsForType( $type ) );

    if ( in_array( $type, ['layout:header', 'layout:footer' ], true ) ) {
      array_unshift($items, [
        'value' => 'none',
        'label' => __( 'None', 'cornerstone' )
      ]);
    }

    array_unshift($items, [
      'value' => 'default',
      'label' => __( 'Default', 'cornerstone' )
    ]);

    return $items;

  }

}
