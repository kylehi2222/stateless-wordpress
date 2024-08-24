<?php namespace RealTimeAutoFindReplacePro\functions;

/**
 * Class: Register custom menu
 *
 * @package Action
 * @since 1.0.0
 * @author M.Tuhin <info@codesolz.net>
 */

if ( ! defined( 'CS_BFRP_VERSION' ) ) {
	die();
}

use RealTimeAutoFindReplace\lib\RTAFAR_DB;
use RealTimeAutoFindReplacePro\lib\CsUtilPro;
use RealTimeAutoFindReplace\admin\functions\Masking;
use RealTimeAutoFindReplacePro\functions\ActionHandler;

class FilterHandler {

	/**
	 * Get All post Types
	 *
	 * @param [type] $args
	 * @return void
	 */
	public static function getUrlsOptionsFromPostTbl( $args, $type ) {
		global $wpdb;

		// remove all disabled options
		if ( $args ) {
			foreach ( $args as $key => $arg ) {
				if ( false !== \strpos( $key, '_disabled' ) ) {
					unset( $args[ $key ] );
				}
			}
		}

		$taxConfig = array(
			'public'   => true,
			'_builtin' => true,

		);
		$output     = 'objects'; // or objects
		$operator   = 'or'; // 'and' or 'or'
		$taxonomies = \get_taxonomies( $taxConfig, $output, $operator );

		if ( $taxonomies ) {
			foreach ( $taxonomies as $taxonomy ) {
				if ( $type == 'selectOptions' ) {
					$args[ 'tblp_taxonomy_' . $taxonomy->name ] = sprintf( __( 'Taxonomy URLs ( %s )', 'better-find-and-replace-pro' ), $taxonomy->label );
				}
			}
		}

		$getPosts = $wpdb->get_results( "SELECT distinct post_type from `{$wpdb->base_prefix}posts` order by post_type asc " );
		if ( $getPosts ) {
			foreach ( $getPosts as $post ) {
				if ( $type == 'selectOptions' ) {
					if ( isset( $args[ $post->post_type ] ) ) {
						continue;
					}
					$args[ $post->post_type ] = \str_replace( '_', ' ', \ucwords( $post->post_type ) ) . ' URLs';
				}
			}
		}

		return $args;
	}

	/**
	 * Get all pro tabe list
	 *
	 * @param [type] $args
	 * @return void
	 */
	public static function getAllProTblsList( $args ) {
		$tables = RTAFAR_DB::get_sizes();
		return $tables;
	}

	/**
	 * Format find
	 *
	 * @param [type] $settings
	 * @param [type] $find
	 * @return void
	 */
	public static function bfrpFormatFindWholeWord( $settings, $isCaseInsensitive, $find ) {
		$userQuery = isset( $settings['cs_db_string_replace'] ) ? $settings['cs_db_string_replace'] : '';

		$pregCase = '#($0)#';

		// check if whole words has been selected
		if ( isset( $userQuery['whole_word'] ) && $userQuery['whole_word'] == 'on' ) {
			$pregCase = '#\b($0)\b#';
		}

		// if utf8 has checked
		if ( isset( $userQuery['unicode_modifier'] ) && $userQuery['unicode_modifier'] == 'on' ) {
			$pregCase .= 'u';

			if ( is_array( $find ) ) {
				$find = \array_map(
					function( $str ) {
						return \preg_quote( $str );
					},
					$find
				);
			} else {
				$find = \preg_quote( $find );
			}
		}

		if ( true === $isCaseInsensitive ) {
			$pregCase .= 'i';
		}

		$res = \preg_filter( '#^(.*?)$#', $pregCase, $find );

		return $res;
	}

