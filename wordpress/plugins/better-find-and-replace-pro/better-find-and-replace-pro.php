<?php

/**
 * @wordpress-plugin
 * Plugin Name:       Better Find and Replace Pro - Basic
 * Plugin URI:        https://codesolz.net/our-products/wordpress-plugin/real-time-auto-find-and-replace/
 * Description:       A pro addons of - Better Find and Replace. This plugin automatically activate pro features of Better Find and Replace. To make this plugin work keep installed - Better Find and Replace standard free version.
 * Version:           1.3.3
 * Author:            CodeSolz
 * Author URI:        https://www.codesolz.net
 * License:           GPLv3
 * License URI:       https://www.gnu.org/licenses/gpl.txt
 * Domain Path:       /languages
 * Text Domain:       better-find-and-replace-pro
 * Requires PHP: 7.0
 * Requires At Least: 4.0
 * Tested Up To: 6.4
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Better_Find_And_Replace_Pro' ) ) {

	class Better_Find_And_Replace_Pro {

		/**
		 * Hold actions hooks
		 *
		 * @var array
		 */
		private static $bfrp_hooks = array();

		/**
		 * Hold version
		 *
		 * @var String
		 */
		private static $version = '1.3.3';

		/**
		 * Hold version
		 *
		 * @var String
		 */
		private static $db_version = '1.0.8';

		/**
		 * Hold nameSpace
		 *
		 * @var string
		 */
		private static $namespace = 'RealTimeAutoFindReplacePro';

		public function __construct() {

			// load plugins constant
			self::set_constants();

			// load core files
			self::load_core_framework();

			// load init
			self::load_hooks();

			/** Called during the plugin activation */
			self::on_activate();

			/**load textdomain */
			add_action( 'plugins_loaded', array( __CLASS__, 'bfrp_init_textdomain' ), 15 );

			/**Init necessary functions */
			add_action( 'plugins_loaded', array( __CLASS__, 'bfrp_init_function' ), 14 );

			/**Update DB*/
			add_action( 'plugins_loaded', array( __CLASS__, 'bfrp_update_db' ), 17 );

		}

		/**
		 * Set constant data
		 */
		private static function set_constants() {

			$constants = array(
				'CS_BFRP_VERSION'           => self::$version, // Define current version
				'CS_BFRP_DB_VERSION'        => self::$db_version, // Define current db version
				'CS_BFRP_HOOKS_DIR'         => untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/core/actions/', // plugin hooks dir
				'CS_BFRP_BASE_DIR_PATH'     => untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/', // Hold plugins base dir path
				'CS_BFRP_PLUGIN_ASSET_URI'  => plugin_dir_url( __FILE__ ) . 'assets/', // Define asset uri
				'CS_BFRP_PLUGIN_LIB_URI'    => plugin_dir_url( __FILE__ ) . 'lib/', // Library uri
				'CS_BFRP_PLUGIN_IDENTIFIER' => plugin_basename( __FILE__ ), // plugins identifier - base dir
				'CS_BFRP_PLUGIN_NAME'       => 'Better Find And Replace Pro', // Plugin name
				'CS_NOTICE_ID'              => 'bfrp_notice_dismiss', // Plugin Notice id
			);

			foreach ( $constants as $name => $value ) {
				self::set_constant( $name, $value );
			}

			return true;
		}

		/**
		 * Set constant
		 *
		 * @param type $name
		 * @param type $value
		 * @return boolean
		 */
		private static function set_constant( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
			return true;
		}


		/**
		 * load core framework
		 */
		private static function load_core_framework() {
			require_once CS_BFRP_BASE_DIR_PATH . 'vendor/autoload.php';
		}

		/**
		 * Load Action Files
		 *
		 * @return classes
		 */
		private static function load_hooks() {

			if ( false === has_core_bfar() ) {
				return false;
			}

			$namespace = self::$namespace . '\\actions\\';
			foreach ( \glob( CS_BFRP_HOOKS_DIR . '*.php' ) as $cs_action_file ) {
				$class_name = basename( $cs_action_file, '.php' );
				$class      = $namespace . $class_name;
				if ( class_exists( $class ) &&
					! array_key_exists( $class, self::$bfrp_hooks ) ) { // check class doesn't load multiple time
					self::$bfrp_hooks[ $class ] = new $class();
				}
			}
			return self::$bfrp_hooks;
		}


		/**
		 * init textdomain
		 */
		public static function bfrp_init_textdomain() {
			\load_plugin_textdomain( 'better-find-and-replace-pro', false, CS_BFRP_BASE_DIR_PATH . '/languages/' );
		}

		/**
		 * Init plugin's functions
		 *
		 * @return void
		 */
		public static function bfrp_init_function() {
			if ( false === has_core_bfar() ) {
				return false;
			}
			// init notices
			\RealTimeAutoFindReplacePro\admin\notices\BfrpNotices::init();
		}

		/**
		 * init activation hook
		 */
		private static function on_activate() {
			if ( false === has_core_bfar() ) {
				return false;
			}

			// activation hook
			register_activation_hook( __FILE__, array( self::$namespace . '\\install\\Activate', 'on_activate' ) );

			// deactivation hook
			register_deactivation_hook( __FILE__, array( self::$namespace . '\\install\\Activate', 'on_deactivate' ) );

		}

		/**
		 * Update DB
		 *
		 * @return void
		 */
		public static function bfrp_update_db() {
			if ( false === has_core_bfar() ) {
				return false;
			}

			$cls_install = self::$namespace . '\install\Activate';
			$cls_install::bfrp_update_db();
		}


	}

	global $RTAFAF;
	$RTAFAF = new Better_Find_And_Replace_Pro();

}
