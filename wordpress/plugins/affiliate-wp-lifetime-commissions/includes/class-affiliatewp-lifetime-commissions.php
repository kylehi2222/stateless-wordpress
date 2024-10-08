<?php
/**
 * Core: Plugin Bootstrap
 *
 * @package     AffiliateWP Lifetime Commissions
 * @subpackage  Core
 * @copyright   Copyright (c) 2021, Sandhills Development, LLC
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.5
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Main plugin bootstrap class.
 *
 * @since 1.0
 */
final class AffiliateWP_Lifetime_Commissions {

	/**
	 * Main plugin instance.
	 *
	 * @since 1.0
	 * @var   AffiliateWP_Lifetime_Commissions
	 * @static
	 */
	private static $instance;

	/**
	 * Plugin loader file.
	 *
	 * @since 1.5
	 * @var   string
	 */
	private $file = '';

	/**
	 * The version number.
	 *
	 * @since 1.0
	 * @var    string
	 */
	private $version = '1.6.2';

	/**
	 * The integrations handler instance variable.
	 *
	 * @var Affiliate_WP_Lifetime_Commissions_Base
	 * @since 1.0
	 */
	public $integrations;

	/**
	 * The lifetime commissions DB instance variable.
	 *
	 * @var Affiliate_WP_Lifetime_Commissions_DB
	 * @since 1.4.1
	 */
	public $lifetime_customers;

	/**
	 * The lifetime commissions upgrades instance variable.
	 *
	 * @var Affiliate_WP_Lifetime_Commissions_Upgrades
	 * @since 1.4.2
	 */
	public $upgrades;