	/**
	 * Activate pro fields
	 *
	 * @param [type] $fields
	 * @return array
	 */
	public static function bfrpActivateProFields( $fields, $settingsConfig ) {
		if ( empty( $fields ) ) {
			return $fields;
		}

		// pre_print( $settingsConfig );
		foreach ( $fields as $key => $field ) {
			if ( 
				( isset( $field['is_pro'] ) &&  ! isset( $field['pro_plan'] ) ) || 
				( isset( $field['pro_plan'] ) && CsUtilPro::has_special_feature( $field['pro_plan'] ) )
			) {
				if ( isset( $field['options'] ) && is_array( $field['options'] ) ) {
					foreach ( $field['options'] as $subArrkey => $option ) {
						if ( isset( $option['custom_attributes']['disabled'] ) ) {
							unset( $option['custom_attributes']['disabled'] );
						}
						$field['options'][ $subArrkey ] = $option;
					}
				}

				if ( isset( $field['is_pro'] ) ) {
					unset( $field['is_pro'] );
				}

				if ( isset( $field['custom_attributes']['disabled'] ) ) {
					unset( $field['custom_attributes']['disabled'] );
				}
				
				$field['title'] = CsUtilPro::filter_pro_version_texting( $field['title']  );
				$fields[ $key ] = $field;
			}
		}

		//NOTE: Custom HOOK: filter fields
		$fields = \apply_filters( 'add_new_rule_additional_fields', $fields, $settingsConfig );

		return $fields;
	}


	/**
	 * Get Page List
	 *
	 * @param [type] $pages
	 * @return void
	 */
	public static function bfrpSkipPages( $default_pages = array(), $option = '' ) {

		if ( isset($option['where_to_replace']) && $option['where_to_replace'] != 'specificPagePost' && empty( $default_pages ) ) {
			return;
		}

		$pages          = \get_pages(
			array(
				'post__in'    => self::bfrpActiveSkipPages( $default_pages, $option ),
				'numberposts' => -1,
			)
		);
		$selected_pages = array();
		if ( $pages ) {
			foreach ( $pages as $page ) {
				$selected_pages += array(
					$page->ID => "(#{$page->ID}) " . $page->post_title,
				);
			}
		}

		return $selected_pages;
	}


	/**
	 * Get active skip page list
	 *
	 * @param [type] $page_ids
	 * @return void
	 */
	public static function bfrpActiveSkipPages( $page_ids, $option = '' ) {
		
		if ( isset($option['where_to_replace']) && $option['where_to_replace'] != 'specificPagePost' && empty( $page_ids ) ) {
			return;
		}

		if( isset($option['where_to_replace']) && $option['where_to_replace'] == 'specificPagePost' ){
			$page_ids = $option['where_to_replace_page_ids'];
		}

		if( empty( $page_ids ) ){
			return;
		}

		return \array_filter( \explode( ',', $page_ids ) );
	}


	/**
	 * Get all pages list - ajax search
	 *
	 * @param [type] $userQuery
	 * @return void
	 */
	public function bfrpGetSkipPagesList( $userQuery ) {

		if ( ! isset( $userQuery['search'] ) || empty( $userQuery['search'] ) ) {
			return false;
		}

		$query = new \WP_Query(
			array(
				'post_type' => 'page',
				's'         => $userQuery['search'],
			)
		);

		$found_pages = array();
		while ( $query->have_posts() ) {
			$query->the_post();
			if ( empty( get_the_title() ) ) {
				continue;
			}
			$found_pages[] = array(
				'id'   => get_the_id(),
				'text' => '(#' . get_the_id() . ') ' . get_the_title(),
			);
		}

		wp_reset_postdata();

		return wp_send_json( $found_pages );
	}

	/**
	 * filter des text
	 *
	 * @param [type] $text
	 * @param [type] $option
	 * @return void
	 */
	public static function bfrpSkipPagesDesTip( $text, $option ){

		if( isset($option['where_to_replace']) && $option['where_to_replace'] == 'specificPagePost' ){
			return __( "Select pages where you want to apply this rule. e.g: Checkout, Home", 'better-find-and-replace-pro');
		}

		return $text;
	}

	/**
	 * filter label text
	 *
	 * @param [type] $text
	 * @param [type] $option
	 * @return void
	 */
	public static function bfrpSkipPagesTitle( $text, $option ){

		if( isset($option['where_to_replace']) && $option['where_to_replace'] == 'specificPagePost' ){
			return __( "Pages", 'better-find-and-replace-pro');
		}

		return $text;
	}


