<?php

function cs_offload_media_s3_to_local( $content ) {
  return apply_filters( 'as3cf_filter_post_s3_to_local', $content );
}

function cs_offload_media_local_to_s3( $content ) {
  return apply_filters( 'as3cf_filter_post_local_to_s3', $content );
}

add_filter('cs_document_load_content', 'cs_offload_media_local_to_s3' );
add_filter('cs_document_update_content', 'cs_offload_media_s3_to_local' );
add_filter('cs_content_load_serialized_content', 'cs_offload_media_local_to_s3' );
add_filter('cs_content_update_serialized_content', 'cs_offload_media_s3_to_local' );