	/**
	 * Main AffiliateWP_Lifetime_Commissions Instance.
	 *
	 * Insures that only one instance of AffiliateWP_Lifetime_Commissions exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since 1.0
	 * @static
	 *
	 * @param string $file Path to the main plugin file.
	 * @return \AffiliateWP_Lifetime_Commissions The one true bootstrap instance.
	 */
	public static function instance( $file = '' ) {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof AffiliateWP_Lifetime_Commissions ) ) {
			self::$instance = new AffiliateWP_Lifetime_Commissions;
			self::$instance->file = $file;

			self::$instance->setup_constants();
			self::$instance->load_textdomain();
			self::$instance->includes();
			self::$instance->init();
			self::$instance->hooks();

			self::$instance->integrations       = new Affiliate_WP_Lifetime_Commissions_Base;
			self::$instance->lifetime_customers = new Affiliate_WP_Lifetime_Commissions_DB;
		}
		return self::$instance;
	}

	/**
	 * Throw error on object clone.
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since 1.0
	 * @access protected
	 * @return void
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affiliate-wp-lifetime-commissions' ), '1.0' );
	}

	/**
	 * Disable unserializing of the class.
	 *
	 * @since 1.0
	 * @access protected
	 * @return void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affiliate-wp-lifetime-commissions' ), '1.0' );
	}

	/**
	 * Setup plugin constants.
	 *
	 * @access private
	 * @since 1.3
	 * @return void
	 */
	private function setup_constants() {
		// Plugin version
		if ( ! defined( 'AFFWP_LC_VERSION' ) ) {
			define( 'AFFWP_LC_VERSION', $this->version );
		}

		// Plugin Folder Path
		if ( ! defined( 'AFFP_LC_PLUGIN_DIR' ) ) {
			define( 'AFFWP_LC_PLUGIN_DIR', plugin_dir_path( $this->file ) );
		}

		// Plugin Folder URL
		if ( ! defined( 'AFFWP_LC_PLUGIN_URL' ) ) {
			define( 'AFFWP_LC_PLUGIN_URL', plugin_dir_url( $this->file ) );
		}

		// Plugin Root File
		if ( ! defined( 'AFFWP_LC_PLUGIN_FILE' ) ) {
			define( 'AFFWP_LC_PLUGIN_FILE', $this->file );
		}
	}

	/**
	 * Loads the plugin language files.
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function load_textdomain() {

		// Set filter for plugin's languages directory
		$lang_dir = dirname( plugin_basename( $this->file ) ) . '/languages/';
		$lang_dir = apply_filters( 'affwp_lc_languages_directory', $lang_dir );

		// Traditional WordPress plugin locale filter
		$locale   = apply_filters( 'plugin_locale',  get_locale(), 'affiliate-wp-lifetime-commissions' );
		$mofile   = sprintf( '%1$s-%2$s.mo', 'affiliate-wp-lifetime-commissions', $locale );

		// Setup paths to current locale file
		$mofile_local  = $lang_dir . $mofile;
		$mofile_global = WP_LANG_DIR . '/affiliate-wp-lifetime-commissions/' . $mofile;

		if ( file_exists( $mofile_global ) ) {
			// Look in global /wp-content/languages/affiliate-wp-lifetime-commissions/ folder
			load_textdomain( 'affiliate-wp-lifetime-commissions', $mofile_global );
		} elseif ( file_exists( $mofile_local ) ) {
			// Look in local /wp-content/plugins/affiliate-wp-lifetime-commissions/languages/ folder
			load_textdomain( 'affiliate-wp-lifetime-commissions', $mofile_local );
		} else {
			// Load the default language files
			load_plugin_textdomain( 'affiliate-wp-lifetime-commissions', false, $lang_dir );
		}
	}

	/**
	 * Include required files.
	 *
	 * @access private
	 * @since 1.0
	 * @return void
	 */
	private function includes() {

		if ( is_admin() ) {
			require_once AFFWP_LC_PLUGIN_DIR . 'includes/class-admin.php';
			require_once AFFWP_LC_PLUGIN_DIR . 'includes/class-upgrades.php';
		}

		require_once AFFWP_LC_PLUGIN_DIR . 'includes/class-dashboard.php';
		require_once AFFWP_LC_PLUGIN_DIR . 'includes/class-shortcodes.php';
		require_once AFFWP_LC_PLUGIN_DIR . 'includes/class-lifetime-commissions-db.php';
		require_once AFFWP_LC_PLUGIN_DIR . 'integrations/class-base.php';

		// Load the class for each integration enabled.
		foreach ( affiliate_wp()->integrations->get_enabled_integrations() as $filename => $integration ) {

			if ( file_exists( AFFWP_LC_PLUGIN_DIR . 'integrations/class-' . $filename . '.php' ) ) {
				require_once AFFWP_LC_PLUGIN_DIR . 'integrations/class-' . $filename . '.php';
			}

		}

	}

	/**
	 * Init
	 *
	 * @access private
	 * @since 1.0
	 * @return void
	 */
	private function init() {

		if ( is_admin() ) {
			$this->updater();

			$this->upgrades = new Affiliate_WP_Lifetime_Commissions_Upgrades( $this->version );
		}

	}

	/**
	 * Setup the default hooks and actions.
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	private function hooks() {
		// Forces the was_referred() function to return true so our referral can still be created.
		add_filter( 'affwp_was_referred', '__return_true' );

		// Prevent access to the lifetime customers tab.
		add_action( 'template_redirect', array( $this, 'no_access' ) );

		// Add lifetime customers tab.
		add_action( 'affwp_affiliate_dashboard_tabs', array( $this, 'add_lifetime_customers_tab' ), 10, 2 );

		// Add template folder to hold the lifetime customers table.
		add_filter( 'affwp_template_paths', array( $this, 'get_theme_template_paths' ) );

		// Add to the tabs list for 1.8.1 (fails silently if the hook doesn't exist).
		add_filter( 'affwp_affiliate_area_tabs', array( $this, 'register_tab' ), 10, 1 );
	}

	/**
	 * Load the custom plugin updater.
	 *
	 * @access private
	 * @since 1.0
	 * @return void
	 */
	public function updater() {

		if ( class_exists( 'AffWP_AddOn_Updater' ) ) {
			$updater = new AffWP_AddOn_Updater( 6956, $this->file, $this->version );
		}
	}

	/**
	 * Forces the was_referred() function to return true so our referral can still be created.
	 *
	 * @access public
	 * @since  1.0
	 *
	 * @deprecated 1.2.3 Use __return_true() instead.
	 * @see __return_true()
	 */
	public function force_was_referred( $bool ) {
		_deprecated_function( __METHOD__, '1.3', '__return_true' );

		return true;
	}

	/**
	 * Redirect affiliate to main dashboard page if they cannot access lifetime customers tab.
	 *
	 * @since 1.3
	 *
	 * @return void
	 */
	public function no_access() {
		if ( $this->is_lifetime_customers_tab() && ! ( $this->can_access_lifetime_customers() || $this->global_lifetime_customers_access() ) ) {
			wp_redirect( affiliate_wp()->login->get_login_url() ); exit;
		}
	}

	/**
	 * Whether or not we're on the lifetime customers tab of the dashboard.
	 *
	 * @since 1.3
	 *
	 * @return boolean
	 */
	public function is_lifetime_customers_tab() {
		if ( isset( $_GET['tab']) && 'lifetime-customers' == $_GET['tab'] ) {
			return (bool) true;
		}

		return (bool) false;
	}

	/**
	 * Register the "Lifetime Customers" tab.
	 *
	 * @since  1.3
	 * @since  AffiliateWP 2.1.7 The tab being registered requires both a slug and title.
	 *
	 * @return array $tabs The list of tabs
	 */
	public function register_tab( $tabs ) {

		/**
		 * User is on older version of AffiliateWP, use the older method of
		 * registering the tab.
		 *
		 * The previous method was to register the slug, and add the tab
		 * separately, @see add_tab()
		 *
		 * @since 1.3
		 */
		if ( ! $this->has_2_1_7() ) {
			return array_merge( $tabs, array( 'lifetime-customers' ) );
		}

		/**
		 * Don't show tab to affiliate if they don't have access.
		 * Also makes sure tab is properly outputted in Affiliate Area Tabs.
		 *
		 * @since 1.3
		 */
		if ( ! ( $this->can_access_lifetime_customers() || $this->global_lifetime_customers_access() ) ) {
			return $tabs;
		}

		// Register the "Lifetime Customers" tab.
		$tabs['lifetime-customers'] = __( 'Lifetime Customers', 'affiliate-wp-lifetime-commissions' );

		// Return the tabs.
		return $tabs;
	}

	/**
	 * Add Lifetime Customers tab.
	 *
	 * @since 1.3
	 *
	 * @return void
	 */
	public function add_lifetime_customers_tab( $affiliate_id, $active_tab ) {

		// Return early if user has AffiliateWP 2.1.7 or newer. This method is no longer needed.
		if ( $this->has_2_1_7() ) {
			return;
		}

		if ( ! ( $this->can_access_lifetime_customers() || $this->global_lifetime_customers_access() ) ) {
			return;
		}

		?>
		<li class="affwp-affiliate-dashboard-tab<?php echo $active_tab == 'lifetime-customers' ? ' active' : ''; ?>">
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'lifetime-customers' ) ); ?>"><?php _e( 'Lifetime Customers', 'affiliate-wp-lifetime-commissions' ); ?></a>
		</li>
		<?php
	}

	/**
	 * Determine if the user has at least version 2.1.7 of AffiliateWP.
	 *
	 * @since 1.3
	 *
	 * @return boolean True if AffiliateWP v2.1.7 or newer, false otherwise.
	 */
	public function has_2_1_7() {

		$return = true;

		if ( version_compare( AFFILIATEWP_VERSION, '2.1.7', '<' ) ) {
			$return = false;
		}

		return $return;
	}

	/**
	 * Add template folder to hold the lifetime customers table.
	 *
	 * @since 1.3
	 *
	 * @return void
	 */
	public function get_theme_template_paths( $file_paths ) {
		$file_paths[120] = plugin_dir_path( $this->file ) . 'templates';

		return $file_paths;
	}

	/**
	 * Determine if the given affiliate can view their lifetime customers?
	 *
	 * @access public
	 * @since  1.3
	 *
	 * @param int $affiliate_id Optional. Affiliate to check for access to lifetime customers. Default 0 (current affiliate).
	 *
	 * @return bool True if the affiliate can access their lifetime customers, otherwise false.
	 */
	public function can_access_lifetime_customers( $affiliate_id = 0 ) {

		// Use affiliate ID passed in, else get current affiliate ID.
		$affiliate_id = $affiliate_id ? $affiliate_id : affwp_get_affiliate_id();

		if ( ! $affiliate_id ) {
			return false;
		}

		// Look up meta.
		$can_access = affwp_get_affiliate_meta( $affiliate_id, 'affwp_lc_customers_access', true );

		if ( $can_access ) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if affiliates have been globally granted access to view their lifetime customers.
	 *
	 * @access public
	 * @since  1.3
	 *
	 * @return bool True if global access is enabled, otherwise false.
	 */
	public function global_lifetime_customers_access() {
		$global_access = affiliate_wp()->settings->get( 'lifetime_commissions_customers_access', false );

		if ( $global_access ) {
			return true;
		}

		return false;
	}

}

/**
 * The main function responsible for returning the one true AffiliateWP_Lifetime_Commissions
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $affiliatewp_lifetime_commissions = affiliate_wp_lifetime_commissions(); ?>
 *
 * @since 1.0
 *
 * @return AffiliateWP_Lifetime_Commissions|void The one true plugin instance.
 */
function affiliate_wp_lifetime_commissions() {
	return AffiliateWP_Lifetime_Commissions::instance();
}
