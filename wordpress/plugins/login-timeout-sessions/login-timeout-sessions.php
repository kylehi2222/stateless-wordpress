<?php
/**
 * Package details:
 *
 * @link              https://www.galaxyweblinks.com/
 * @since             1.0.3
 * @package           login-timeout-sessions
 *
 * @wordpress-plugin
 * Plugin Name:       Login Timeout Sessions
 * Plugin URI:        http://wordpress.org/plugins/login-timeout-sessions/
 * Description:       Allows you the ability to set login session / expiry Settings on user capacities by admin panel.
 * Version:           1.0.3
 * Author:            Galaxy Web Links
 * Author URI:        https://profiles.wordpress.org/galaxyweblinks/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       login-timeout-sessions
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

// Define plugin version.
if ( ! defined( 'LTS_LOGIN_TIMEOUT_VERSION' ) ) {
	define( 'LTS_LOGIN_TIMEOUT_VERSION', '1.0.3' );
}

// Register plugin activation hook.
register_activation_hook( __FILE__, 'lts_login_timeout_sessions_plugin_install' );
if ( ! function_exists( 'lts_login_timeout_sessions_plugin_install' ) ) {

	/**
	 * Create new setting for this plugin activation
	 *
	 * @return void
	 */
	function lts_login_timeout_sessions_plugin_install() {
		if ( defined( 'LTS_LOGIN_TIMEOUT_VERSION' ) ) {
			$version = LTS_LOGIN_TIMEOUT_VERSION;
		} else {
			$version = '1.0.3';
		}

		if ( get_option( 'login_timeout_sessions', true ) === false ) {

			$login_timeout_limits = array(
				'default_session'              => 2,
				'default_session_unit',
				60 * 60,
				'enable_session'               => 14,
				'enable_session_unit',
				60 * 60,
				'sessions_capability_timeout'  => 'edit_posts',
				'capabilities_default_session' => 2,
				'capabilities_timeout_unit',
				60 * 60,
				'capabilities_enable_session'  => 14,
				'capabilities_enable_session_unit',
				60 * 60,
				'version'                      => $version,
			);
			// Set @setting when activate the plugin.
			update_option( 'login_timeout_sessions', $login_timeout_limits );
		}
	}
}


// Register plugin deactivation hook.
register_deactivation_hook( __FILE__, 'lts_login_timeout_sessions_plugin_uninstall' );
if ( ! function_exists( 'lts_login_timeout_sessions_plugin_uninstall' ) ) {

	/**
	 * Delete setting for this plugin deactivation
	 *
	 * @return void
	 */
	function lts_login_timeout_sessions_plugin_uninstall() {
		// delete @setting when deactivate the plugin.
		delete_option( 'login_timeout_sessions' );
	}
}

// Filters the duration of the authentication cookie expiration period.
add_filter( 'auth_cookie_expiration', 'lts_login_timeout_sessions_auth_expiry_filter', 99, 3 );
if ( ! function_exists( 'lts_login_timeout_sessions_auth_expiry_filter' ) ) {

	/**
	 * Modified authentication cookie expiration period
	 *
	 * @param  int     $auth_expiry time in seconds.
	 * @param  int     $user_id User Id.
	 * @param  boolean $enable true | false.
	 *
	 * @return int
	 */
	function lts_login_timeout_sessions_auth_expiry_filter( $auth_expiry, $user_id, $enable ) {

		$login_timeout_limits = get_option( 'login_timeout_sessions', true );

		if ( isset( $login_timeout_limits['activate_session_timeout'] ) && true === $login_timeout_limits['activate_session_timeout'] && user_can( $user_id, $login_timeout_limits['sessions_capability_timeout'] ) ) {

			if ( $enable ) {
				$auth_expiry = $login_timeout_limits['capabilities_enable_session'] * $login_timeout_limits['capabilities_enable_session_unit'];
			} else {
				$auth_expiry = $login_timeout_limits['capabilities_timeout'] * $login_timeout_limits['capabilities_timeout_unit'];
			}
		} else {

			if ( $enable ) {
				$auth_expiry = $login_timeout_limits['enable_session'] * $login_timeout_limits['enable_session_unit'];
			} else {
				$auth_expiry = $login_timeout_limits['default_session'] * $login_timeout_limits['default_session_unit'];
			}
		}

		// The largest integer supported in this build of PHP. Usually int(2147483647) in 32 bit systems and int(9223372036854775807) in 64 bit systems.
		if ( PHP_INT_MAX - time() < $auth_expiry ) {
			$auth_expiry = PHP_INT_MAX - time() - 5;
		}
		return $auth_expiry;
	}
}

