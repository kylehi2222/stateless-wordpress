<?php namespace RealTimeAutoFindReplacePro\functions;

/**
 * Class: Register custom menu
 *
 * @package Action
 * @since 1.0.5
 * @author M.Tuhin <info@codesolz.net>
 */

if ( ! defined( 'CS_BFRP_VERSION' ) ) {
	die();
}

use RealTimeAutoFindReplace\lib\Util;
use RealTimeAutoFindReplacePro\lib\CsUtilPro;
use RealTimeAutoFindReplacePro\lib\CsMinifier;
use RealTimeAutoFindReplacePro\lib\CsBeautifier;
use RealTimeAutoFindReplace\admin\functions\Masking;
use RealTimeAutoFindReplace\admin\builders\FormBuilder;
use RealTimeAutoFindReplacePro\functions\FilterHandler;


class MaskingRule {

	/**
	 * Masking Rules
	 *
	 * @return void
	 */
	public static function bfrpMaskingRules( $fields ) {
		if ( empty( $fields ) ) {
			return $fields;
		}

		// pre_print( $fields );

		if ( isset( $fields['hasGroup'] ) ) { 
			foreach ( $fields['hasGroup'] as $groupName => $groupOptions ) {
				$fields['hasGroup'][ $groupName ] = self::bfrpMaskingRulesActivate( $groupOptions );
			}

		}else{
			$fields = self::bfrpMaskingRulesActivate( $fields );
		}

		return $fields;
	}

	/**
	 * Activate masking rules
	 *
	 * @param [type] $fields
	 * @return void
	 */
	private static function bfrpMaskingRulesActivate( $fields ){
		foreach ( $fields as $key => $val ) {
			if ( true === CsUtilPro::enable_feature_by_plan( $key ) ) {
				unset( $fields[ $key ] );
				$fields[ CsUtilPro::filter_pro_version_texting( $key ) ] = CsUtilPro::filter_pro_version_texting( $val );
			}
		}

		return $fields;
	}

	/**
	 * Apply advance masking rules
	 *
	 * @param [type] $find
	 * @param [type] $replace
	 * @param [type] $buffer_content
	 * @return void
	 */
	public static function bfrpAddAdvanceRegxMask( $find, $replace, $buffer ) {
		$find    = Util::cs_stripslashes( $find );
		$replace = Util::cs_stripslashes( $replace );

		$minifier = new CsMinifier();
		$buffer   = $minifier->minify( $buffer );
		$find     = $minifier->minify( $find );
		$pattern  = '#(' . preg_quote( $find ) . ')#u';

		$count = 0;
		$html  = \preg_replace( $pattern, $replace, $buffer, -1, $count );

		// beautify html
		$beautify = new CsBeautifier(
			array(
				'indent_inner_html'     => true,
				'indent_char'           => ' ',
				'indent_size'           => 2,
				'wrap_line_length'      => 32786,
				'unformatted'           => array( 'code', 'pre' ),
				'preserve_newlines'     => true,
				'max_preserve_newlines' => 32786,
				'indent_scripts'        => 'normal', // keep|separate|normal
			)
		);

		return $beautify->beautify( $html );
	}




