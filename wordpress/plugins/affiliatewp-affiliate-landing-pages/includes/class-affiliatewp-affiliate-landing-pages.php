<?php
/**
 * Core: Plugin Bootstrap
 *
 * @package     AffiliateWP Affiliate Landing Pages
 * @subpackage  Core
 * @copyright   Copyright (c) 2021, Sandhills Development, LLC
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.1
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'AffiliateWP_Affiliate_Landing_Pages' ) ) {

	/**
	 * Main plugin bootstrap.
	 *
	 * @since 1.0.0
	 */
	final class AffiliateWP_Affiliate_Landing_Pages {

		/**
		 * Holds the instance.
		 *
		 * Ensures that only one instance of AffiliateWP_Affiliate_Landing_Pages exists in memory at any one
		 * time and it also prevents needing to define globals all over the place.
		 *
		 * TL;DR This is a static property property that holds the singleton instance.
		 *
		 * @access private
		 * @var    \AffiliateWP_Affiliate_Landing_Pages
		 * @static
		 *
		 * @since 1.0
		 */
		private static $instance;

		/**
		 * The version number
		 *
		 * @since 1.0
		 * @var   string
		 */
		private $version = '';

		/**
		 * The affiliate landing pages upgrades instance variable.
		 *
		 * @since 1.0.3
		 * @var Affiliate_WP_Affiliate_Landing_Pages_Upgrades
		 */
		public $upgrades;

		/**
		 * Main plugin file.
		 *
		 * @since 1.0.0
		 * @var   string
		 */
		private $file = '';

		/**
		 * Generates the main AffiliateWP_Affiliate_Landing_Pages instance.
		 *
		 * Insures that only one instance of AffiliateWP_Affiliate_Landing_Pages exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @access public
		 * @since 1.0
		 * @static
		 *
		 * @param string $file Main plugin file.
		 * @return \AffiliateWP_Affiliate_Landing_Pages The one true AffiliateWP_Affiliate_Landing_Pages.
		 */
		public static function instance( $file = null ) {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof AffiliateWP_Affiliate_Landing_Pages ) ) {

				self::$instance = new AffiliateWP_Affiliate_Landing_Pages;

				self::$instance->file = $file;
				self::$instance->version = get_plugin_data( self::$instance->file, false, false )['Version'] ?? '';

				self::$instance->setup_constants();
				self::$instance->load_textdomain();
				self::$instance->includes();
				self::$instance->init();
				self::$instance->hooks();

				if ( is_admin() ) {
					self::$instance->upgrades = new Affiliate_WP_Affiliate_Landing_Pages_Upgrades( self::$instance->version );
				}
			}

			return self::$instance;
		}


		/**
		 * Sets up the plugin constants.
		 *
		 * @since 1.0.3
		 */
		private function setup_constants() {
			// Plugin version.
			if ( ! defined( 'AFFWP_ALP_VERSION' ) ) {
				define( 'AFFWP_ALP_VERSION', $this->version );
			}

			// Plugin Folder Path.
			if ( ! defined( 'AFFWP_ALP_PLUGIN_DIR' ) ) {
				define( 'AFFWP_ALP_PLUGIN_DIR', plugin_dir_path( $this->file ) );
			}

			// Plugin Folder URL.
			if ( ! defined( 'AFFWP_ALP_PLUGIN_URL' ) ) {
				define( 'AFFWP_ALP_PLUGIN_URL', plugin_dir_url( $this->file ) );
			}

			// Plugin Root File.
			if ( ! defined( 'AFFWP_ALP_PLUGIN_FILE' ) ) {
				define( 'AFFWP_ALP_PLUGIN_FILE', $this->file );
			}
		}

		/**
		 * Throws an error on object clone.
		 *
		 * The whole idea of the singleton design pattern is that there is a single
		 * object therefore, we don't want the object to be cloned.
		 *
		 * @access protected
		 * @since  1.0
		 *
		 * @return void
		 */
		protected function __clone() {
			// Cloning instances of the class is forbidden.
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affiliatewp-affiliate-landing-pages' ), '1.0' );
		}

		/**
		 * Disables unserializing of the class.
		 *
		 * @access public
		 * @since  1.0
		 * @since  1.3 @access changed to public
		 *
		 * @return void
		 */
		public function __wakeup() {
			// Unserializing instances of the class is forbidden.
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affiliatewp-affiliate-landing-pages' ), '1.0' );
		}

		/**
		 * Sets up the class.
		 *
		 * @access private
		 * @since  1.0
		 */
		private function __construct() {
			self::$instance = $this;
		}

		/**
		 * Resets the instance of the class.
		 *
		 * @access public
		 * @since  1.0
		 * @static
		 */
		public static function reset() {
			self::$instance = null;
		}

		/**
		 * Loads the plugin language files.
		 *
		 * @access public
		 * @since  1.0
		 *
		 * @return void
		 */
		public function load_textdomain() {

			// Set filter for plugin's languages directory.
			$lang_dir = dirname( plugin_basename( $this->file ) ) . '/languages/';

			/**
			 * Filters the languages directory for AffiliateWP Affiliate Landing Pages plugin.
			 *
			 * @since 1.0
			 *
			 * @param string $lang_dir Language directory.
			 */
			$lang_dir = apply_filters( 'affiliatewp_affiliate_landing_pages_languages_directory', $lang_dir );

			// Traditional WordPress plugin locale filter.
			$locale = apply_filters( 'plugin_locale', get_locale(), 'affiliatewp-affiliate-landing-pages' );
			$mofile = sprintf( '%1$s-%2$s.mo', 'affiliatewp-affiliate-landing-pages', $locale );

			// Setup paths to current locale file.
			$mofile_local  = $lang_dir . $mofile;
			$mofile_global = WP_LANG_DIR . '/affiliatewp-affiliate-landing-pages/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/affiliatewp-affiliate-landing-pages/ folder.
				load_textdomain( 'affiliatewp-affiliate-landing-pages', $mofile_global );
			} elseif ( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/affiliatewp-affiliate-landing-pages/languages/ folder.
				load_textdomain( 'affiliatewp-affiliate-landing-pages', $mofile_local );
			} else {
				// Load the default language files.
				load_plugin_textdomain( 'affiliatewp-affiliate-landing-pages', false, $lang_dir );
			}
		}

		/**
		 * Include necessary files.
		 *
		 * @access private
		 * @since  1.0
		 *
		 * @return void
		 */
		private function includes() {

			if ( is_admin() ) {
				require_once AFFWP_ALP_PLUGIN_DIR . 'includes/class-metabox.php';
				require_once AFFWP_ALP_PLUGIN_DIR . 'includes/class-settings.php';
				require_once AFFWP_ALP_PLUGIN_DIR . 'includes/class-upgrades.php';
			}

			require_once AFFWP_ALP_PLUGIN_DIR . 'includes/functions.php';
			require_once AFFWP_ALP_PLUGIN_DIR . 'includes/class-shortcodes.php';
		}

		/**
		 * Sets up the default hooks and actions.
		 *
		 * @access private
		 * @since  1.0
		 *
		 * @return void
		 */
		private function hooks() {

			// Plugin meta.
			add_filter( 'plugin_row_meta', array( $this, 'plugin_meta' ), null, 2 );

			// List an affiliate's landing pages.
			add_action( 'affwp_affiliate_dashboard_urls_bottom', array( $this, 'list_landing_pages' ), 10, 1 );

			if ( true === affwp_alp_is_enabled() ) {

				add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_scripts' ) );
				add_action( 'wp_enqueue_scripts', array( $this, 'dequeue_tracking' ) );

				if ( ! affiliate_wp()->tracking->use_fallback_method() ) {
					add_action( 'wp_footer', array( $this, 'track_visit' ), 100 );
				} else {
					add_action( 'template_redirect', array( $this, 'fallback_track_visit' ), -9999 );
				}
			}

		}

		/**
		 * Init
		 *
		 * @access private
		 * @since  1.0
		 *
		 * @return void
		 */
		private function init() {
			if ( is_admin() ) {
				self::$instance->updater();
			}
		}

		/**
		 * Load the custom plugin updater
		 *
		 * @access private
		 * @since  1.0
		 *
		 * @return void
		 */
		public function updater() {
			if ( class_exists( 'AffWP_AddOn_Updater' ) ) {
				$updater = new AffWP_AddOn_Updater( 167098, $this->file, $this->version );
			}
		}

		/**
		 * Dequeue AffiliateWP's tracking JS file if an affiliate link is used on a landing page.
		 *
		 * @since 1.0
		 *
		 * @return void
		 */
		public function dequeue_tracking() {

			// Remove tracking script if the landing page is assigned to an affiliate.
			if ( $this->get_affiliate_id( get_the_ID() ) ) {
				wp_dequeue_script( 'affwp-tracking' );
			}

		}

		/**
		 * List an affiliate's landing pages
		 *
		 * @since  1.0
		 * @since  1.3.0 Added copy button for easier sharing and updated design.
		 * @access public
		 *
		 * @param int $affiliate_id The affiliate's ID.
		 * @return void
		 */
		public function list_landing_pages( $affiliate_id = 0 ) {

			$display_pages       = affiliate_wp()->settings->get( 'affiliate-landing-pages', false );
			$affiliate_user_name = affwp_get_affiliate_username( $affiliate_id );
			$landing_page_ids    = affwp_alp_get_landing_page_ids( $affiliate_user_name );
			$has_links_class     = class_exists( 'AffiliateWP\Affiliate_Links' );
			$tooltip_content     = sprintf(
				'<p>%s</p><p>%s</p>',
				esc_html__( 'Landing pages are directly linked to your affiliate account. Unlike standard affiliate links, they don’t need extra tracking parameters—just share them as-is.', 'affiliatewp-affiliate-landing-pages' ),
				esc_html__( 'Share them anywhere: on social media, in emails, on your website, or any other way to earn commissions.', 'affiliatewp-affiliate-landing-pages' )
			);

			// Bail if the setting is disabled or no landing pages are found.
			if ( ! $display_pages || empty( $landing_page_ids ) ) {
				return;
			}
			?>

			<div class="affwp-card affwp-affiliate-landing-pages">
				<div class="affwp-card__header">
					<div>
						<h3>
							<?php count( $landing_page_ids ) === 1 ? esc_html_e( 'Your Landing Page', 'affiliatewp-affiliate-landing-pages' ) : esc_html_e( 'Your Landing Pages', 'affiliatewp-affiliate-landing-pages' ); ?>
						</h3>
						<p>
							<?php count( $landing_page_ids ) === 1 ? esc_html_e( 'Share your landing page to earn commissions.', 'affiliatewp-affiliate-landing-pages' ) : esc_html_e( 'Share these landing pages to earn commissions.', 'affiliatewp-affiliate-landing-pages' ); ?>
							</p>
					</div>

					<?php if ( $has_links_class ) : // Tooltip needs updated version of Core. ?>
					<span class="affwp-card__tooltip" data-tippy-content="<?php echo esc_attr( $tooltip_content ); ?>">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
							<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M9 9.00004c0.00011 -0.54997 0.15139 -1.08933 0.43732 -1.55913s0.69548 -0.85196 1.18398 -1.10472c0.4884 -0.25275 1.037 -0.36637 1.5856 -0.32843 0.5487 0.03793 1.0764 0.22596 1.5254 0.54353 0.449 0.31757 0.8021 0.75246 1.0206 1.25714 0.2186 0.50468 0.2942 1.05973 0.2186 1.60448 -0.0756 0.54475 -0.2994 1.05829 -0.6471 1.48439 -0.3477 0.4261 -0.8059 0.7484 -1.3244 0.9317 -0.2926 0.1035 -0.5459 0.2951 -0.725 0.5485 -0.1791 0.2535 -0.2752 0.5562 -0.275 0.8665v1.006" stroke-width="1.5"></path><path stroke="currentColor" d="M12 18c-0.2071 0 -0.375 -0.1679 -0.375 -0.375s0.1679 -0.375 0.375 -0.375" stroke-width="1.5"></path><path stroke="currentColor" d="M12 18c0.2071 0 0.375 -0.1679 0.375 -0.375s-0.1679 -0.375 -0.375 -0.375" stroke-width="1.5"></path><path stroke="currentColor" stroke-miterlimit="10" d="M12 23.25c6.2132 0 11.25 -5.0368 11.25 -11.25S18.2132 0.75 12 0.75 0.75 5.7868 0.75 12 5.7868 23.25 12 23.25Z" stroke-width="1.5"></path>
						</svg>
					</span>
					<?php endif; ?>

				</div>

				<div class="affwp-card__content">

					<?php foreach ( $landing_page_ids as $id ) : ?>
						<div class="affwp-affiliate-link__display">
							<?php if ( $has_links_class ) : // Styling needs updated version of Core. ?>
								<input
									type="text"
									readonly
									class="affwp-affiliate-link__input"
									value="<?php echo esc_url( get_permalink( $id ) ); ?>"
								>
								<button
										id="affwp-alp-link-copy-link-<?php echo esc_attr( $id ); ?>"
										class="affwp-affiliate-link-copy-link button"
										data-content="<?php echo esc_url( get_permalink( $id ) ); ?>"
									><?php esc_html_e( 'Copy Link', 'affiliate-wp' ); ?>
								</button>
							<?php else : // Otherwise, just list the links. ?>
								<?php echo esc_url( get_permalink( $id ) ); ?>
							<?php endif; ?>

						</div>

					<?php endforeach; ?>

				</div>
			</div>

			<?php
		}

		/**
		 * Load the admin scripts
		 *
		 * @since  1.0
		 * @access public
		 *
		 * @param string $hook The hook suffix.
		 * @return void
		 */
		public function load_admin_scripts( $hook ) {

			if ( 'post.php' === $hook || 'post-new.php' === $hook ) {
				affwp_enqueue_admin_js();

				$ui_style = ( 'classic' === get_user_option( 'admin_color' ) ) ? 'classic' : 'fresh';
				wp_enqueue_style( 'jquery-ui-css', AFFILIATEWP_PLUGIN_URL . 'assets/css/jquery-ui-' . $ui_style . '.min.css' );
			}

		}

		/**
		 * Retrieves the affiliate ID from the post or page
		 *
		 * @since 1.0
		 * @access public
		 *
		 * @param int $post_id Post ID.
		 * @return mixed bool|int Affiliate ID or false if not found.
		 */
		public function get_affiliate_id( $post_id ) {

			// Bail if this post type is inactive.
			$post_types = affwp_alp_get_post_types();
			if ( ! isset( $post_types[ get_post_type( $post_id ) ] ) ) {
				return false;
			}

			// Get the affiliate username.
			$user_name = get_post_meta( $post_id, 'affwp_landing_page_user_name', true );

			if ( ! empty( $user_name ) ) {
				$affiliate    = affwp_get_affiliate( $user_name );
				$affiliate_id = $affiliate->affiliate_id;

				if ( $affiliate_id ) {
					return (int) $affiliate_id;
				}
			}

			return false;

		}

		/**
		 * Store the visit
		 *
		 * @since  1.0
		 * @since  1.0.4 Prevents visits from being tracked on anything but singular content.
		 * @access public
		 *
		 * @return void
		 */
		public function track_visit() {

			// Bail early if this is not a valid single post for an available post type.
			if ( ! affwp_alp_is_singular() ) {
				return;
			}

			$affiliate_id = $this->get_affiliate_id( get_the_ID() );

			if ( empty( $affiliate_id ) ) {
				return;
			}

			$affwp_version = defined( 'AFFILIATEWP_VERSION' ) ? AFFILIATEWP_VERSION : 'undefined';
			if ( version_compare( $affwp_version, '2.7.1', '>=' ) ) {
				$ref_cookie      = affiliate_wp()->tracking->get_cookie_name( 'referral' );
				$visit_cookie    = affiliate_wp()->tracking->get_cookie_name( 'visit' );
				$campaign_cookie = affiliate_wp()->tracking->get_cookie_name( 'campaign' );
			} else {
				$ref_cookie      = 'affwp_ref';
				$visit_cookie    = 'affwp_ref_visit_id';
				$campaign_cookie = 'affwp_campaign';
			}

			?>
			<script>
			jQuery(document).ready( function($) {

				// Affiliate ID
				var ref = "<?php echo $affiliate_id; ?>";
				var ref_cookie = $.cookie( '<?php echo esc_js( $ref_cookie ); ?>' );
				var credit_last = AFFWP.referral_credit_last;
				var campaign = affwp_alp_get_query_vars()['campaign'];

				if ( '1' != credit_last && ref_cookie ) {
					return;
				}

				// If a referral var is present and a referral cookie is not already set
				if ( ref && ! ref_cookie ) {
					affwp_track_visit( ref, campaign );
				} else if( '1' == credit_last && ref && ref_cookie && ref !== ref_cookie ) {
					$.removeCookie( '<?php echo esc_js( $ref_cookie ); ?>' );
					affwp_track_visit( ref, campaign );
				}

				// Track the visit
				function affwp_track_visit( affiliate_id, url_campaign ) {

					// Set the cookie and expire it after 24 hours
					affwp_set_cookie( '<?php echo esc_js( $ref_cookie ); ?>', affiliate_id, { expires: AFFWP.expiration, path: '/' } );

					// Fire an ajax request to log the hit
					$.ajax({
						type: "POST",
						data: {
							action: 'affwp_track_visit',
							affiliate: affiliate_id,
							campaign: url_campaign,
							url: document.URL,
							referrer: document.referrer
						},
						url: affwp_scripts.ajaxurl,
						success: function (response) {
							affwp_set_cookie( '<?php echo esc_js( $visit_cookie ); ?>', response, { expires: AFFWP.expiration, path: '/' } );
							affwp_set_cookie( '<?php echo esc_js( $campaign_cookie ); ?>', url_campaign, { expires: AFFWP.expiration, path: '/' } );
						}

					}).fail(function (response) {
						if ( window.console && window.console.log ) {
							console.log( response );
						}
					});

				}

				/**
				 * Gets url query variables from the current URL.
				 *
				 * @since  1.0
				 *
				 * @return {array} vars The url query variables in the current site url, if present.
				 */
				function affwp_alp_get_query_vars() {
					var vars = [], hash;
					var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
					for(var i = 0; i < hashes.length; i++) {
						hash = hashes[i].split('=');
						vars.push(hash[0]);

						var key = typeof hash[1] == 'undefined' ? 0 : 1;

						// Remove fragment identifiers
						var n = hash[key].indexOf('#');
						hash[key] = hash[key].substring(0, n != -1 ? n : hash[key].length);
						vars[hash[0]] = hash[key];
					}
					return vars;
				}

				/**
				 * Set a cookie, with optional domain if set. Note that providing *any* domain will
				 * set the cookie domain with a leading dot, indicating it should be sent to sub-domains.
				 *
				 * example: host.tld
				 *
				 * - $.cookie( 'some_cookie', ...) = cookie domain: host.tld
				 * - $.cookie ('some_cookie', ... domain: 'host.tld' ) = .host.tld
				 *
				 * @since 2.x.x
				 *
				 * @param {string} name cookie name, e.g. affwp_ref
				 * @param {string} value cookie value
				 */
				function affwp_set_cookie( name, value ) {

					if ( 'cookie_domain' in AFFWP ) {
						$.cookie( name, value, { expires: AFFWP.expiration, path: '/', domain: AFFWP.cookie_domain } );
					} else {
						$.cookie( name, value, { expires: AFFWP.expiration, path: '/' } );
					}
				}

			});

			</script>
			<?php
		}

		/**
		 * Record referral visit via template_redirect
		 *
		 * @since  1.0
		 * @since  1.0.4 Prevents visits from being tracked on anything but singular content.
		 *
		 * @return void
		 */
		public function fallback_track_visit() {

			// Bail early if this is not a valid single post for an available post type.
			if ( ! affwp_alp_is_singular() ) {
				return;
			}

			$affiliate_id = $this->get_affiliate_id( get_the_ID() );

			if ( empty( $affiliate_id ) ) {
				return;
			}

			$is_valid_affiliate = affiliate_wp()->tracking->is_valid_affiliate( $affiliate_id );
			$visit_id           = affiliate_wp()->tracking->get_visit_id();

			if ( $is_valid_affiliate && ! $visit_id ) {

				if ( ( ! empty( $_SERVER['HTTP_REFERER'] ) && ! affwp_is_url_banned( sanitize_text_field( $_SERVER['HTTP_REFERER'] ) ) )
					|| empty( $_SERVER['HTTP_REFERER'] )
				) {

					// Set affiliate ID.
					affiliate_wp()->tracking->set_affiliate_id( $affiliate_id );

					// Store the visit in the DB.
					$visit_id = affiliate_wp()->visits->add( array(
						'affiliate_id' => $affiliate_id,
						'ip'           => affiliate_wp()->tracking->get_ip(),
						'url'          => affiliate_wp()->tracking->get_current_page_url(),
						'campaign'     => affiliate_wp()->tracking->get_campaign(),
						'referrer'     => ! empty( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : '',
					) );

					// Set visit.
					affiliate_wp()->tracking->set_visit_id( $visit_id );

				}

			}

		}

		/**
		 * Modifies the plugin list table meta links.
		 *
		 * @access public
		 * @since  1.0
		 *
		 * @param  array  $links The current links array.
		 * @param  string $file  A specific plugin table entry.
		 * @return array The modified links array.
		 */
		public function plugin_meta( $links, $file ) {

			if ( plugin_basename( $this->file ) === $file ) {

				$url = admin_url( 'admin.php?page=affiliate-wp-add-ons' );

				$plugins_link = array( '<a title="' . esc_attr__( 'Get more add-ons for AffiliateWP', 'affiliatewp-affiliate-landing-pages' ) . '" href="' . esc_url( $url ) . '">' . __( 'More add-ons', 'affiliatewp-affiliate-landing-pages' ) . '</a>' );

				$links = array_merge( $links, $plugins_link );
			}

			return $links;

		}
	}

	/**
	 * The main function responsible for returning the one true AffiliateWP_Affiliate_Landing_Pages
	 * Instance to functions everywhere.
	 *
	 * Use this function like you would a global variable, except without needing
	 * to declare the global.
	 *
	 * Example: <?php $affiliatewp_affiliate_landing_pages = affiliatewp_affiliate_landing_pages(); ?>
	 *
	 * @since  1.0
	 *
	 * @return object The one true AffiliateWP_Affiliate_Landing_Pages Instance
	 */
	function affiliatewp_affiliate_landing_pages() {
		return AffiliateWP_Affiliate_Landing_Pages::instance();
	}
}