// Fires after WordPress has finished loading but before any headers are sent.
add_action( 'init', 'lts_login_timeout_sessions_init_level_callback' );
if ( ! function_exists( 'lts_login_timeout_sessions_init_level_callback' ) ) {

	/**
	 * Add hooks for create admin menu and create plugin link.
	 *
	 * @return void
	 */
	function lts_login_timeout_sessions_init_level_callback() {
		// Add admin menu hook.
		add_action( 'admin_menu', 'lts_login_timeout_sessions_menu_page_register' );

		// Give the plugin a settings link in the plugin.
		add_filter( 'plugin_action_links', 'lts_login_timeout_sessions_add_action_link', 10, 2 );
	}
}

if ( ! function_exists( 'lts_login_timeout_sessions_menu_page_register' ) ) {

	/**
	 * Add setting menu page.
	 * add hook for Load guide to set values in plugin page
	 *
	 * @return void
	 */
	function lts_login_timeout_sessions_menu_page_register() {
		global $login_timeout_sessions_menu_page;

		$login_timeout_sessions_menu_page = add_options_page( __( 'Login Sessions', 'login-timeout-sessions' ), __( 'Login Sessions', 'login-timeout-sessions' ), 'manage_options', 'login_timeout_sessions', 'lts_add_login_timeout_sessions_menu_page' );
		add_action( 'load-' . $login_timeout_sessions_menu_page, 'lts_login_timeout_sessions_guide_texts_display' );
	}
}

if ( ! function_exists( 'lts_add_login_timeout_sessions_menu_page' ) ) {

	/**
	 * Add hook for enqueue css file for page styles.
	 * Create option form in page.
	 *
	 * @return void
	 */
	function lts_add_login_timeout_sessions_menu_page() {
		if ( defined( 'LTS_LOGIN_TIMEOUT_VERSION' ) ) {
			$version = LTS_LOGIN_TIMEOUT_VERSION;
		} else {
			$version = '1.0.3';
		}

		?>
	<div class="wrap">
		<h2><?php __( 'Login Timeout Sessions', 'login-timeout-sessions' ); ?></h2>
		<form action="options.php" method="post">
			<?php settings_fields( 'login_timeout_sessions' ); ?>
			<?php do_settings_sections( 'login_timeout_sessions_sections' ); ?>
			<?php submit_button(); ?>
		</form>
	</div>
		<?php
	}
}

if ( ! function_exists( 'lts_login_timeout_sessions_guide_texts_display' ) ) {

	/**
	 * Add guide texts in page, if current screen is matched.
	 *
	 * @return void
	 */
	function lts_login_timeout_sessions_guide_texts_display() {
		global $login_timeout_sessions_menu_page;
		$screen = get_current_screen();

		if ( $screen->id !== $login_timeout_sessions_menu_page ) {
			return;
		}

		$screen->add_help_tab(
			array(
				'id'      => 'wpus_help_login_timeout_tab',
				'title'   => __( 'Authentication cookies', 'login-timeout-sessions' ),
				'content' => '<p><ul><li>' . __( 'By default, the authentication cookies remembering is 2 days. When "Remember me" is set, the cookies will be kept for 14 days. (see <a href="http://codex.wordpress.org/Function_Reference/wp_set_auth_cookie" target="_blank">Function Reference/wp set auth cookie in Wordpress Codex</a>)', 'login-timeout-sessions' ) . '</li>'
					. '</ul>'
					. '</p>',
			)
		);

		$screen->set_help_sidebar(
			'<p><strong>'
			. __( 'For more information:', 'login-timeout-sessions' )
			. '</strong></p>'
			. '<p>'
			. '<a href="http://wordpress.org/plugins/login-timeout-sessions/" target="_blank">' . __( 'Visit plugin page', 'login-timeout-sessions' ) . '</a>'
			. '</p>'
		);
	}
}

