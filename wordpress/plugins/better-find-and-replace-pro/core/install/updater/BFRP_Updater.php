<?php

/**
 * Class: Plugin Updater
 *
 * @package Action
 * @since 1.0.0
 * @author M.Tuhin <info@codesolz.net>
 */

if ( ! defined( 'CS_BFRP_VERSION' ) ) {
	die();
}

use RealTimeAutoFindReplace\lib\Util;
use RealTimeAutoFindReplacePro\functions\LicenseHandler;

class BFRP_Updater {

	private $plugin_info = 'https://codesolz.net/account/api/wp-plugin/';

	function __construct() {

		// pre_print( 'test');

		/*** plugins info */
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'brfp_modify_transient' ), 10, 1 );

		/*** plugins info */
		add_filter( 'plugins_api', array( $this, 'bfrp_plugin_popup' ), 10, 3 );

		/*** plugins update */
		add_filter( 'upgrader_post_install', array( $this, 'brfpAfterInstall' ), 10, 3 );

		/*** upgrade info */
		add_action(
			'in_plugin_update_message-' . CS_BFRP_PLUGIN_IDENTIFIER,
			array(
				$this,
				'brfpAddUpgradeMessageLink',
			)
		);

	}

	/**
	 * Check plugin info
	 *
	 * @param [type] $transient
	 * @return void
	 */
	public function brfp_modify_transient( $transient ) {

		if ( \property_exists( $transient, 'checked' ) ) { // Check if transient has a checked property

			if ( $checked = $transient->checked ) { // Did WordPress check for updates?

				$plugin_info = $this->get_plugin_info();

				if ( false === $plugin_info || ( isset( $plugin_info->success ) && false === $plugin_info->success ) ) {
					return $transient; // Return transient
				}

				$out_of_date = isset( $checked[ CS_BFRP_PLUGIN_IDENTIFIER ] ) && \version_compare( $plugin_info->info->version, $checked[ CS_BFRP_PLUGIN_IDENTIFIER ], 'gt' ); // Check if we're out of date

				if ( $out_of_date ) {


					$new_files = ''; // Get the ZIP
					$has_expired = 'yes';
					if( isset( $plugin_info->info->download_link ) && !empty( $new_files = $plugin_info->info->download_link ) ){
						$has_expired = 'no';
					}
					
					$hasValidated = LicenseHandler::hasLicenseExpired( $has_expired );

					$slug = \current( explode( '/', CS_BFRP_PLUGIN_IDENTIFIER ) ); // Create valid slug
					
					$plugin    = array( // setup our plugin info
						'icons'       => array(
							'default' => $plugin_info->info->icons,
						),
						'banners'     => $plugin_info->info->banners->low,
						'url'         => isset( $plugin_info->info->homepage ) ? $plugin_info->info->homepage : '',
						'slug'        => $slug,
						// 'package'     => isset( $plugin_info->info->download_link ) ? $plugin_info->info->download_link : '',
						'package'     => $new_files,
						'new_version' => $plugin_info->info->version,
						'tested'      => $plugin_info->info->tested,
					);
					$transient->response[ CS_BFRP_PLUGIN_IDENTIFIER ] = (object) $plugin; // Return it in response
				}
			}
		}

		return $transient; // Return filtered transient

	}

	/**
	 * Get plugins info
	 *
	 * @return void
	 */
	public function bfrp_plugin_popup( $result, $action, $args ) {

		if ( ! empty( $args->slug ) ) { // If there is a slug

			if ( $args->slug == current( explode( '/', CS_BFRP_PLUGIN_IDENTIFIER ) ) ) { // And it's our slug

				$plugin_info = $this->get_plugin_info();

				if ( $plugin_info ) {

					if ( $plugin_info->success == true ) {

						$plugin = \array_merge_recursive(
							array(
								'name' => CS_BFRP_PLUGIN_NAME,
								'slug' => \current( explode( '/', CS_BFRP_PLUGIN_IDENTIFIER ) ),
							),
							$this->objectToArray( $plugin_info->info )
						);

						return (object) $plugin;

					}
				}
			}
		}

		return $result;

	}


	public function brfpAfterInstall( $response, $hook_extra, $result ) {
		global $wp_filesystem; // Get global FS object

		$install_directory = \rtrim( CS_BFRP_BASE_DIR_PATH, '/' ); // Our plugin directory
		$wp_filesystem->move( $result['destination'], $install_directory ); // Move files to the plugin dir
		$result['destination'] = $install_directory; // Set the destination for the rest of the stack

		if ( is_plugin_active( CS_BFRP_PLUGIN_IDENTIFIER ) ) { // If it was active
			activate_plugin( CS_BFRP_PLUGIN_IDENTIFIER ); // Reactivate
		}

		return $result;
	}


	/**
	 * Show update message
	 *
	 * @return void
	 */
	public function brfpAddUpgradeMessageLink() {

		$hasActivated = $this->hasProActivated();

		if ( $hasActivated ) {
			return;
		}

		echo sprintf(
			__( ' Please %sadd your license key%s for one click update. Generate your license key from our %spremium WordPress License server%2$s. ', 'better-find-and-replace-pro' ),
			'<a href="' . admin_url( 'admin.php?page=cs-bfar-pro-license' ) . '" >',
			'</a>',
			'<a href="https://codesolz.net/account/login" target="_blank" >'
		);
	}


	/**
	 * Get plugin info
	 *
	 * @return void
	 */
	private function get_plugin_info() {

		$plugin_info = Util::remote_call(
			$this->plugin_info,
			'POST',
			array(
				'body' => false !== $this->hasProActivated() ?
						\array_merge_recursive( $this->hasProActivated(), array( 'slug' => \current( explode( '/', CS_BFRP_PLUGIN_IDENTIFIER ) ) ) ) :
						array( 'slug' => \current( explode( '/', CS_BFRP_PLUGIN_IDENTIFIER ) ) ),
			)
		);

		// pre_print( $plugin_info );

		if ( $plugin_info ) {
			return \json_decode( $plugin_info );
		}

		return false;
	}


	/**
	 * Object to array
	 *
	 * @param [type] $obj
	 * @return void
	 */
	private function objectToArray( $obj ) {
		if ( is_object( $obj ) || is_array( $obj ) ) {
			$ret = (array) $obj;
			foreach ( $ret as &$item ) {
				$item = $this->objectToArray( $item );
			}
			return $ret;
		} else {
			return $obj;
		}
	}


	/**
	 * Get pro license key
	 *
	 * @return boolean
	 */
	private function hasProActivated() {
		$hasValidated = LicenseHandler::getLicenseStatus();
		if ( $hasValidated ) {
			return (array) $hasValidated;
		}
		return false;
	}


}


new BFRP_Updater();