<?php
add_filter( 'pre_get_site_by_path', 'dms_get_site_by_host' );
/**
 * Finds blog in which mapped current domain if exist then returns blog
 * otherwise returns null
 *
 * @param $site
 *
 * @return mixed|null
 */
function dms_get_site_by_host( $site ) {
	global $wpdb;
	$current_path = $_SERVER['REQUEST_URI'];
	$is_admin = ( str_contains( $current_path, 'wp-admin' ) || str_contains( $current_path, 'wp-login' ) );

	if ( ! is_multisite() || $is_admin ) {
		return $site;
	}
	$query_string = '';
	$domain       = $_SERVER['HTTP_HOST'];
	foreach ( get_sites() as $blog ) {
		$prefix = $wpdb->get_blog_prefix( $blog->id );
		$result = $wpdb->get_row( "SHOW TABLES LIKE '" . $prefix . "dms_mappings'" );
		if ( ! empty( $result ) ) {
			$query_string .= "SELECT `host`, '" . $blog->id . "' as `blog_id` FROM " . $prefix . "dms_mappings WHERE host='" . $domain . "' UNION ";
		}
	}
	$pos = strrpos( $query_string, 'UNION' );
	if ( $pos !== false ) {
		$query_string = substr_replace( $query_string, '', $pos, strlen( 'UNION' ) );
	}
	$query_string = trim( $query_string );
	$result       = $wpdb->get_row( $query_string );
	if ( ! empty( $result->blog_id ) ) {
		$blog_id = (int) trim( str_replace( 'id', '', $result->blog_id ) );
		$site    = get_site( $blog_id );
	}

	return $site;
}

?>