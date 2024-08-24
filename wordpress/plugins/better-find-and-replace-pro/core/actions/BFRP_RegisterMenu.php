<?php namespace RealTimeAutoFindReplacePro\actions;

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

use RealTimeAutoFindReplacePro\admin\builders\AdminPageBuilder;
use RealTimeAutoFindReplace\admin\options\Scripts_Settings;
use RealTimeAutoFindReplace\lib\Util;
use RealTimeAutoFindReplace\actions\RTAFAR_RegisterMenu;


class BFRP_RegisterMenu {

	/**
	 * Hold pages
	 *
	 * @var type
	 */
	private $pages;

	/**
	 *
	 * @var type
	 */
	private $WcFunc;

	/**
	 *
	 * @var type
	 */
	public $current_screen;

	/**
	 * Hold Menus
	 *
	 * @var [type]
	 */
	public $bfarp_menus;

	public function __construct() {
		 // call WordPress admin menu hook
		add_action( 'admin_menu', array( $this, 'bfrp_register_menu' ), 20 );

		add_filter( 'rtafar_menu_scripts', array( $this, 'bfrp_menu_filter' ), 10 );
	}

	/**
	 * Init current screen
	 *
	 * @return type
	 */
	public function init_current_screen() {
		$this->current_screen = get_current_screen();
		return $this->current_screen;
	}

	/**
	 * Create plugins menu
	 */
	public function bfrp_register_menu() {
		global $rtafr_menu;

		$this->activate_pro_menu(
			array(
				'cs-bfar-go-pro',
				'cs-bfar-restore-database-pro',
			)
		);

		$this->bfarp_menus['restore_in_db'] = add_submenu_page(
			CS_RTAFAR_PLUGIN_IDENTIFIER,
			__( 'Restore Database', 'better-find-and-replace-pro' ),
			__( 'Restore in Database', 'better-find-and-replace-pro' ),
			'read',
			'cs-bfar-restore-database',
			array( $this, 'bfarp_page_restore_db' )
		);

		$this->bfarp_menus['brafp_license'] = add_submenu_page(
			CS_RTAFAR_PLUGIN_IDENTIFIER,
			__( 'License', 'better-find-and-replace-pro' ),
			'License',
			'manage_options',
			'cs-bfar-pro-license',
			array( $this, 'bfarp_page_pro_license' )
		);

		// load script
		$baseMenuIns = RTAFAR_RegisterMenu::get_instance();
		add_action( "load-{$this->bfarp_menus['brafp_license']}", array( $baseMenuIns, 'rtafr_register_admin_settings_scripts' ) );
		add_action( "load-{$this->bfarp_menus['restore_in_db']}", array( $baseMenuIns, 'rtafr_register_admin_settings_scripts' ) );

		// init pages
		$this->pages = new AdminPageBuilder();
		$rtafr_menu  = \array_merge_recursive( $rtafr_menu, $this->bfarp_menus );

		// pre_print( $this->bfarp_menus );
	}


	/**
	 * page pro license
	 *
	 * @return void
	 */
	public function bfarp_page_pro_license() {
		$page_info = array(
			'title'     => __( 'License', 'better-find-and-replace-pro' ),
			'sub_title' => __( 'Validate your pro license key', 'better-find-and-replace-pro' ),
		);

		if ( current_user_can( 'manage_options' ) || current_user_can( 'administrator' ) ) {
			$Default_Settings = $this->pages->ProLicense();
			if ( is_object( $Default_Settings ) ) {
				echo $Default_Settings->generate_default_settings( array_merge_recursive( $page_info, array( 'gateway_settings' => array() ) ) );
			} else {
				echo $Default_Settings;
			}
		} else {
			$AccessDenied = $this->pages->AccessDenied();
			if ( is_object( $AccessDenied ) ) {
				echo $AccessDenied->generate_access_denided( array_merge_recursive( $page_info, array( 'gateway_settings' => array() ) ) );
			} else {
				echo $AccessDenied;
			}
		}
	}

	/**
	 * Restore DB
	 *
	 * @return void
	 */
	public function bfarp_page_restore_db() {
		$page_info = array(
			'title'     => __( 'Restore in Database', 'real-time-auto-find-and-replace' ),
			'sub_title' => __( 'You can restore data to database what you have replaced', 'real-time-auto-find-and-replace' ),
		);

		if ( current_user_can( 'manage_options' ) || current_user_can( 'administrator' ) || current_user_can( RTAFAR_RegisterMenu::$nav_cap['restore_in_db'] ) ) {
			$RestoreDb = $this->pages->RestoreDb();
			if ( is_object( $RestoreDb ) ) {
				echo $RestoreDb->generate_page( array_merge_recursive( $page_info, array( 'default_settings' => array() ) ) );
			} else {
				echo $RestoreDb;
			}
		} else {
			$AccessDenied = $this->pages->AccessDenied();
			if ( is_object( $AccessDenied ) ) {
				echo $AccessDenied->generate_access_denided( array_merge_recursive( $page_info, array( 'default_settings' => array() ) ) );
			} else {
				echo $AccessDenied;
			}
		}
	}

	/**
	 * filter menu
	 *
	 * @param [type] $menu
	 * @return void
	 */
	public function bfrp_menu_filter( $menu ) {
		return array_merge_recursive( (array) $this->bfarp_menus, (array) $menu );
	}

	/**
	 * activate pro menu
	 *
	 * @param [type] $menu
	 * @return void
	 */
	private function activate_pro_menu( $menu ) {
		if ( $menu ) {
			foreach ( $menu as $key => $value ) {
				\remove_submenu_page( CS_RTAFAR_PLUGIN_IDENTIFIER, $value );
			}
		}

		return true;
	}

}

