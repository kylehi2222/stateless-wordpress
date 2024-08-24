<?php namespace RealTimeAutoFindReplacePro\functions;

/**
 * Class: Additional Form Fields
 *
 * @package Functions
 * @since 1.2.7
 * @author M.Tuhin <info@codesolz.net>
 */

if ( ! defined( 'CS_BFRP_VERSION' ) ) {
	die();
}

class RenderRTContent{

	/**
	 * Render Real-Time Content
	 *
	 * @return void
	 */
	public static function render_content( $rule, $buffer ){
		// check bypass filter rule
		if ( isset( $rule->type ) && $rule->type == 'plain' ) {
			$buffer = apply_filters( 'bfrp_add_bypass_rule', $rule, $buffer, false );
		}

		$buffer = self::filter_content( $rule, $buffer );

		if ( isset( $rule->type ) && $rule->type == 'plain' ) {
			$buffer = apply_filters( 'bfrp_remove_bypass_rule', $rule, $buffer, false );
		}

		return $buffer;
	}

	/**
	 * Filter content
	 *
	 * @return void
	 */
	public static function filter_content( $rule, $buffer){
		$find = isset( $rule->find ) ? $rule->find : '';
		
		if ( $rule->type == 'regex' ) {
			return \apply_filters( 'bfrp_masking_plain_filter', $rule, $find, $buffer );
		} 
		elseif ( $rule->type == 'advance_regex' ) {
			return \apply_filters( 'bfrp_advance_regex_mask', $find, $rule->replace, $buffer );
		} 
		elseif ( $rule->type == 'htmlTags' ) {
			return $buffer; //only available for pro & above
		} 
		elseif ( $rule->type == 'regexCustom' ) {
			return \apply_filters( 'bfrp_regex_custom_mask', $rule, $buffer );
		} 
		elseif ( $rule->type == 'multiByte' ) {
			return \apply_filters( 'bfrp_multi_byte_mask', $rule, $buffer );
		} 
		else {
			return \apply_filters( 'bfrp_masking_plain_filter', $rule, $find, $buffer );
		}
	}

}