	/**
	 * Get Post List
	 *
	 * @param [type] $posts
	 * @return void
	 */
	public static function bfrpSkipPosts( $default_posts = array(), $option = '' ) {
		// if ( empty( $default_posts ) ) {
		// 	return array();
		// }

		if ( isset($option['where_to_replace']) && $option['where_to_replace'] != 'specificPagePost' && empty( $default_posts ) ) {
			return;
		}

		$posts          = \get_posts(
			array(
				'post_type'   => 'any',
				'post__in'    => self::bfrpActiveSkipPosts( $default_posts, $option ),
				'numberposts' => -1,
			)
		);
		$selected_posts = array();
		if ( $posts ) {
			foreach ( $posts as $post ) {
				$selected_posts += array(
					$post->ID => "(#{$post->ID}) " . $post->post_title,
				);
			}
		}

		return $selected_posts;
	}

	/**
	 * Get active skip post list
	 *
	 * @param [type] $post_ids
	 * @return void
	 */
	public static function bfrpActiveSkipPosts( $post_ids, $option = '' ) {
		// if ( empty( $post_ids ) ) {
		// 	return;
		// }

		if ( isset($option['where_to_replace']) && $option['where_to_replace'] != 'specificPagePost' && empty( $post_ids ) ) {
			return;
		}

		if( isset($option['where_to_replace']) && $option['where_to_replace'] == 'specificPagePost' ){
			$post_ids = $option['where_to_replace_post_ids'];
		}

		if( empty( $post_ids)){
			return;
		}

		return \array_filter( \explode( ',', $post_ids ) );
	}


	/**
	 * Get all posts list - ajax search
	 *
	 * @param [type] $userQuery
	 * @return void
	 */
	public function bfrpGetSkipPostsList( $userQuery ) {

		if ( ! isset( $userQuery['search'] ) || empty( $userQuery['search'] ) ) {
			return false;
		}

		$pages = \wp_list_pluck( \get_pages(), 'ID' );
		$query = new \WP_Query(
			array(
				'post_type'    => 'any',
				's'            => $userQuery['search'],
				'post__not_in' => $pages,
			)
		);

		$found_posts = array();
		while ( $query->have_posts() ) {
			$query->the_post();
			if ( empty( get_the_title() ) ) {
				continue;
			}
			$found_posts[] = array(
				'id'   => get_the_id(),
				'text' => '(#' . get_the_id() . ') ' . get_the_title(),
			);
		}

		wp_reset_postdata();

		return wp_send_json( $found_posts );
	}

	/**
	 * filter des text
	 *
	 * @param [type] $text
	 * @param [type] $option
	 * @return void
	 */
	public static function bfrpSkipPostsDesTip( $text, $option ){

		if( isset($option['where_to_replace']) && $option['where_to_replace'] == 'specificPagePost' ){
			return __( "Select posts where you want to apply this rule. Rule will be applied on single post pages only. e.g: My post", 'better-find-and-replace-pro');
		}

		return $text;
	}

	/**
	 * filter label text
	 *
	 * @param [type] $text
	 * @param [type] $option
	 * @return void
	 */
	public static function bfrpSkipPostsTitle( $text, $option ){

		if( isset($option['where_to_replace']) && $option['where_to_replace'] == 'specificPagePost' ){
			return __( "Posts", 'better-find-and-replace-pro');
		}

		return $text;
	}


	/**
	 * Get fields to show
	 *
	 * @param [type] $global_class
	 * @param [type] $options
	 * @param [type] $ruleType
	 * @return void
	 */
	public static function bfrpAddNewRuleScFields( $global_class, $options, $ruleType ) {
		if ( $ruleType == 'filterShortCodes' || $ruleType == 'ajaxContent') {
			return array(
				'st2'        => 'show',
				'skip_pages' => 'show',
				'skip_posts' => 'show',
			);
		}
	}

