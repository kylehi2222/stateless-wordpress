<?php namespace RealTimeAutoFindReplacePro\Actions;

/**
 * Class: Register Scripts
 *
 * @package Action
 * @since 1.0.0
 * @author M.Tuhin <tuhin@codesolz.net>
 */

if ( ! defined( 'CS_BFRP_VERSION' ) ) {
	die();
}

use RealTimeAutoFindReplacePro\functions\LicenseHandler;

class BFRP_EnqueueScript {


	function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'bfrpAdminScripts' ), 15 );

		//front-end script
		add_action( 'wp_enqueue_scripts', array( $this, 'bfrpFrontEnqueueScripts' ), 90 );
	}

	/**
	 * Enqueue admin scripts
	 *
	 * @return void
	 */
	public function bfrpAdminScripts( $hook = '' ) {
		wp_enqueue_script( 'admin.app.main', CS_BFRP_PLUGIN_ASSET_URI . 'js/appAdminMain.min.js', array(), CS_BFRP_VERSION, true );

		// register custom data
		wp_localize_script(
			'admin.app.main',
			'BFRP',
			array(
				'asset_uri'  => CS_BFRP_PLUGIN_ASSET_URI,
				'ajax_url'   => admin_url( 'admin-ajax.php' ),
				'admin_url'   => admin_url(),
				'cs_token'   => wp_create_nonce( SECURE_AUTH_SALT ),
				'loader_gif' => CS_BFRP_PLUGIN_ASSET_URI . 'img/loader.gif',
				'li' => LicenseHandler::hasLicenseExpired(),
			)
		);

		wp_enqueue_style(
			'BRRPappAdminMain',
			CS_BFRP_PLUGIN_ASSET_URI . 'css/appAdminMain.min.css',
			array(),
			CS_BFRP_VERSION
		);


		// register custom data in head
		wp_enqueue_script( 'admin.app.silence', CS_BFRP_PLUGIN_ASSET_URI . 'js/silence.js', array(), CS_BFRP_VERSION, false );
		wp_localize_script(
			'admin.app.silence',
			'BFRPH',
			array(
				'pgt'         => __( 'Pages', 'real-time-auto-find-and-replace' ),
				'skpgt'         => __( 'Skip Pages', 'real-time-auto-find-and-replace' ),
				'skpgpht'         => __( "Select pages where you don't want to apply this rule. e.g: Checkout, Home", 'real-time-auto-find-and-replace' ),
				'pgpht'         => __( "Select pages where you want to apply this rule. e.g: Checkout, Home", 'real-time-auto-find-and-replace' ),
				'skpt'         => __( 'Skip Posts', 'real-time-auto-find-and-replace' ),
				'pt'         => __( 'Posts', 'real-time-auto-find-and-replace' ),
				'pht'         => __( "Select posts where you don't want to apply this rule. Rule will be applied on single post pages only. e.g: My post", 'real-time-auto-find-and-replace' ),
				'phat'         => __( "Select posts where you want to apply this rule. Rule will be applied on single post pages only. e.g: My post", 'real-time-auto-find-and-replace' ),
				'swal'         => array(
						'rcl' => array(
							'title' => __( 'Are you sure?', 'real-time-auto-find-and-replace' ),
							'text' => __( "You won't be able to revert this!", 'real-time-auto-find-and-replace' ),
							'cbt' => __( 'Yes, delete it!', 'real-time-auto-find-and-replace' ),
						)
				)
			)
		);

	}


	/**
	 * Enqueue app scripts
	 *
	 * @return void
	 */
	public function bfrpFrontEnqueueScripts() {
		wp_enqueue_script( 'bfrp.textReplacer', CS_BFRP_PLUGIN_ASSET_URI . 'js/ajaxContentReplacer.min.js', array(), CS_BFRP_VERSION, true );
	}


}