if ( ! function_exists( 'lts_login_timeout_sessions_add_action_link' ) ) {

	/**
	 * Add page link to menu page function.
	 * redirect plugin link in setting menu.
	 *
	 * @param  string $source link create in settings.
	 * @param  string $plugin_file plugin file path.
	 *
	 * @return string
	 */
	function lts_login_timeout_sessions_add_action_link( $source, $plugin_file ) {

		static $current_plugin;

		if ( false === is_array( $source ) ) {
			$links = array( $source );
		} else {
			$links = $source;
		}

		if ( ! $current_plugin ) {
			$current_plugin = plugin_basename( __FILE__ );
		}

		if ( $plugin_file === $current_plugin ) {
			$settings_link = '<a href="options-general.php?page=login_timeout_sessions">' . __( 'Settings', 'login-timeout-sessions' ) . '</a>';
			array_unshift( $links, $settings_link );
		}

		return $links;
	}
}

// Add hook for admin screen or script is being initialized.
add_action( 'admin_init', 'lts_login_timeout_sessions_admin_init_callback' );
if ( ! function_exists( 'lts_login_timeout_sessions_admin_init_callback' ) ) {

	/**
	 * Register settings in Admin page function.
	 * Add setting input fields and section texts.
	 *
	 * @return void
	 */
	function lts_login_timeout_sessions_admin_init_callback() {
		// Register setting in Settings.
		register_setting( 'login_timeout_sessions', 'login_timeout_sessions', 'lts_login_timeout_sessions_setting_register' );

		// Display the section in the Setting form.
		add_settings_section( 'login_timeout_default_sessions', __( 'Sessions settings', 'login-timeout-sessions' ), 'lts_login_timeout_session_head', 'login_timeout_sessions_sections' );

		// Display input field in the Setting form.
		add_settings_field( 'wp_default_session', __( 'Set new session (default: 2 days)', 'login-timeout-sessions' ), 'lts_login_default_session_text_input', 'login_timeout_sessions_sections', 'login_timeout_default_sessions' );

		// Display input field in the Setting form.
		add_settings_field( 'wp_remember_me_timeout', __( 'Remember me session (default: 14 days)', 'login-timeout-sessions' ), 'lts_login_timeout_sessions_remember_me_text_input', 'login_timeout_sessions_sections', 'login_timeout_default_sessions' );

		// Display the section in the Setting form.
		add_settings_section( 'login_capability_timeout_sessions', __( 'Special session settings based on user\'s capability', 'login-timeout-sessions' ), 'lts_login_timeout_sessions_capability_notice_text', 'login_timeout_sessions_sections' );

		// Display checkbox in the Setting form.
		add_settings_field( 'wp_activate_capabilities_timeout', __( 'Activate special session settings', 'login-timeout-sessions' ), 'lts_activate_capability_session_checkbox', 'login_timeout_sessions_sections', 'login_capability_timeout_sessions' );

		// Display input field in the Setting form.
		add_settings_field( 'wp_select_capabilities_timeout', __( 'Choose user capability activating the special session (default: edit_posts)', 'login-timeout-sessions' ), 'lts_choose_capabilities_timeout_sessions', 'login_timeout_sessions_sections', 'login_capability_timeout_sessions' );

		// Display input field in the Setting form.
		add_settings_field( 'wp_capabilities_timeout', __( 'Special new session (default: 2 days)', 'login-timeout-sessions' ), 'lts_capability_timeout_sessions_text_input', 'login_timeout_sessions_sections', 'login_capability_timeout_sessions' );

		// Display input field in the Setting form.
		add_settings_field( 'wp_remember_me_capabilities_timeout', __( 'Special Remember me session (default: 14 days)', 'login-timeout-sessions' ), 'lts_capability_remember_me_timeout_sessions_text_input', 'login_timeout_sessions_sections', 'login_capability_timeout_sessions' );
	}
}

if ( ! function_exists( 'lts_login_timeout_session_head' ) ) {

	/**
	 * Add head description after form title.
	 *
	 * @return void
	 */
	function lts_login_timeout_session_head() {
		esc_html_e( 'By default, the authentication cookies remembering is 2 days. When "Remember me" is set, the cookies will be kept for 14 days. This panel allows you to change these settings.', 'login-timeout-sessions' );
	}
}