	/**
	 * Filter Get Rules SQL
	 *
	 * @param [type] $args
	 * @return void
	 */
	public static function bfrpGetRulesSql( $args ) {
		global $wpdb;

		$page_id = self::bfarp_get_current_page_id();

		//check for country and lang filter has cache SQL, get and return
		$country_rule = '';
		if( \has_filter( 'get_rules_sql') ){
			$cached_sql = \apply_filters( 'get_rules_sql', $page_id );
			if( false !== $cached_sql ){
				if( isset( $cached_sql['cachedSql'] ) ){
					return $cached_sql['cachedSql'];
				}
				else if( isset( $cached_sql['newSql'] ) ){
					$country_rule =  $cached_sql['newSql'];
				}
			}			
		}

		$lang_rule = '';
		if( \has_filter( 'get_rules_lang' ) ){
			$lang_rule = \apply_filters( 'get_rules_lang_sql', $page_id );
		}


		$where_to_replace = isset( $args['where_to_replace'] ) ? $args['where_to_replace'] : '';
		$where_id         = isset( $args['where_id'] ) ? $args['where_id'] : '';
		$ruleType         = isset( $args['ruleType'] ) ? $args['ruleType'] : '';

		$where_to_replace_sql = $wpdb->prepare( 
			" ( where_to_replace = %s"
			. " OR ( where_to_replace = %s AND where_to_replace_page_ids LIKE %s )"
			. "	OR ( where_to_replace = %s AND where_to_replace_post_ids LIKE %s )"
			. " )", 
			'all', 
			'specificPagePost', 
			"bfarPercent," . $page_id . ",bfarPercent",
			'specificPagePost', 
			"bfarPercent," . $page_id . ",bfarPercent"
		);

		$skip_page_find = $wpdb->prepare(
			' and  ( skip_pages NOT LIKE %s or skip_pages = "" or skip_pages IS NULL ) ',
			"bfarPercent," . $page_id . ",bfarPercent"
		);

		$skip_post_find = $wpdb->prepare(
			' and ( skip_posts NOT LIKE %s or skip_posts = "" or skip_posts IS NULL ) ',
			"bfarPercent," . $page_id . ",bfarPercent"
		);
		
		// \pre_print( $where_to_replace_sql );

		$custom_sql = \str_replace(
            'bfarPercent',
            '%',
            "SELECT * from `{$wpdb->prefix}rtafar_rules` as r where {$where_to_replace_sql} {$where_id} {$ruleType} {$skip_page_find} {$skip_post_find} {$country_rule} {$lang_rule} order by id asc",
        );

        //apply only on live mode
		if( \has_filter( 'save_cache_rules_sql' ) ){
			return apply_filters( 'save_cache_rules_sql', $custom_sql, $page_id );
		}

        return $custom_sql;
	}

	/**
	 * Filter new rule before insert
	 *
	 * @return void
	 */
	public static function bfrpFilterNewRule( $user_query ){
		// pre_print($user_query);
		global $wpdb;

		// check skip posts / pages
		$skip_pages = isset( $user_query['skip_pages'] ) ? $user_query['skip_pages'] : [];
		$skip_posts = isset( $user_query['skip_posts'] ) ? $user_query['skip_posts'] : [];

		if( empty($skip_posts) && empty($skip_pages) ){
			return false;
		}

		$isDataExists = $wpdb->get_results(
			$wpdb->prepare(
				"select * from {$wpdb->prefix}rtafar_rules where find = %s and type = %s",
				$user_query['find'],
				$user_query['type'],
			)
		);


		$isNew = true;
		$dataId = '';


		if( $isDataExists ){

			foreach ( $isDataExists as $isExists) {

			if( $isExists->replace == $user_query['replace'] ){
				$dataId = isset($isExists->id) ? $isExists->id : '';
				break;
			}else{
					// if replace is new
					$where_to_replace_page_ids = isset( $isExists->where_to_replace_page_ids ) && ! empty( $isExists->where_to_replace_page_ids ) ?
								$isExists->where_to_replace_page_ids : '';

					$where_to_replace_post_ids = isset( $isExists->where_to_replace_post_ids ) && ! empty( $isExists->where_to_replace_post_ids ) ?
								$isExists->where_to_replace_post_ids : '';

					$where_to_replace_page_ids = \array_filter ( @\explode( ',', $where_to_replace_page_ids) );			
					$where_to_replace_post_ids = \array_filter ( @\explode( ',', $where_to_replace_post_ids) );	

					$post_diff = \array_diff( $skip_posts, $where_to_replace_post_ids );
					$page_diff = \array_diff( $skip_pages, $where_to_replace_page_ids );

					//check post exists
					if( \count($skip_posts) != \count($post_diff) ){
						$dataId = isset($isExists->id) ? $isExists->id : '';
						break;
					}

					if( \count($skip_pages) != \count($page_diff) ){
						$dataId = isset($isExists->id) ? $isExists->id : '';
						break;
					}
				}
			}

			// pre_print($dataId);

			return $dataId;

		}else{
			return false;
		}

	}

