<?php

// Breadcrumb Label
// =============================================================================

add_filter( 'x_breadcrumb_post_type_archive', function( $item, $post_type_obj, $args ) {

  if ($post_type_obj->name === 'mec-events' ) {

    if ( isset( $args['events_label'] ) ) {
      $item['label'] = $args['events_label'];
    } else {

      $events_options = get_option( 'mec_options' );

      if (isset( $events_options['settings']) && isset($events_options['settings']['archive_title'])) {
        $item['label'] = $events_options['settings']['archive_title'];
      }

    }

  }

  return $item;

}, 10, 3 );