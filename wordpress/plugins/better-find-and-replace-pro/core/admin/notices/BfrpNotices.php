<?php namespace RealTimeAutoFindReplacePro\admin\notices;

/**
 * Admin Notice
 *
 * @package Notices
 * @since 1.0.0
 * @author M.Tuhin <tuhin@codesolz.net>
 */

if ( ! defined( 'CS_BFRP_VERSION' ) ) {
	exit;
}

use RealTimeAutoFindReplace\lib\Util;
use RealTimeAutoFindReplace\admin\builders\NoticeBuilder;
use RealTimeAutoFindReplacePro\functions\LicenseHandler;

class BfrpNotices {

	public static function init() {
		$notice = NoticeBuilder::get_instance();
		self::proActivated( $notice );
		self::licenseStatus( $notice );
	}

	/**
	 * Activated Notice
	 *
	 * @return String
	 */
	public static function proActivated( $notice ) {
		$licenseStatus = LicenseHandler::getLicenseStatus();
		if ( $licenseStatus ) {
			return true;
		}
		$message       = __( 'Thank you for choosing pro version. Please enter your license to activate pro version & one click update.', 'better-find-and-replace-pro' );
		$register_link = admin_url( 'admin.php?page=cs-add-replacement-rule' );
		$default_link  = site_url( '' );
		$message       = sprintf(
			$message,
			'<a href="' . $register_link . '"><strong>',
			'</strong></a>',
			'<a target="_blank" href="' . $default_link . '"><strong>',
			'</strong></a>'
		);
		$notice->info( $message, 'LicenseActivated' );
	}


	/**
	 * License expired
	 *
	 * @param [type] $notice
	 * @return void
	 */
	public static function licenseStatus( $notice ) {
		$licenseStatus = LicenseHandler::hasLicenseExpired( 'global-call' );
		if ( false === $licenseStatus ) {
			return true;
		}
		$message    = __( 'Your license has been expired! You\'ll not be able to get the latest version. Latest version 
							comes with regular updates regarding WordPress core and new features. 
							%s Renew your license %s to stay updated & get new features regularly.', 
							'better-find-and-replace-pro' 
						);
		$renew_link  = 'https://codesolz.net/our-products/wordpress-plugin/real-time-auto-find-and-replace';
		$message       = sprintf(
			$message,
			'<a href="' . $renew_link . '" target="_blank"><strong>',
			'</strong></a>'
		);
		$notice->warning( $message, 'LicenseExpired' );
	}

}