	/**
	 * Get current page / post id
	 *
	 * @return void
	 */
	public static function bfarp_get_current_page_id() {
		global $wpdb, $post;

		$page_id = '';
		if ( function_exists( 'is_shop' ) && is_shop() ) {
			$page_id = \wc_get_page_id( 'shop' );
		} else {

			if ( ! is_object( $post ) ) {
				return false;
			}

			$page_id = $post->ID;
		}

		return $page_id;
	}


	/**
	 * Add masking rules for shortcode
	 *
	 * @param [type] $content
	 * @return void
	 */
	public static function bfrpShortcodeReplacer( $content ) {
		$replace_rules = Masking::get_rules( 'all', '', 'filterShortCodes' );

		if ( $replace_rules ) {
			foreach ( $replace_rules as $item ) {
				$content = \str_replace(
					$item->find,
					$item->replace,
					$content
				);
			}
		}

		return $content;
	}

	/**
	 * Filter comments before inserting into database
	 *
	 * @param [type] $comment_data
	 * @return void
	 */
	public static function bfrpFilterComments( $comment_data ) {
		$replace_rules = Masking::get_rules( 'all', '', 'filterComment' );

		if ( $replace_rules ) {
			foreach ( $replace_rules as $item ) {
				$comment_data['comment_content'] = \str_replace(
					$item->find,
					$item->replace,
					$comment_data['comment_content']
				);
			}
		}
		return $comment_data;
	}


	/**
	 * Filter old comments
	 *
	 * @param [type] $comment_text
	 * @return void
	 */
	public static function bfrpFilterOldComments( $comment_text ) {
		$replace_rules = Masking::get_rules( 'all', '', 'filterOldComments' );

		if ( $replace_rules ) {
			foreach ( $replace_rules as $item ) {
				$comment_text = \str_replace(
					$item->find,
					$item->replace,
					$comment_text
				);
			}
		}
		return $comment_text;
	}

	/**
	 * Filter before insert into DB - New / Auto post
	 *
	 * @param [type] $data
	 * @param [type] $postarr
	 * @return void
	 */
	public static function bfrpFilterNewPosts( $data, $postarr ) {
		$replace_rules = Masking::get_rules( 'all', '', 'filterAutoPost' );

		if ( $replace_rules ) {
			foreach ( $replace_rules as $item ) {
				$data['post_title']   = \str_replace( $item->find, $item->replace, $data['post_title'] );
				$data['post_content'] = \str_replace( $item->find, $item->replace, $data['post_content'] );
				$data['post_excerpt'] = \str_replace( $item->find, $item->replace, $data['post_excerpt'] );
			}
		}

		return $data;
	}

	/**
	 * dry run report
	 *
	 * @param [type] $dry_run_report
	 * @return void
	 */
	public static function bfrpFilterDryRunReport( $dry_run_report ){
		if( isset( $dry_run_report['dryRunReport']) ){
			ActionHandler::bfrpSaveDryRunItemTemp( $dry_run_report['dryRunReport'] );
		}

		return $dry_run_report;
	}

}