	/**
	 * Save Bypass Rules
	 *
	 * @return void
	 */
	public static function bfrpSaveMaskingRules( $item_id, $user_query ) {
		
		// check skip posts / pages
		$skip_pages = isset( $user_query['skip_pages'] ) ? $user_query['skip_pages'] : '';
		if ( isset( $user_query['skip_pages'] ) && ! empty( $user_query['skip_pages'] ) && \is_array( $user_query['skip_pages'] ) ) {
			$skip_pages = ',' . \implode( ',', $user_query['skip_pages'] ) . ',';
		}

		$skip_posts = isset( $user_query['skip_posts'] ) ? $user_query['skip_posts'] : '';
		if ( isset( $user_query['skip_posts'] ) && ! empty( $user_query['skip_posts'] ) && \is_array( $user_query['skip_posts'] ) ) {
			$skip_posts = ',' . \implode( ',', $user_query['skip_posts'] ) . ',';
		}

		$where_to_replace_page_ids = '';
		$where_to_replace_post_ids = '';
		if( isset( $user_query['where_to_replace'] ) && $user_query['where_to_replace'] == 'specificPagePost' ){

			//is CSV
			if( empty( $skip_pages ) &&  isset( $user_query['where_to_replace_page_ids'] ) && !empty( $user_query['where_to_replace_page_ids'] ) ){
				$skip_pages = $user_query['where_to_replace_page_ids'];
			}
			
			if( empty( $skip_posts ) &&  isset( $user_query['where_to_replace_post_ids'] ) && !empty( $user_query['where_to_replace_post_ids'] ) ){
				$skip_posts = $user_query['where_to_replace_post_ids'];
			}
			
			$where_to_replace_page_ids = $skip_pages;
			$where_to_replace_post_ids = $skip_posts;
			$skip_pages = '';
			$skip_posts = '';
		}


		$update_data = array(
			'bypass_rule_is_active'          => isset( $user_query['bypass_rule_is_active'] ) ? 'on' : 'off',
			'bypass_rule_wrapped_first_char' => isset( $user_query['bypass_rule_wrapped_first_char'] ) ? $user_query['bypass_rule_wrapped_first_char'] : '',
			'bypass_rule_wrapped_last_char'  => isset( $user_query['bypass_rule_wrapped_last_char'] ) ? $user_query['bypass_rule_wrapped_last_char'] : '',
			'remove_bypass_wrapper'          => isset( $user_query['remove_bypass_wrapper'] ) ? 'on' : 'off',
			'case_insensitive'               => isset( $user_query['case_insensitive'] ) ? 'on' : 'off',
			'whole_word'                     => isset( $user_query['whole_word'] ) ? 'on' : 'off',
			'unicode_modifier'               => isset( $user_query['unicode_modifier'] ) ? 'on' : 'off',
			'skip_base_url'                  => isset( $user_query['skip_base_url'] ) ? 'on' : 'off',
			'skip_css_url_external'          => isset( $user_query['skip_css_url_external'] ) ? 'on' : 'off',
			'skip_css_internal'              => isset( $user_query['skip_css_internal'] ) ? 'on' : 'off',
			'skip_css_inline'                => isset( $user_query['skip_css_inline'] ) ? 'on' : 'off',
			'skip_js_url_external'           => isset( $user_query['skip_js_url_external'] ) ? 'on' : 'off',
			'skip_js_internal'               => isset( $user_query['skip_js_internal'] ) ? 'on' : 'off',
			'skip_js_internal'               => isset( $user_query['skip_js_internal'] ) ? 'on' : 'off',
			'skip_pages'                     => $skip_pages,
			'skip_posts'                     => $skip_posts,
			'where_to_replace_page_ids'      => $where_to_replace_page_ids,
			'where_to_replace_post_ids'      => $where_to_replace_post_ids,
		);

		// pre_print( $update_data );

		global $wpdb;
		$wpdb->update( "{$wpdb->prefix}rtafar_rules", $update_data, array( 'id' => $item_id ) );

		return true;
	}

