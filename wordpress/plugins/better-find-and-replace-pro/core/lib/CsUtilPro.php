<?php namespace RealTimeAutoFindReplacePro\lib;

/**
 * Library Class
 *
 * @package Library
 * @since 1.2.7
 * @author Tuhin <info@codesolz.com>
 */

if ( ! defined( 'CS_BFRP_VERSION' ) ) {
	exit;
}

class CsUtilPro{

	/**
	 * Check for special features
	 *
	 * @param [type] $keyWord
	 * @return boolean
	 */
	public static function has_special_feature( $keyWord ){
		if( empty( $keyWord )) return false;

		if ( \preg_match( "/[1]/", $keyWord, $matches)) { //check special features
			return true;
		} 

		return false;
	}

	/**
	 * Feature Checking - Special
	 *
	 * @return void
	 */
	public static function enable_feature_by_plan( $keyWord ){
		if( empty( $keyWord ) ) return false;
		if ( false !== \strpos( $keyWord, '_disabled' ) && \preg_match( "/^[^\d]+$/", $keyWord, $matches) ) {
			return true;
		} 
		else if ( self::has_special_feature( $keyWord ) ) { //check special features
			return true;
		} 

		return false;
	}

	/**
	 * Filter Pro texting
	 *
	 * @param [type] $string
	 * @return void
	 */
	public static function filter_pro_version_texting( $string ){
		$string = \str_replace( 
			array( 
				' - pro version only (pro PRO + Extend)', 
				' - pro version only', 
				' - pro extend version only',
				'_disabled',
				'<br/><span class="pro-version-only"> Pro version only </span>', 
				'<br/><span class="pro-version-only"> Pro Extend - version only </span>', 
			), 
			'', 
			$string );

		return self::filter_pro_version_custom_text( $string );
	}

	/**
	 * Filter pro version custom text
	 *
	 * @return void
	 */
	public static function filter_pro_version_custom_text( $string ){
		return \str_replace( 
			array( 
				'ajaxContentAdvanced'
			), 
			array(
				'ajaxContent'
			), 
			$string );
	}

}
