<?php


add_filter( 'x_breadcrumbs_integrate', function( $crumbs ) {

  if ( ! is_bbpress() ) {
    return $crumbs;
  }

  global $wp;
  $crumbs = [];

  if ( bbp_is_forum_archive() ) {

    $crumbs[] = array(
      'type'  => 'bbp',
      'url'   => home_url( $wp->request . '/' ),
      'label' => bbp_get_forum_archive_title(),
    );

  } else {

    $disabled = has_filter( 'bbp_no_breadcrumb', '__return_true' );

    if ( $disabled ) {
      remove_filter( 'bbp_no_breadcrumb', '__return_true' );
    }

    $bbpress_crumbs = [];

    // Siphon out the bbPress crumbs
    $temp_filter = function ($trail, $crumbs) use (&$bbpress_crumbs) {
      $bbpress_crumbs = $crumbs;
      return $trail;
    };

    add_filter( 'bbp_get_breadcrumb', $temp_filter, 10, 2 );
    bbp_get_breadcrumb();
    remove_filter( 'bbp_get_breadcrumb', $temp_filter, 10, 2);

    if ( $disabled ) {
      add_filter( 'bbp_no_breadcrumb', '__return_true' );
    }

    $final_bbpress_crumb = array_pop( $bbpress_crumbs );

    foreach ( $bbpress_crumbs as $bbpress_crumb ) {

      preg_match( '/<a.+?href="(.+?)".*?class="(.*?)".*?>(.*?)<\/a>/', $bbpress_crumb, $matches );

      $crumbs[] = array(
        'type'  => isset( $matches[2] ) ? $matches[2] : '',
        'url'   => isset( $matches[1] ) ? $matches[1] : '',
        'label' => isset( $matches[3] ) ? $matches[3] : '',
      );

    }

    $crumbs[] = array(
      'type'  => 'bbp-current',
      'url'   => home_url( $wp->request . '/' ),
      'label' => $final_bbpress_crumb,
    );

  }

  return $crumbs;

} );