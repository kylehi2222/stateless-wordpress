<?php

class Cornerstone_Dynamic_Content_Query extends Cornerstone_Plugin_Component {

  public function setup() {
    add_filter('cs_dynamic_content_query', array( $this, 'supply_field' ), 10, 3 );
    add_action('cs_dynamic_content_setup', array( $this, 'register' ) );
  }

  public function register() {
    cornerstone_dynamic_content_register_group(array(
      'name'  => 'query',
      'label' => csi18n('app.dc.group-title-query')
    ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'current_page',
      'group' => 'query',
      'label' => csi18n( 'app.dc.query.current-page' )
    ));

    cornerstone_dynamic_content_register_field([
      'name'  => 'current_page_zero',
      'group' => 'query',
      'label' => __('Current Page (Zero Index)', 'cornerstone'),
    ]);

    cornerstone_dynamic_content_register_field([
      'name'  => 'posts_per_page',
      'group' => 'query',
      'label' => __('Default Posts Per Page', 'cornerstone'),
    ]);

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'total_pages',
      'group' => 'query',
      'label' => csi18n( 'app.dc.query.total-pages' )
    ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'found_posts',
      'group' => 'query',
      'label' => csi18n( 'app.dc.query.found-posts' )
    ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'search_query',
      'group' => 'query',
      'label' => csi18n( 'app.dc.query.search-query' )
    ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'query_var',
      'group' => 'query',
      'label' => csi18n( 'app.dc.query.query-var' ),
      'controls' => array( array(
        'key'     => 'key',
        'type'    => 'text',
        'label'   => csi18n('app.dc.key')
      ) ),
      'deep' => true
    ));

  }

  public function supply_field( $result, $field, $args) {

    global $wp_query;

    switch ($field) {
      case 'current_page':
        $result = (get_query_var('paged')) ? get_query_var('paged') : 1;
        break;
      case 'current_page_zero':
        $result = (get_query_var('paged')) ? get_query_var('paged') : 1;
        $result -= 1;
        break;
      case 'total_pages':
        $result = $wp_query->max_num_pages;
        break;
      case 'search_query': 
        $result = esc_html( get_search_query( false ) );
        break;
      case 'found_posts': 
        $result = (int) $wp_query->found_posts;
        break;
      case 'posts_per_page': 
        $result = get_option('posts_per_page');
        break;
      case 'query_var':
        if ( isset( $args['key'] ) ) {
          $result = esc_html( get_query_var( $args['key'] ) );
        }
        break;
    }

    return $result;
  }

}