if ( ! function_exists( 'lts_login_default_session_text_input' ) ) {

	/**
	 * Add input field and default value in setting form for new sessions.
	 * With option for session expired on minutes, hours, and days.
	 *
	 * @return void
	 */
	function lts_login_default_session_text_input() {
		$sessions           = get_option( 'login_timeout_sessions' );
		$session_value      = isset( $sessions['default_session'] ) ? $sessions['default_session'] : 2;
		$session_value_unit = isset( $sessions['default_session_unit'] ) ? $sessions['default_session_unit'] : 3600;
		ob_start();
		?>
	<input name="login_timeout_sessions[default_session]" type="text" value="<?php echo intval( $session_value ); ?>">

	<select name="login_timeout_sessions[default_session_unit]">
		<option value="60" <?php echo selected( intval( $session_value_unit ), 60, false ); ?>> <?php esc_html_e( 'minute(s)', 'login-timeout-sessions' ); ?> </option>
		<option value="3600" <?php echo selected( intval( $session_value_unit ), 3600, false ); ?>> <?php esc_html_e( 'hour(s)', 'login-timeout-sessions' ); ?> </option>
		<option value="86400" <?php echo selected( intval( $session_value_unit ), 86400, false ); ?>> <?php esc_html_e( 'day(s)', 'login-timeout-sessions' ); ?></option>
	</select>
		<?php
		$html    = ob_get_clean();
		$allowed = array(
			'input'  => array(
				'value' => array(),
				'type'  => array(),
				'name'  => array(),
			),
			'select' => array( 'name' => array() ),
			'option' => array(
				'value'    => array(),
				'selected' => array(),
			),
		);
		echo wp_kses( $html, $allowed );
	}
}

if ( ! function_exists( 'lts_login_timeout_sessions_remember_me_text_input' ) ) {

	/**
	 * Add input field and default value in setting form to enable session.
	 * With option for session expired on minutes, hours, and days.
	 *
	 * @return void
	 */
	function lts_login_timeout_sessions_remember_me_text_input() {
		$sessions           = get_option( 'login_timeout_sessions' );
		$session_value      = isset( $sessions['enable_session'] ) ? $sessions['enable_session'] : 14;
		$session_value_unit = isset( $sessions['enable_session_unit'] ) ? $sessions['enable_session_unit'] : 3600;
		ob_start();
		?>
	<input name="login_timeout_sessions[enable_session]" type="text" value="<?php echo intval( $session_value ); ?>">
	<select name="login_timeout_sessions[enable_session_unit]">
		<option value="60" <?php echo selected( intval( $session_value_unit ), 60, false ); ?>><?php esc_html_e( 'minute(s)', 'login-timeout-sessions' ); ?></option>
		<option value="3600" <?php echo selected( intval( $session_value_unit ), 3600, false ); ?>><?php esc_html_e( 'hour(s)', 'login-timeout-sessions' ); ?></option>
		<option value="86400" <?php echo selected( intval( $session_value_unit ), 86400, false ); ?>><?php esc_html_e( 'day(s)', 'login-timeout-sessions' ); ?></option>
	</select>
		<?php
		$html    = ob_get_clean();
		$allowed = array(
			'input'  => array(
				'value' => array(),
				'type'  => array(),
				'name'  => array(),
			),
			'select' => array( 'name' => array() ),
			'option' => array(
				'value'    => array(),
				'selected' => array(),
			),
		);
		echo wp_kses( $html, $allowed );
	}
}

if ( ! function_exists( 'lts_login_timeout_sessions_capability_notice_text' ) ) {

	/**
	 * Add capabilities section texts with example and references.
	 *
	 * @return void
	 */
	function lts_login_timeout_sessions_capability_notice_text() {
		esc_html_e( 'You can set a different login timeout sessions to Users with a specific capability.', 'login-timeout-sessions' );
		echo '<br>';
		echo 'eg. : You can set longer/shorter login timeout session to Administrators using "edit_theme_options" capability. (see <a href="https://wordpress.org/support/article/roles-and-capabilities/" target="_blank">Roles and Capabilities in WordPress Codex</a>)';
	}
}

if ( ! function_exists( 'lts_activate_capability_session_checkbox' ) ) {

	/**
	 * Add sessions enable / disable checkbox.
	 *
	 * @return void
	 */
	function lts_activate_capability_session_checkbox() {
		$sessions      = get_option( 'login_timeout_sessions' );
		$session_value = isset( $sessions['activate_session_timeout'] ) ? $sessions['activate_session_timeout'] : 0;
		echo '<input type="checkbox" name="login_timeout_sessions[activate_session_timeout]" value="1" ' . checked( intval( $session_value ), 1, false ) . ' />';
	}
}

