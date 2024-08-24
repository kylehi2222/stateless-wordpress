<?php namespace RealTimeAutoFindReplacePro\admin\options\pages;

/**
 * Class: Add New Coin
 *
 * @package Admin
 * @since 1.0.0
 * @author CodeSolz <customer-support@codesolz.net>
 */

if ( ! defined( 'CS_BFRP_VERSION' ) ) {
	die();
}

use RealTimeAutoFindReplace\lib\Util;
use RealTimeAutoFindReplace\admin\builders\FormBuilder;
use RealTimeAutoFindReplacePro\admin\builders\AdminPageBuilder;
use RealTimeAutoFindReplacePro\functions\LicenseHandler;

class ProLicense {

	/**
	 * Hold page generator class
	 *
	 * @var type
	 */
	private $Admin_Page_Generator;

	/**
	 * Form Generator
	 *
	 * @var type
	 */
	private $Form_Generator;


	public function __construct( AdminPageBuilder $AdminPageGenerator ) {
		$this->Admin_Page_Generator = $AdminPageGenerator;

		/*create obj form generator*/
		$this->Form_Generator = new FormBuilder();

	}

	/**
	 * Generate add new coin page
	 *
	 * @param type $args
	 * @return type
	 */
	public function generate_default_settings( $args ) {

		$settings_data = LicenseHandler::getLicenseStatus();

		$fields = array(
			'cs_bfrp_config[cms_username]' => array(
				'title'       => __( 'Email', 'better-find-and-replace-pro' ),
				'type'        => 'email',
				'class'       => 'form-control',
				'required'    => true,
				'value'       => FormBuilder::get_value( 'cms_username', $settings_data, '' ),
				'placeholder' => __( 'Enter your email address', 'better-find-and-replace-pro' ),
				'desc_tip'    => sprintf( __( 'Please enter the email that you used to make the purchase and currently use to log in here %s ', 'better-find-and-replace-pro' ), '<code>codesolz.net/account/login</code>' ),
			),
			'cs_bfrp_config[cms_pass]'     => array(
				'title'       => __( 'Password', 'better-find-and-replace-pro' ),
				'type'        => 'password',
				'class'       => 'form-control',
				'required'    => true,
				'value'       => FormBuilder::get_value( 'cms_pass', $settings_data, '' ),
				'placeholder' => __( 'Enter your password', 'better-find-and-replace-pro' ),
				'desc_tip'    => sprintf( __( 'Please enter the password that you use to log in here %s ', 'better-find-and-replace-pro' ), '<code>codesolz.net/account/login</code>' ),
			),
			'cs_bfrp_config[api_key]'      => array(
				'title'       => __( 'License Key', 'better-find-and-replace-pro' ),
				'type'        => 'text',
				'class'       => 'form-control',
				'required'    => true,
				'value'       => FormBuilder::get_value( 'api_key', $settings_data, '' ),
				'placeholder' => __( 'Enter your license key', 'better-find-and-replace-pro' ),
				'desc_tip'    => sprintf( __( 'Enter your license key. You can find your license key in the "My license Keys" menu in %1$s myportal area %2$s .', 'better-find-and-replace-pro' ), "<a href='https://codesolz.net/account/login/' target='_blank'>", '</a>' ),
			),

		);

		$args['content'] = $this->Form_Generator->generate_html_fields( $fields );

		$hidden_fields = array(
			'action'                          => array(
				'id'    => 'action',
				'type'  => 'hidden',
				'value' => 'bfrp_ajax',
			),
			'method'                          => array(
				'id'    => 'method',
				'type'  => 'hidden',
				'value' => 'functions\\LicenseHandler@bfarValidateLicense',
			),
			'swal_title'                      => array(
				'id'    => 'swal_title',
				'type'  => 'hidden',
				'value' => 'Validating your license',
			),
			'swal_des'                        => array(
				'id'    => 'swal_des',
				'type'  => 'hidden',
				'value' => __( 'Please wait a while...', 'better-find-and-replace-pro' ),
			),
			'swal_loading_gif'                => array(
				'id'    => 'swal_loading_gif',
				'type'  => 'hidden',
				'value' => CS_RTAFAR_PLUGIN_ASSET_URI . 'img/loading-timer.gif',
			),
			'swal_error'                      => array(
				'id'    => 'swal_error',
				'type'  => 'hidden',
				'value' => __( 'Something went wrong! Please try again by refreshing the page.', 'better-find-and-replace-pro' ),
			),
			'cs_bfrp_config[cms_refferer]'    => array(
				'id'    => 'cs_bfrp_config[cms_refferer]',
				'type'  => 'hidden',
				'value' => site_url(),
			),
			'cs_bfrp_config[cms_refferer_id]' => array(
				'id'    => 'cs_bfrp_config[cms_refferer_id]',
				'type'  => 'hidden',
				'value' => 1,
			),

		);

		$args['hidden_fields'] = $this->Form_Generator->generate_hidden_fields( $hidden_fields );

		$args['btn_text']   = 'Validate License';
		$args['show_btn']   = true;
		$args['body_class'] = 'no-bottom-margin';

		$args['well'] = "<ul>
            <li> <b>Basic Hints</b>
                <ol>
                    <li>
                        Please go to here - <a href='https://codesolz.net/account/login' target=\"_blank\">https://codesolz.net/account/login</a> and generate your license key.
                    </li>
                </ol>
            </li>
        </ul>";

		return $this->Admin_Page_Generator->generate_page( $args );
	}

}