	/**
	 * Add Bypass Rules
	 *
	 * @return void
	 */
	public static function applyBypassRule( $ruleDef, $bufferedContent, $findWord = false ) {
		$find = false !== $findWord ? $findWord : $ruleDef->find;

		// add bypass rule
		if ( isset( $ruleDef->bypass_rule_is_active ) && $ruleDef->bypass_rule_is_active == 'on' ) {
			$formatFind      = $ruleDef->bypass_rule_wrapped_first_char . $find . $ruleDef->bypass_rule_wrapped_last_char;
			$bufferedContent = \str_replace( $formatFind, '~bfrpBypassText~', $bufferedContent );
		}

		// apply skip url rule + bypass filter rules
		if ( ( isset( $ruleDef->skip_base_url ) && $ruleDef->skip_base_url == 'on' ) ||
			( isset( $ruleDef->skip_css_url_external ) && $ruleDef->skip_css_url_external == 'on' ) ||
			( isset( $ruleDef->skip_js_url_external ) && $ruleDef->skip_js_url_external == 'on' )
		) {
			$site_domain = \str_replace( array( 'http://', 'https://', 'www', '.com' ), '', \site_url() );
			\preg_match_all( '#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $bufferedContent, $match );
			if ( isset( $match[0] ) && ! empty( $match[0] ) ) {

				foreach ( $match[0] as $url ) {

					if ( isset( $ruleDef->skip_css_url_external ) && $ruleDef->skip_css_url_external == 'on' &&
						false !== \strpos( $url, '.css' )
					) {
						$wrap_css_url    = \str_replace( $find, '~bfrpWrapCssUrl~', $url );
						$bufferedContent = \str_replace( $url, $wrap_css_url, $bufferedContent );
					}

					if ( isset( $ruleDef->skip_js_url_external ) && $ruleDef->skip_js_url_external == 'on' &&
						false !== \strpos( $url, '.js' )
					) {
						$wrap_js_url     = \str_replace( $find, '~bfrpWrapJsUrl~', $url );
						$bufferedContent = \str_replace( $url, $wrap_js_url, $bufferedContent );
					}

					if ( isset( $ruleDef->skip_base_url ) && $ruleDef->skip_base_url == 'on' &&
						false !== \strpos( $url, $site_domain ) && false !== \strpos( $url, $find )
					) {
						$wrap_url        = \str_replace( $find, '~bfrpSkipUrl~', $url );
						$bufferedContent = \str_replace( $url, $wrap_url, $bufferedContent );
					}
				}
			}
		}

		if ( isset( $ruleDef->skip_css_internal ) && $ruleDef->skip_css_internal == 'on' ) {
			\preg_match_all( '%<(style)(?=[^<>]*?(?:type="(text/css)"|>))(?:.*?</\1>|[^<>]*>)%si', $bufferedContent, $internalCss );
			if ( isset( $internalCss[0] ) && ! empty( $internalCss[0] ) ) {
				foreach ( $internalCss[0] as $iCss ) {
					$wrap_icss       = \str_replace( $find, '~bfrpWrapIcss~', $iCss );
					$bufferedContent = \str_replace( $iCss, $wrap_icss, $bufferedContent );
				}
			}
		}

		if ( isset( $ruleDef->skip_css_inline ) && $ruleDef->skip_css_inline == 'on' ) {
			\preg_match_all( '/style="(.*?)"/is', $bufferedContent, $inlineCss );
			if ( isset( $inlineCss[0] ) && ! empty( $inlineCss[0] ) ) {
				foreach ( $inlineCss[0] as $inlJs ) {
					$wrap_inlJs      = \str_replace( $find, '~bfrpWrapInlCss~', $inlJs );
					$bufferedContent = \str_replace( $inlJs, $wrap_inlJs, $bufferedContent );
				}
			}
		}

		if ( isset( $ruleDef->skip_js_internal ) && $ruleDef->skip_js_internal == 'on' ) {
			\preg_match_all( '%<(script)(?=[^<>]*?(?:type="(text/javascript)"|>))(?:.*?</\1>|[^<>]*>)%si', $bufferedContent, $internalJs );

			if ( isset( $internalJs[0] ) && ! empty( $internalJs[0] ) ) {
				foreach ( $internalJs[0] as $iJs ) {
					$wrap_iJs        = \str_replace( $find, '~bfrpWrapIJs~', $iJs );
					$bufferedContent = \str_replace( $iJs, $wrap_iJs, $bufferedContent );
				}
			}
		}

		return $bufferedContent;
	}

	/**
	 * Remove bypass rule
	 *
	 * @param [type]  $ruleDef
	 * @param [type]  $bufferedContent
	 * @param boolean $findWord
	 * @return void
	 */
	public static function removeBypassRule( $ruleDef, $bufferedContent, $findWord = false ) {
		$find = false !== $findWord ? $findWord : $ruleDef->find;

		// remove bypass rule
		if ( isset( $ruleDef->bypass_rule_is_active ) && $ruleDef->bypass_rule_is_active == 'on' ) {
			if ( isset( $ruleDef->remove_bypass_wrapper ) && $ruleDef->remove_bypass_wrapper == 'on' ) {
				$formatFind = $find;
			} else {
				$formatFind = $ruleDef->bypass_rule_wrapped_first_char . $find . $ruleDef->bypass_rule_wrapped_last_char;
			}
			$bufferedContent = \str_replace( '~bfrpBypassText~', $formatFind, $bufferedContent );
		}

		// remove skip rule
		$bufferedContent = \str_replace(
			array(
				'~bfrpSkipUrl~',
				'~bfrpWrapCssUrl~',
				'~bfrpWrapJsUrl~',
				'~bfrpWrapIcss~',
				'~bfrpWrapIJs~',
				'~bfrpWrapInlCss~',
			),
			$find,
			$bufferedContent
		);

		return $bufferedContent;
	}


	/**
	 * Masking plain text filter
	 *
	 * @param [type] $item
	 * @param [type] $find
	 * @param [type] $buffer
	 * @return void
	 */
	public static function bfrpMaskingPlainFilter( $item, $find, $buffer ) {
		// return for whole word search and case insensitive or regex rule
		if ( ( isset( $item->whole_word ) && $item->whole_word == 'on' ) ||
				( isset( $item->whole_word ) && $item->whole_word == 'on' ) ||
				( isset( $item->type ) && $item->type == 'regex' ) // rule type general regex
			) {
			return self::pregMatchFilter( $item, $find, $buffer );
		}

		// return if filter is not for whole word
		if ( isset( $item->case_insensitive ) && $item->case_insensitive == 'on' ) {
			return \str_ireplace( $find, Util::cs_stripslashes( $item->replace ), $buffer );
		} else {
			return \str_replace( $find, Util::cs_stripslashes( $item->replace ), $buffer );
		}
	}

	/**
	 * Preg match filter
	 *
	 * @param [type] $item
	 * @param [type] $find
	 * @param [type] $buffer
	 * @return void
	 */
	private static function pregMatchFilter( $item, $find, $buffer ) {
		$findFormat = FilterHandler::bfrpFormatFindWholeWord(
			array(
				'cs_db_string_replace' => (array) $item,
			),
			isset( $item->case_insensitive ) && $item->case_insensitive == 'on' ? true : false,
			\preg_quote( Util::cs_stripslashes( $item->find ) )
		);

		return \preg_replace( $findFormat, Util::cs_stripslashes( $item->replace ), $buffer );
	}

	/**
	 * Add New Rule Page Footer Hooks
	 *
	 * @return void
	 */
	public static function bfrpFooterAddNewRuleMasking() {
		?>
			<script type="text/javascript">
				jQuery(document).ready(function($){
					jQuery("body").on('change', '.rule-type', function(){
						jQuery(".bypass-rule, .advance-filter").addClass('force-hidden');
						if( jQuery(this).val() === 'plain' ){
							jQuery(".bypass-rule, .advance-filter").removeClass('force-hidden');
						}
						if( jQuery(this).val() === 'regex' ){
							jQuery(".advance-filter").removeClass('force-hidden');
						}

						if( jQuery(this).val() === 'filterShortCodes' ){
							jQuery(".st2-wrapper, .wrap-skip-pages, .wrap-skip-posts").removeClass('force-hidden');
							// console.log('hi');
						}

						if( jQuery(this).val() === 'ajaxContent' ){ 
							jQuery(".st2-wrapper, .wrap-skip-pages, .wrap-skip-posts").removeClass('force-hidden');
						}
					});

					jQuery("body").on('change', '.where-to-replace-select', function(){
						var wtrs_val = jQuery(this).val();
						if( wtrs_val == 'all' ){
							jQuery(".st2-wrapper, .wrap-skip-pages, .wrap-skip-posts").removeClass('force-hidden');

							jQuery(".wrap-skip-pages > .label > label").text( BFRPH.skpgt );
							jQuery(".wrap-skip-pages > .input-group > .description").text( BFRPH.skpgpht );

							jQuery(".wrap-skip-posts > .label > label").text( BFRPH.skpt );
							jQuery(".wrap-skip-posts > .input-group > .description").text( BFRPH.pht );

						}
						else if( wtrs_val == 'specificPagePost' ){
							jQuery(".st2-wrapper, .wrap-skip-pages, .wrap-skip-posts").removeClass('force-hidden');

							jQuery(".wrap-skip-pages > .label > label").text(BFRPH.pgt);
							jQuery(".wrap-skip-pages > .input-group > .description").text( BFRPH.pgpht );

							jQuery(".wrap-skip-posts > .label > label").text(BFRPH.pt);
							jQuery(".wrap-skip-posts > .input-group > .description").text( BFRPH.phat );
						}
						
					});



					$('body').on("select2:select", '.skip-pages', function (e) { 
						var data = e.params.data.text;
						if(data=='Select All'){
							$(".skip-pages > option:enabled").prop("selected","selected");
							$(".skip-pages > option:contains(Unselect All)").prop('selected', false);
							$(".skip-pages").trigger("change");
						}
						else if( data=='Unselect All'){
							$(".skip-pages > option:enabled").prop("selected","");
							$(".skip-pages").trigger("change");
						}
					});

					$('body').on("select2:select", '.skip-posts', function (e) { 
						var data = e.params.data.text;
						if(data=='Select All'){
							$(".skip-posts > option:enabled").prop("selected","selected");
							$(".skip-posts > option:contains(Unselect All)").prop('selected', false);
							$(".skip-posts").trigger("change");
						}
						else if( data=='Unselect All'){
							$(".skip-posts > option:enabled").prop("selected","");
							$(".skip-posts").trigger("change");
						}
					});

					$('.skip-posts').select2({
						placeholder: "Enter post title.. At least one character",
						ajax: {
							url: ajaxurl,
							data: function ( userQuery ) {
								var query = {
									search: userQuery.term,
									type: 'public',
									action : 'bfrp_ajax',
									cs_token : $("#cs_token").val(),
									data : {
										method : 'functions\\FilterHandler@bfrpGetSkipPostsList',
										search: userQuery.term
									}
								}
								return query;
							},
							processResults: function (data) {
								return {
									results: data
								};
							}
						}
					});

					$('.skip-pages').select2({
						placeholder: "Enter page title.. At least one character",
						ajax: {
							url: ajaxurl,
							data: function ( userQuery ) {
								var query = {
									search: userQuery.term,
									type: 'public',
									action : 'bfrp_ajax',
									cs_token : $("#cs_token").val(),
									data : {
										method : 'functions\\FilterHandler@bfrpGetSkipPagesList',
										search: userQuery.term
									}
								}
								return query;
							},
							processResults: function (data) {
								return {
									results: data
								};
							}
						}
					});

					

				});
			</script>
		<?php
	}


	/**
	 * All masking rules tbl rows
	 *
	 * @param [type] $args
	 * @return void
	 */
	public static function bfrpAllMaskingRulesTblRows( $args ) {
		return \apply_filters( 'rules_table_header', \array_merge_recursive(
			$args,
			array(
				'skip_pages' => __( 'Skip pages', 'better-find-and-replace-pro' ),
				'skip_posts' => __( 'Skip posts', 'better-find-and-replace-pro' ),
			)
		) );
	}


	/**
	 * Show column data on data table
	 *
	 * @param [type] $args
	 * @return void
	 */
	public static function bfrpTblColumnSkipPages( $args ) {
		// pre_print( $args );

		if ( ! isset( $args->skip_pages ) || empty( $args->skip_pages ) ) {
			return '---';
		}

		$pageids = \array_filter( \explode( ',', $args->skip_pages ) );

		if ( empty( $pageids ) ) {
			return '---';
		}

		$pages = \get_pages(
			array(
				'numberposts' => 1,
				'include'     => $pageids,
			)
		);

		if ( $pages ) {
			$p = array();
			foreach ( $pages as $page ) {
				if ( empty( $page->post_title ) ) {
					$p[] = '(#' . $page->ID . ') ---';
					continue;
				}
				$pageUrl = \get_the_permalink( $page->ID );
				$title   = \strlen( $page->post_title ) > 15 ? \substr( $page->post_title, 0, 15 ) . '..' : $page->post_title;
				$p[]     = '<a class="list-skip-pages" title="' . $page->post_title . '"  href="' . $pageUrl . '" target="_blank">(#' . $page->ID . ') ' . $title . '</a>';
			}
			return implode( ', ', $p );
		}

		return '---';

	}

	/**
	 * Show column data on data table
	 *
	 * @param [type] $args
	 * @return void
	 */
	public static function bfrpTblColumnSkipPosts( $args ) {
		// pre_print( $args );

		if ( ! isset( $args->skip_posts ) || empty( $args->skip_posts ) ) {
			return '---';
		}

		$postids = \array_filter( \explode( ',', $args->skip_posts ) );

		if ( empty( $postids ) ) {
			return '---';
		}

		$posts = \get_posts(
			array(
				'post_type'   => 'any',
				'numberposts' => -1,
				'include'     => $postids,
			)
		);

		if ( $posts ) {
			$p = array();
			foreach ( $posts as $post ) {
				if ( empty( $post->post_title ) ) {
					$p[] = '(#' . $post->ID . ') ---';
					continue;
				}
				$postUrl = \get_the_permalink( $post->ID );
				$title   = \strlen( $post->post_title ) > 15 ? \substr( $post->post_title, 0, 15 ) . '..' : $post->post_title;
				$p[]     = '<a class="list-skip-posts" title="' . $post->post_title . '" href="' . $postUrl . '" target="_blank">(#' . $post->ID . ') ' . $title . '</a>';
			}
			return \implode( ', ', $p );
		}

		return '---';

	}




	/**
	 * Data tables column text
	 *
	 * @param [type] $item
	 * @return void
	 */
	public static function bfrpColumnTypeText( $item ) {
		if ( isset( $item->type ) && $item->type == 'filterShortCodes' ) {
			return sprintf(
				__( 'Shortcode %1$s (replace before rendering on Browser) %2$s ', 'real-time-auto-find-and-replace' ),
				'<span class="dt-col-sm-des">',
				'</span>'
			);
		} elseif ( isset( $item->type ) && $item->type == 'filterAutoPost' ) {
			return sprintf(
				__( 'Auto / New Post %1$s (replace before inserting into Database) %2$s ', 'real-time-auto-find-and-replace' ),
				'<span class="dt-col-sm-des">',
				'</span>'
			);
		} elseif ( isset( $item->type ) && $item->type == 'filterComment' ) {
			return sprintf(
				__( 'New Comment %1$s (replace before inserting into Database) %2$s ', 'real-time-auto-find-and-replace' ),
				'<span class="dt-col-sm-des">',
				'</span>'
			);
		} elseif ( isset( $item->type ) && $item->type == 'filterOldComments' ) {
			return sprintf(
				__( 'Old Comments %1$s (replace before rendering on Browser) %2$s ', 'real-time-auto-find-and-replace' ),
				'<span class="dt-col-sm-des">',
				'</span>'
			);
		}
	}


	/**
	 * Data tables column text
	 *
	 * @param [object] $item
	 * @return void
	 */
	public static function bfrpColumnWhereToReplace( $item ) {

		$post_ids = '';
		if( isset( $item->where_to_replace_page_ids ) || isset($item->where_to_replace_post_ids) ){
			$post_ids = \str_replace( ',,', ',', $item->where_to_replace_page_ids . $item->where_to_replace_post_ids );
		}

		$posts = '---';
		if( !empty($post_ids)){
			$posts = self::bfrpTblColumnSkipPosts( (object)array(
				'skip_posts' => $post_ids
			));
		}


		if ( isset( $item->where_to_replace ) && $item->where_to_replace == 'specificPagePost' ) {
			return sprintf(
				__( 'On specific page or posts %1$s %2$s %3$s ', 'real-time-auto-find-and-replace' ),
				'<br><span class="dt-col-sm-des">',
				$posts,
				'</span>'
			);
		} 
	}

	/**
	 * Regex Custom Masking
	 *
	 * @param [type] $rule
	 * @param [type] $html
	 * @return void
	 */
	public static function bfrpRegexCustomMask( $rule, $html ){
		return \preg_replace( $rule->find, $rule->replace, $html );
	}

	/**
	 * Multi byte masking
	 *
	 * @param [type] $rule
	 * @param [type] $html
	 * @return void
	 */
	public static function bfrpMultiByteMask( $rule, $html ){
		\mb_regex_encoding( $rule->html_charset );
		return \mb_ereg_replace( $rule->find, Util::cs_stripslashes( $rule->replace ), $html );
	}


}