if ( ! function_exists( 'lts_choose_capabilities_timeout_sessions' ) ) {

	/**
	 * Add session capacity options within the form.
	 *
	 * @return void
	 */
	function lts_choose_capabilities_timeout_sessions() {
		$capability_list = lts_get_user_capebility_list();

		$sessions      = get_option( 'login_timeout_sessions', true );
		$session_value = isset( $sessions['sessions_capability_timeout'] ) ? $sessions['sessions_capability_timeout'] : 0;

		if ( ! $capability_list ) {
			exit();
		}
		echo '<select name="login_timeout_sessions[sessions_capability_timeout]">';

		foreach ( $capability_list as $capability ) {
			if ( strpos( $capability, 'level' ) === false ) {
				echo '<option value="' . esc_html( $capability ) . '"' . selected( $session_value, $capability, false ) . '>' . esc_html( $capability ) . '</option>';
			}
		}

		echo '</select>';
	}
}

if ( ! function_exists( 'lts_capability_timeout_sessions_text_input' ) ) {

	/**
	 * Add Special sessions input field and default value in the form.
	 * With option for session expired on minutes, hours, and days.
	 *
	 * @return void
	 */
	function lts_capability_timeout_sessions_text_input() {
		$sessions           = get_option( 'login_timeout_sessions', true );
		$session_value      = isset( $sessions['capabilities_timeout'] ) ? $sessions['capabilities_timeout'] : 2;
		$session_value_unit = isset( $sessions['capabilities_timeout_unit'] ) ? $sessions['capabilities_timeout_unit'] : 3600;
		ob_start();
		?>
	<input name="login_timeout_sessions[capabilities_timeout]" type="text" value="<?php echo intval( $session_value ); ?>">
	<select name="login_timeout_sessions[capabilities_timeout_unit]">
		<option value="60" <?php echo selected( intval( $session_value_unit ), 60, false ); ?>><?php esc_html_e( 'minute(s)', 'login-timeout-sessions' ); ?> </option>
		<option value="3600" <?php echo selected( intval( $session_value_unit ), 3600, false ); ?>><?php esc_html_e( 'hour(s)', 'login-timeout-sessions' ); ?> </option>
		<option value="86400" <?php echo selected( intval( $session_value_unit ), 86400, false ); ?>><?php esc_html_e( 'day(s)', 'login-timeout-sessions' ); ?></option>
	</select>
		<?php
		$html    = ob_get_clean();
		$allowed = array(
			'input'  => array(
				'value' => array(),
				'type'  => array(),
				'name'  => array(),
			),
			'select' => array( 'name' => array() ),
			'option' => array(
				'value'    => array(),
				'selected' => array(),
			),
		);
		echo wp_kses( $html, $allowed );
	}
}

if ( ! function_exists( 'lts_capability_remember_me_timeout_sessions_text_input' ) ) {

	/**
	 * Add Special enable sessions, input field and default value in the form.
	 * With option for session expired on minutes, hours, and days.
	 *
	 * @return void
	 */
	function lts_capability_remember_me_timeout_sessions_text_input() {
		$sessions           = get_option( 'login_timeout_sessions', true );
		$session_value      = isset( $sessions['capabilities_enable_session'] ) ? $sessions['capabilities_enable_session'] : 14;
		$session_value_unit = isset( $sessions['capabilities_enable_session_unit'] ) ? $sessions['capabilities_enable_session_unit'] : 3600;
		ob_start();
		?>

	<input name="login_timeout_sessions[capabilities_enable_session]" type="text" value="<?php echo intval( $session_value ); ?>">
	<select name="login_timeout_sessions[capabilities_enable_session_unit]">
		<option value="60" <?php echo selected( intval( $session_value_unit ), 60, false ); ?>><?php esc_html_e( 'minute(s)', 'login-timeout-sessions' ); ?></option>
		<option value="3600" <?php echo selected( intval( $session_value_unit ), 3600, false ); ?>><?php esc_html_e( 'hour(s)', 'login-timeout-sessions' ); ?></option>
		<option value="86400" <?php echo selected( intval( $session_value_unit ), 86400, false ); ?>><?php esc_html_e( 'day(s)', 'login-timeout-sessions' ); ?></option>
	</select>
		<?php
		$html    = ob_get_clean();
		$allowed = array(
			'input'  => array(
				'value' => array(),
				'type'  => array(),
				'name'  => array(),
			),
			'select' => array( 'name' => array() ),
			'option' => array(
				'value'    => array(),
				'selected' => array(),
			),
		);
		echo wp_kses( $html, $allowed );
	}
}

