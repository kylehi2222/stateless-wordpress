<?php

add_filter( 'vc_gitem_template_attribute_post_excerpt', function( $value ) {
  cornerstone('FrontEnd')->untrack_excerpt();
  return $value;
}, 0 );


add_filter( 'vc_gitem_template_attribute_post_excerpt', function( $value ) {
  cornerstone('FrontEnd')->track_excerpt();
  return $value;
}, 1000 );
