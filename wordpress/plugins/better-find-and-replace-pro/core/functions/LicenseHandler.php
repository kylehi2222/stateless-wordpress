<?php namespace RealTimeAutoFindReplacePro\functions;

/**
 * Class: License handler
 *
 * @package Action
 * @since 1.0.0
 * @author M.Tuhin <info@codesolz.net>
 */

if ( ! defined( 'CS_BFRP_VERSION' ) ) {
	die();
}

use RealTimeAutoFindReplace\lib\Util;

class LicenseHandler {

	public static $settings_key = 'csBrarLicenseConfirm';
	public static $license_status_key = 'csBfarLicenseStatus';
	private $api_url            = 'https://codesolz.net/account/api/license-validator-for-better-find-and-replace-pro';

	/**
	 * License validator
	 *
	 * @param [type] $userData
	 * @return void
	 */
	public function bfarValidateLicense( $userData ) {

		$user_data = Util::check_evil_script( $userData );

		// pre_print( $user_data['cs_bfrp_config'] );

		// check is valid api / credentials
		$api_status = Util::remote_call(
			$this->api_url,
			'POST',
			array(
				'body' => $user_data['cs_bfrp_config'],
			)
		);

		if ( isset( $api_status['error'] ) ) {
			return wp_send_json(
				array(
					'status' => false,
					'title'  => __( 'Error', 'better-find-and-replace-pro' ),
					'text'   => $api_status['response'],
				)
			);
		}

		$api_status = json_decode( $api_status );

		// pre_print( $api_status );

		if ( true === $api_status->success ) {
			$save_user_data = \array_merge_recursive( $user_data['cs_bfrp_config'], array( 'id' => $api_status->id ) );
			update_option( self::$settings_key, $save_user_data );
			return wp_send_json(
				array(
					'status' => true,
					'title'  => __( 'Success', 'better-find-and-replace-pro' ),
					'text'   => __( 'Your License has been validated successfully.', 'better-find-and-replace-pro' ),
				)
			);
		} else {
			return wp_send_json(
				array(
					'status' => false,
					'title'  => __( 'Error', 'better-find-and-replace-pro' ),
					'text'   => isset( $api_status->response ) ? $api_status->response : '',
				)
			);
		}

	}


	/**
	 * license key
	 *
	 * @return void
	 */
	public static function getLicenseStatus() {
		return get_option( self::$settings_key );
	}

	/**
	 * License Status
	 *
	 * @return void
	 */
	public static function hasLicenseExpired( $has_expired = 'global-call' ){
		$hasLicenseInstalled = self::getLicenseStatus();

		if( ! empty( $hasLicenseInstalled ) && $has_expired != 'global-call' ) {
			update_option( self::$license_status_key, array(
				"has_expired" => $has_expired == 'yes' ? true : false
			) );

			return true;
		}
		else if( $has_expired == 'global-call' ){
			$get_status = get_option( self::$license_status_key );

			return isset($get_status['has_expired'] ) ? $get_status['has_expired'] : false;
		}
		else{
			$get_status = get_option( self::$license_status_key );
			
			return 'suck';
			return isset($get_status['has_expired'] ) ? 'y' : 'n';
		}
		
	}




}