if ( ! function_exists( 'lts_login_timeout_sessions_setting_register' ) ) {

	/**
	 * Register settings form value validate function.
	 * Update the setting values.
	 *
	 * @param array $values return form value after validate.
	 *
	 * @return array
	 */
	function lts_login_timeout_sessions_setting_register( $values ) {

		if ( defined( 'LTS_LOGIN_TIMEOUT_VERSION' ) ) {
			$version = LTS_LOGIN_TIMEOUT_VERSION;
		} else {
			$version = '1.0.3';
		}

		$capability_list = lts_get_user_capebility_list();

		$value = array();

		$default_session          = sanitize_text_field( $values['default_session'] );
		$value['default_session'] = ( intval( $default_session ) === false || intval( $default_session ) < 1 ) ? 2 : intval( $default_session );

		$default_session_unit          = sanitize_text_field( $values['default_session_unit'] );
		$value['default_session_unit'] = ( ( intval( $default_session_unit ) || intval( $default_session ) < 1 ) === false ) ? 86400 : intval( $default_session_unit );

		$enable_session          = sanitize_text_field( $values['enable_session'] );
		$value['enable_session'] = ( intval( $enable_session ) === false || intval( $enable_session ) < 1 ) ? 14 : intval( $enable_session );

		$enable_session_unit          = sanitize_text_field( $values['enable_session_unit'] );
		$value['enable_session_unit'] = ( intval( $enable_session_unit ) === false || intval( $enable_session ) < 1 ) ? 86400 : intval( $enable_session_unit );

		$activate_session_timeout = sanitize_text_field( $values['activate_session_timeout'] );
		if ( isset( $activate_session_timeout ) && filter_var( $activate_session_timeout, FILTER_VALIDATE_BOOLEAN ) ) {
			$value['activate_session_timeout'] = $activate_session_timeout;
		}

		$capabilities_timeout          = sanitize_text_field( $values['capabilities_timeout'] );
		$value['capabilities_timeout'] = ( intval( $capabilities_timeout ) === false || intval( $capabilities_timeout ) < 1 ) ? 2 : intval( $capabilities_timeout );

		$sessions_capability_timeout          = sanitize_text_field( $values['sessions_capability_timeout'] );
		$value['sessions_capability_timeout'] = in_array( $sessions_capability_timeout, $capability_list, true ) ? $sessions_capability_timeout : 'edit_posts';

		$capabilities_timeout_unit          = sanitize_text_field( $values['capabilities_timeout_unit'] );
		$value['capabilities_timeout_unit'] = ( ( intval( $capabilities_timeout_unit ) || intval( $capabilities_timeout ) < 1 ) === false ) ? 86400 : intval( $capabilities_timeout_unit );

		$capabilities_enable_session          = sanitize_text_field( $values['capabilities_enable_session'] );
		$value['capabilities_enable_session'] = ( intval( $capabilities_enable_session ) === false || intval( $capabilities_enable_session ) < 1 ) ? 14 : intval( $capabilities_enable_session );

		$capabilities_enable_session_unit          = sanitize_text_field( $values['capabilities_enable_session_unit'] );
		$value['capabilities_enable_session_unit'] = ( intval( $capabilities_enable_session_unit ) === false || intval( $capabilities_enable_session ) < 1 ) ? 86400 : intval( $capabilities_enable_session_unit );

		$value['version'] = $version;

		return $value;
	}
}

if ( ! function_exists( 'lts_get_user_capebility_list' ) ) {

	/**
	 * Get the capability lists feature.
	 *
	 * @return array
	 */
	function lts_get_user_capebility_list() {
		$capability_list = array();

		$get_admin_roles = get_role( 'administrator' );
		$get_admin_roles = $get_admin_roles->capabilities;
		$get_admin_roles = array_keys( $get_admin_roles );
		foreach ( $get_admin_roles as $capability ) {
			if ( strpos( $capability, 'level' ) === false ) {
				$capability_list[] = $capability;
			}
		}

		sort( $capability_list );

		return $capability_list;
	}
}


?>
