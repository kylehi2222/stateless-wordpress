<?php
/**
 * User Login Bootstrap
 *
 * @package     AffiliateWP
 * @subpackage  Core
 * @copyright   Copyright (c) 2014, Sandhills Development, LLC
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

class Affiliate_WP_Login {

	private $errors;

	/**
	 * Get things started
	 *
	 * @since 1.0
	 */
	public function __construct() {

		add_action( 'affwp_user_login', [ $this, 'process_login' ] );
		add_action( 'template_redirect', [ $this, 'redirect_out_from_login_page_when_logged' ] );
	}

	/**
	 * Prevents users from direct accessing the Login page if they are already logged.
	 *
	 * @since 2.25.0
	 * @return void
	 */
	public function redirect_out_from_login_page_when_logged() {

		if ( ! is_user_logged_in() ) {
			return; // Bail if the user is not logged.
		}

		$affiliate_area_page_id = affwp_get_affiliate_area_page_id();
		$login_page_id          = affiliatewp_get_affiliate_login_page_id();

		if ( $affiliate_area_page_id === $login_page_id ) {
			return; // Bail since the login page is the same as the Affiliate Area page.
		}

		if ( is_page( $login_page_id ) && ! empty( $affiliate_area_page_id ) ) {
			wp_safe_redirect(
				get_permalink( $affiliate_area_page_id )
			);
			exit;
		}
	}

	/**
	 * Login Form
	 *
	 * @since 1.2
	 * @global $affwp_login_redirect
	 * @param string $redirect Redirect page URL
	 * @return string Login form
	*/
	public function login_form( $redirect = '' ) {
		global $affwp_login_redirect;

		if ( empty( $redirect ) ) {
			$redirect = affiliate_wp()->tracking->get_current_page_url();
		}

		$affwp_login_redirect = $redirect;

		ob_start();

		affiliate_wp()->templates->get_template_part( 'login' );

		/**
		 * Filters the output for the AffiliateWP login form.
		 *
		 * @since 1.2
		 *
		 * @param string $output Output for the login form.
		 */
		return apply_filters( 'affwp_login_form', ob_get_clean() );
	}

	/**
	 * Process the loginform submission
	 *
	 * @since 1.0
	 */
	public function process_login( $data ) {

		if ( ! isset( $_POST['affwp_login_nonce'] ) || ! wp_verify_nonce( $_POST['affwp_login_nonce'], 'affwp-login-nonce' ) ) {
			return;
		}

		/**
		 * Fires immediately prior to processing the affiliate login form.
		 *
		 * @since 1.0
		 */
		do_action( 'affwp_pre_process_login_form' );

		if ( empty( $data['affwp_user_login'] ) ) {
			$this->add_error( 'empty_username', __( 'Invalid username', 'affiliate-wp' ) );

			$data['affwp_user_login'] = '';
		}

		$user_login = sanitize_text_field( $data['affwp_user_login'] );

		$user = get_user_by( 'login', $user_login );

		if ( ! $user ) {
			$user = get_user_by( 'email', $user_login );
		}

		if ( ! $user ) {
			$this->add_error( 'no_such_user', __( 'No such user', 'affiliate-wp' ) );
		}

		/**
		 * Filters whether to perform the password check on affiliate login.
		 *
		 * @since 2.0.6
		 *
		 * @param bool     $check Whether to check the password or not.
		 * @param \WP_User $user  The WordPress user whose password is being checked.
		 */
		if ( true === apply_filters( 'affwp_login_check_password', true, $user ) ) {

			if ( empty( $data['affwp_user_pass'] ) ) {
				$this->add_error( 'empty_password', __( 'Please enter a password', 'affiliate-wp' ) );
			}

			if ( $user ) {
				// check the user's login with their password
				if ( ! wp_check_password( $data['affwp_user_pass'], $user->user_pass, $user->ID ) ) {
					// if the password is incorrect for the specified user
					$this->add_error( 'password_incorrect', __( 'Incorrect username or password', 'affiliate-wp' ) );
				}
			}

		}

		if ( function_exists( 'is_limit_login_ok' ) && ! is_limit_login_ok() ) {

			$this->add_error( 'limit_login_failed', limit_login_error_msg() );

		}

		/**
		 * Fires immediately after processing an affiliate login form.
		 *
		 * @since 1.0
		 */
		do_action( 'affwp_process_login_form' );


		// only log the user in if there are no errors
		if ( empty( $this->errors ) ) {

			$remember = ! empty( $data['affwp_user_remember'] );

			$this->log_user_in( $user->ID, $user_login, $remember );

			$redirect = empty( $data['affwp_redirect'] ) ? affwp_get_affiliate_area_page_url() : $data['affwp_redirect'];

			/**
			 * Filters the redirect URL used after a successful login via the AffiliateWP login form.
			 *
			 * @since 1.0
			 *
			 * @param string $redirect Login redirect URL.
			 */
			$redirect = apply_filters( 'affwp_login_redirect', $redirect );

			if ( $redirect ) {
				wp_redirect( $redirect ); exit;
			}

		} else {

			if ( function_exists( 'limit_login_failed' ) ) {
				limit_login_failed( $user_login );
			}

		}
	}

	/**
	 * Log the user in
	 *
	 * @since 1.0
	 */
	private function log_user_in( $user_id = 0, $user_login = '', $remember = false ) {

		$user = get_userdata( $user_id );
		if ( ! $user )
			return;

		wp_set_auth_cookie( $user_id, $remember );
		wp_set_current_user( $user_id, $user_login );
		/**
		 * The `wp_login` action is fired here to maintain compatibility and stability of
		 * any WordPress core features, plugins, or themes hooking onto it.
		 */
		do_action( 'wp_login', $user_login, $user );

	}

	/**
	 * Register a submission error
	 *
	 * @since 1.0
	 */
	public function add_error( $error_id, $message = '' ) {
		$this->errors[ $error_id ] = $message;
	}

	/**
	 * Print errors
	 *
	 * @since 1.0
	 */
	public function print_errors() {

		if ( empty( $this->errors ) ) {
			return;
		}

		echo '<div class="affwp-errors">';

		foreach( $this->errors as $error_id => $error ) {

			echo '<p class="affwp-error">' . esc_html( $error ) . '</p>';

		}

		echo '</div>';

	}

	/**
	 * Retrieves the login URL
	 *
	 * @since 1.1
	 * @since 2.25.0 The new Affiliate Login page is used instead of the Affiliate Area page to return the login URL.
	 */
	function get_login_url() {
		/**
		 * Filters the AffiliateWP login URL.
		 *
		 * @since 1.1
		 *
		 * @param string $url Login URL.
		 */
	    return apply_filters( 'affwp_login_url', get_permalink( affiliatewp_get_affiliate_login_page_id() ) );
	}

}
