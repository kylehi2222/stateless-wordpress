<?php
/**
 * TwoFactor Model
 * Handles the plugin twofactor process
 *
 * @file  The TwoFactor Model file
 * @package HMWPP/TwofactorModel
 * @since 1.0.0
 */
defined('ABSPATH') || die('Cheatin\' uh?');

class HMWPP_Models_Twofactor
{

    /** @var array All auth tokens */
    public static $tokens = array();

    /** @var string login success */
    const USER_NONCE = '_hmwp_nonce';

    /** @var string fail login attempts  */
    const USER_FAILURES = '_hmwp_login_failure';

    /** @var string login attempts */
    const USER_ATTEMPTS = '_hmwp_login_attempts';

    /** @var string login attempts */
    const USER_SUCCESS = '_hmwp_last_login';

    /**
     * Display the login form.
     *
     * @param WP_User $user The WP_User instance representing the currently logged-in user.
     */
    public function showTwoFactorLogin( $user ) {

        if ( ! $user ) {
            $user = wp_get_current_user();
        }

        $login_nonce = $this->createLoginNonce( $user->ID );

        if ( empty($login_nonce) ) {
            wp_die( esc_html__( 'Failed to create a login nonce.', 'hide-my-wp-pack' ) );
        }

        $redirect_to = HMWPP_Classes_Tools::getValue('redirect_to', admin_url());

        $this->loginHtml( $user, $login_nonce['key'], $redirect_to );
    }

    /**
     * Generates the html form for the second step of the authentication process.
     *
     * @param WP_User       $user The WP_User instance representing the currently logged-in user.
     * @param string        $login_nonce A string nonce stored in usermeta.
     * @param string        $redirect_to The URL to which the user would like to be redirected.
     * @param string        $error_msg Optional. Login error message.
     */
    public function loginHtml( $user, $login_nonce, $redirect_to, $error_msg = '' ) {

        $twoFactorService = false;

        /** @var HMWPP_Models_Services_Tftotp $twoFactorService */
        $twoFactorService = HMWPP_Classes_ObjController::getClass('HMWPP_Models_Services_Tftotp');

        /** @var HMWPP_Models_Services_Email $emailService */
        $emailService = HMWPP_Classes_ObjController::getClass('HMWPP_Models_Services_Email');

        // If none of the services are active
        if( !$twoFactorService->isServiceActive( $user ) && !$emailService->isServiceActive( $user ) ){
            return '';
        }

        if($emailService->isServiceActive( $user )){
            $service =  $emailService;
        }

        if($twoFactorService->isServiceActive( $user )){
            $service =  $twoFactorService;
        }

        $interim_login = HMWPP_Classes_Tools::getValue('interim-login');

        //check if remember is on
        $remember = (int) $this->remember();

        if ( ! function_exists( 'login_header' ) ) {
            // We really should migrate login_header() out of `wp-login.php` so it can be called from an includes file.
            include_once _HMWPP_THEME_DIR_ . 'login/header.php';
        }

        login_header();

        if ( ! empty( $error_msg ) ) {
            echo '<div id="login_error"><strong>' . esc_html( $error_msg ) . '</strong><br /></div>';
        } else {
            $this->showNotices( $user );
        }
        ?>

        <form name="validate_2fa_form" id="loginform" action="<?php echo esc_url( $this->loginUrl( array( 'action' => 'validate_2fa' ), 'login_post' ) ); ?>" method="post" autocomplete="off">
            <input type="hidden" name="wp-auth-id"    id="wp-auth-id"    value="<?php echo esc_attr( $user->ID ); ?>" />
            <input type="hidden" name="wp-auth-nonce" id="wp-auth-nonce" value="<?php echo esc_attr( $login_nonce ); ?>" />
            <?php if ( $interim_login ) { ?>
                <input type="hidden" name="interim-login" value="1" />
            <?php } else { ?>
                <input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect_to ); ?>" />
            <?php } ?>
            <input type="hidden" name="rememberme" id="rememberme" value="<?php echo esc_attr( $remember ); ?>" />

            <?php $service->authenticationPage( $user ); ?>
        </form>
        <style>
            /* Prevent Jetpack from hiding our controls, see https://github.com/Automattic/jetpack/issues/3747 */
            .jetpack-sso-form-display #loginform > p,
            .jetpack-sso-form-display #loginform > div {
                display: block;
            }
            form#loginform p.hmwp-prompt {
                margin-bottom: 1em;
            }
            form#loginform .input.authcode {
                letter-spacing: .3em;
            }
            form#loginform .input.authcode::placeholder {
                opacity: 0.5;
            }
        </style>
        <script>
            (function() {
                // Enforce numeric-only input for numeric inputmode elements.
                const form = document.querySelector( '#loginform' ),
                    inputEl = document.querySelector( 'input.authcode[inputmode="numeric"]' ),
                    expectedLength = inputEl?.dataset.digits || 0;

                if ( inputEl ) {
                    let spaceInserted = false;
                    inputEl.addEventListener(
                        'input',
                        function() {
                            let value = this.value.replace( /[^0-9 ]/g, '' ).trimStart();

                            if ( ! spaceInserted && expectedLength && value.length === Math.floor( expectedLength / 2 ) ) {
                                value += ' ';
                                spaceInserted = true;
                            } else if ( spaceInserted && ! this.value ) {
                                spaceInserted = false;
                            }

                            this.value = value;

                            // Auto-submit if it's the expected length.
                            if ( expectedLength && value.replace( / /g, '' ).length == expectedLength ) {
                                if ( undefined !== form.requestSubmit ) {
                                    form.requestSubmit();
                                    form.submit.disabled = "disabled";
                                }
                            }
                        }
                    );
                }
            })();
        </script>
        <?php
        if ( ! function_exists( 'login_footer' ) ) {
            include_once _HMWPP_THEME_DIR_ . 'login/footer.php';
        }

        login_footer();
        exit();
    }

    /**
     * Generate the two-factor login form URL.
     *
     * @param  array  $params List of query argument pairs to add to the URL.
     * @param  string $scheme URL scheme context.
     *
     * @return string
     */
    public static function loginUrl( $params = array(), $scheme = 'login' ) {
        if ( ! is_array( $params ) ) {
            $params = array();
        }

        $params = urlencode_deep( $params );

        return add_query_arg( $params, site_url( 'wp-login.php', $scheme ) );
    }

    /**
     * Validate 2FA login attempt with the current attempt type
     *
     * @return void
     * @throws Exception
     */
    public function validateTwoFactor() {

        $user_id      = HMWPP_Classes_Tools::getValue('wp-auth-id');
        $nonce        = HMWPP_Classes_Tools::getValue('wp-auth-nonce');

        if ( !$user_id || !$nonce ) {
            return;
        }

        //check if it's a valid user
        if ( !$user = get_userdata( $user_id ) ) {
            return;
        }

        //check the current user nonce
        if ( true !== $this->verifyLoginNonce( $user->ID, $nonce ) ) {
            wp_safe_redirect( home_url() );
            exit;
        }

        /** @var HMWPP_Models_Services_Email $emailService */
        $emailService = HMWPP_Classes_ObjController::getClass('HMWPP_Models_Services_Email');

        /** @var HMWPP_Models_Services_Tftotp $twoFactorService */
        $twoFactorService = HMWPP_Classes_ObjController::getClass('HMWPP_Models_Services_Tftotp');

        /** @var HMWPP_Models_Services_Codes $backupService */
        $backupService = HMWPP_Classes_ObjController::getClass('HMWPP_Models_Services_Codes');

        // If none of the services are active
        if( !$twoFactorService->isServiceActive( $user ) && !$emailService->isServiceActive( $user ) ){
            return;
        }

        if($emailService->isServiceActive( $user )){

            // Allow the provider to re-send email code.
            if ( true === $emailService->preAuthentication( $user ) ) {
                $login_nonce = $this->createLoginNonce( $user->ID );

                if ( empty($login_nonce) ) {
                    wp_die( esc_html__( 'Failed to create a login nonce.', 'hide-my-wp-pack' ) );
                }

                $this->loginHtml( $user, $login_nonce['key'], $_REQUEST['redirect_to'], '' );
                exit;
            }
        }

        //if 2FA is active for the current user
        if($twoFactorService->isServiceActive( $user )) {

            // If the form hasn't been submitted, just display the auth form.
            if (!('POST' === strtoupper($_SERVER['REQUEST_METHOD']))) {

                //create a login nonce
                $login_nonce = $this->createLoginNonce($user->ID);
                if (empty($login_nonce)) {
                    wp_die(esc_html__('Failed to create a login nonce.', 'hide-my-wp-pack'));
                }

                //show 2fa login form
                $this->loginHtml($user, $login_nonce['key'], $_REQUEST['redirect_to'], '');
                exit;
            }

        }

        // Rate limit 2FA authentication attempts.
        $this->checkUserAttemptsLimit($user);

        // Verify & Validate the 2FA process
        if ( ($backupService->isServiceActive($user) && $backupService->validateAuthentication($user)) ||
            ($twoFactorService->isServiceActive( $user ) && $twoFactorService->validateAuthentication($user)) ||
            ($emailService->isServiceActive( $user ) && $emailService->validateAuthentication($user)) ){

            // Delete login nonce on success
            $this->deleteLoginNonce($user->ID);

            // Delete fail logins and attepts
            HMWPP_Classes_Tools::deleteUserMeta(self::USER_FAILURES,  $user->ID);
            HMWPP_Classes_Tools::deleteUserMeta(self::USER_ATTEMPTS,  $user->ID);
            HMWPP_Classes_Tools::saveUserMeta(self::USER_SUCCESS, time(), $user->ID);

            // Check if remember me option is on
            $remember = $this->remember();

            // Add compatibility with other plugins
            remove_filter( 'send_auth_cookies', '__return_false', PHP_INT_MAX );
            wp_set_auth_cookie( $user->ID, $remember );

            // Add filter for success login
            do_action( 'hmwp_user_authenticated', $user );

            // Must be global because that's how login_header() uses it.
            global $interim_login;
            $interim_login = HMWPP_Classes_Tools::getValue('interim-login');

            if ( $interim_login ) {
                $customize_login = isset( $_REQUEST['customize-login'] );
                if ( $customize_login ) {
                    wp_enqueue_script( 'customize-base' );
                }
                $message       = '<p class="message">' . __( 'You have logged in successfully.', 'hide-my-wp-pack' ) . '</p>';
                $interim_login = 'success';
                login_header( '', $message );
                do_action( 'login_footer' );
                exit;
            }

            //redirect
            $redirect_to = apply_filters( 'login_redirect', $_REQUEST['redirect_to'], $_REQUEST['redirect_to'], $user );
            wp_redirect( $redirect_to );
            exit;

        }

        do_action('wp_login_failed', $user->user_login, new WP_Error('hmwp_invalid_attempt', __('ERROR: Invalid verification code.', 'hide-my-wp-pack')));

        // Store the last time a failed login occured.
        HMWPP_Classes_Tools::saveUserMeta(self::USER_FAILURES,  time(), $user->ID);

        // Store the number of failed login attempts.
        $attempts = HMWPP_Classes_Tools::getUserMeta(self::USER_ATTEMPTS, $user->ID);
        HMWPP_Classes_Tools::saveUserMeta(self::USER_ATTEMPTS, ((int)$attempts + 1), $user->ID);

        // Create login nonce
        $login_nonce = $this->createLoginNonce($user->ID);
        if ( empty($login_nonce) ) {
            wp_die(esc_html__('Failed to create a login nonce.', 'hide-my-wp-pack'));
        }

        // Show the login form with error
        $this->loginHtml($user, $login_nonce['key'], $_REQUEST['redirect_to'], esc_html__('ERROR: Invalid verification code.', 'hide-my-wp-pack'));
        exit;


    }

    /**
     * Check the fail attempt limits and notify the user
     *
     * @param WP_User $user The WP_User instance representing the currently logged-in user.
     *
     * @return void
     */
    public function checkUserAttemptsLimit( $user ){

        // Rate limit 2FA authentication attempts.
        if ( $this->rateLimitReached($user) ) {
            $time_delay = $this->getUserTimeDelay($user);
            $last_login = $this->getLastUserLoginFail($user);

            $error =  str_replace('{time}', human_time_diff($last_login + $time_delay), HMWPP_Classes_Tools::getOption('hmwp_2falogin_message'));

            // Trigger the login fail hook from WP
            do_action('wp_login_failed', $user->user_login, $error);

            // Create login nonce
            $login_nonce = $this->createLoginNonce($user->ID);
            if ( empty($login_nonce) ) {
                wp_die(esc_html__('Failed to create a login nonce.', 'hide-my-wp-pack'));
            }

            // Show the login form with error
            $this->loginHtml($user, $login_nonce['key'], $_REQUEST['redirect_to'], esc_html($error));
            exit;
        }

    }

    /**
     * Show previous fail attempts for the current user
     *
     * @param WP_User $user The WP_User instance representing the currently logged-in user.
     */
    public function showNotices( $user ) {
        $user_failures = $this->getLastUserLoginFail( $user );
        $user_attempts = HMWPP_Classes_Tools::getUserMeta(self::USER_ATTEMPTS, $user->ID);

        if ( $user_failures ) {
            echo '<div id="login_notice" class="message"><strong>';
            echo str_replace(array('{count}', '{time}'),array(number_format_i18n( $user_attempts ), human_time_diff( $user_failures, time() )), HMWPP_Classes_Tools::getOption('hmwp_2falogin_fail_message'));
            echo '</strong></div>';
        }
    }

    /**
     * Create the login nonce.
     *
     * @param int $user_id User ID.
     * @return array|false
     */
    private function createLoginNonce( $user_id ) {

        //create a nonce for this user login
        $login_nonce = array(
            'user_id'    => $user_id,
            'expiration' => time() + ( 10 * MINUTE_IN_SECONDS ),
            'key' => wp_hash( $user_id . wp_rand(11111,99999) . microtime(), 'nonce' ),
        );

        // Store the nonce hashed to avoid leaking it via database access.
        if ( $hashed_key = $this->hashLoginNonce( $login_nonce ) ) {
            $login_nonce_stored = array(
                'expiration' => $login_nonce['expiration'],
                'key'        => $hashed_key,
            );

            if ( HMWPP_Classes_Tools::saveUserMeta(self::USER_NONCE, $login_nonce_stored, $user_id) ) {
                return $login_nonce;
            }
        }

        return false;
    }

    /**
     * Verify the user nonce.
     *
     * @param int $user_id User ID of the user who logged in.
     * @param string $nonce Login nonce from user meta.
     * @return bool
     */
    public function verifyLoginNonce( $user_id, $nonce ) {

        //get the current nonce from DB
        $login_nonce = HMWPP_Classes_Tools::getUserMeta(self::USER_NONCE, $user_id);

        //check the integrity of the nonce
        if ( ! $login_nonce || empty( $login_nonce['key'] ) || empty( $login_nonce['expiration'] ) ) {
            return false;
        }

        $db_nonce = array(
            'user_id'    => $user_id,
            'expiration' => $login_nonce['expiration'],
            'key'        => $nonce,
        );

        //check if the current hash matched the DB user nonce
        $db_hash = $this->hashLoginNonce( $db_nonce );
        $hashes_match = hash_equals( $login_nonce['key'], $db_hash );

        //Check the nonce expiration
        if ( $hashes_match && time() < $login_nonce['expiration'] ) {
            return true;
        }

        // Require a fresh nonce if valid but the login fails.
        $this->deleteLoginNonce( $user_id );

        return false;
    }

    /**
     * Encode the login nonce for secure login
     *
     * @param array $nonce
     * @return false|string
     */
    private function hashLoginNonce( $nonce ) {
        $message = wp_json_encode( $nonce );

        if ( ! $message ) {
            return false;
        }

        return wp_hash( $message, 'nonce' );
    }

    /**
     * Delete the login nonce.
     *
     * @param int $user_id User ID.
     * @return bool
     */
    private function deleteLoginNonce( $user_id ) {
        return HMWPP_Classes_Tools::deleteUserMeta( self::USER_NONCE, $user_id );
    }

    /**
     * Save timestamp for the current logged user
     *
     * @param WP_User $user The WP_User instance representing the currently logged-in user.
     * @return WP_User|WP_Error
     */
    public function collectAuthLogin( $user ){

        if (!is_wp_error($user)) {
            HMWPP_Classes_Tools::saveUserMeta( self::USER_SUCCESS, time(), $user->ID );
        }

        return $user;
    }

    /**
     * Save all user cookies for later management
     *
     * @param string $cookie
     * @return void
     */
    public function collectAuthCookieTokens( $cookie ){
        if(function_exists('wp_parse_auth_cookie')){
            $parsed = wp_parse_auth_cookie( $cookie );

            if ( ! empty( $parsed['token'] ) ) {
                self::$tokens[] = $parsed['token'];
            }
        }
    }

    /**
     * Destroy the current cookies for the logged user
     *
     * @param WP_User $user The logged user
     * @return void
     */
    public function destroyCurrentSession( $user ) {
        if(class_exists('WP_Session_Tokens')){
            $session_manager = WP_Session_Tokens::get_instance( $user->ID );

            foreach ( self::$tokens as $auth_token ) {
                $session_manager->destroy( $auth_token );
            }
        }
    }

    /**
     * Check remember me option on login.
     *
     * @return boolean
     */
    private function remember() {
        $remember = false;

        if ( HMWPP_Classes_Tools::getValue('rememberme') ) {
            $remember = true;
        }

        return $remember;
    }

    /**
     * Get the timestamp of the last user login fail
     *
     * @param $user The User.
     *
     * @return int
     */
    public function getLastUserLoginFail( $user ) {
        return (int)HMWPP_Classes_Tools::getUserMeta(self::USER_FAILURES, $user->ID);
    }

    /**
     * Determine if a time delay between user two login attempts is reached.
     *
     * @param WP_User $user The User.
     * @return bool True if rate limit is okay, false if not.
     */
    public function rateLimitReached( $user ) {

        $rate_limit  = $this->getUserTimeDelay( $user );
        $last_failed = $this->getLastUserLoginFail( $user );

        $attempt_limit_reached = false;
        if ( $last_failed && ($last_failed + $rate_limit) > time() ) {
            $attempt_limit_reached = true;
        }

        /**
         * Filter whether this login attempt limit is reached.
         *
         * @param bool $attempt_limit_reached Whether the user login is rate limited.
         * @param WP_User $user The user attempting to login.
         */
        return apply_filters( 'hmwp_attempt_limit_reached', $attempt_limit_reached, $user );
    }

    /**
     * Determine the minimum wait between two login attempts for a user.
     *
     * @param WP_User $user The User.
     *
     * @return int Time delay in seconds between login attempts.
     */
    public function getUserTimeDelay( $user ) {

        /** @var int $rate_limit The number of seconds between two attempts. */
        $rate_limit = apply_filters( 'hmwp_min_attempt_seconds', 1 );

        //Number of fail attempts
        if ( $user_failed_logins = HMWPP_Classes_Tools::getUserMeta(self::USER_ATTEMPTS, $user->ID) ) {

            //Check if max attempts is reached
            if( $user_failed_logins >= HMWPP_Classes_Tools::getOption('hmwp_2falogin_max_attempts') ){
                /** @var int $rate_limit The maximum number of seconds a user might be locked out for. Default 60 minutes. */
                $rate_limit = HMWPP_Classes_Tools::getOption('hmwp_2falogin_max_timeout');
            }

        }

        /**
         * Filters the per-user time duration between two fail login attempts.
         *
         * @param int     $rate_limit The number of seconds between two attempts.
         * @param WP_User $user The user attempting to login.
         */
        return apply_filters( 'hmwp_user_attempt_seconds', $rate_limit, $user );
    }

    /**
     * Get the redable time elapsed string
     *
     * @param int $time
     * @param bool $ago
     *
     * @return string
     * @since 1.0.0
     *
     */
    public function timeElapsed( $time, $ago = true ) {

        if ( is_numeric( $time ) ) {

            if ( $ago ) {
                $etime = time() - $time;
            }

            if ( $etime < 1 ) {
                return gmdate(get_option('date_format') . ' ' . get_option('time_format'), $time);
            }

            $a = array(
                // 365 * 24 * 60 * 60 => 'year',
                // 30 * 24 * 60 * 60 => 'month',
                24 * 60 * 60 => 'day',
                60 * 60      => 'hour',
                60           => 'minute',
                1            => 'second',
            );

            $a_plural = array(
                'year'   => 'years',
                'month'  => 'months',
                'day'    => 'days',
                'hour'   => 'hours',
                'minute' => 'minutes',
                'second' => 'seconds',
            );

            foreach ( $a as $secs => $str ) {
                $d = $etime / $secs;

                if ( $d >= 1 ) {
                    $r = round( $d );

                    $time_string = ( $r > 1 ) ? $a_plural[ $str ] : $str;

                    if ( $ago ) {
                        return sprintf( esc_html__('%d %s ago', 'hide-my-wp-pack'), $r, $time_string );
                    } else {
                        return sprintf( esc_html__('%d %s remaining', 'hide-my-wp-pack'), $r, $time_string );
                    }
                }
            }

            return gmdate(get_option('date_format') . ' ' . get_option('time_format'), $time);
        }

    }

    /**
     * Shows an administrative notification when backup codes are depleted.
     *
     */
    public function adminNotices() {
        $user = wp_get_current_user();

        /** @var HMWPP_Models_Services_Email $emailService */
        $emailService = HMWPP_Classes_ObjController::getClass('HMWPP_Models_Services_Email');

        /** @var HMWPP_Models_Services_Tftotp $twoFactorService */
        $twoFactorService = HMWPP_Classes_ObjController::getClass('HMWPP_Models_Services_Tftotp');

        /** @var HMWPP_Models_Services_Codes $backupService */
        $backupService = HMWPP_Classes_ObjController::getClass('HMWPP_Models_Services_Codes');

        // If none of the services are active
        if( !$twoFactorService->isServiceActive( $user ) && !$emailService->isServiceActive( $user ) ){
            return;
        }

        // If the service is not active or there are still codes remained
        if ( !$backupService->isServiceActive( $user )) {

        ?>
        <div class="error">
            <p>
				<span>
					<?php
                    echo wp_kses(
                        sprintf(
                            __( '2FA Codes: You are out of backup codes and need <a href="%s">new codes!</a>', 'hide-my-wp-pack' ),
                            esc_url( get_edit_user_link( $user->ID ) . '#hmwp_two_factor_options' )
                        ),
                        array( 'a' => array( 'href' => true ) )
                    );
                    ?>
				<span>
            </p>
        </div>
        <?php
        }
    }


    /**
     * Sert the users table by LastLogin
     * @param $sortable_columns
     *
     * @return mixed
     */
    public function manageUsersColumnSort($sortable_columns) {
        return array_merge($sortable_columns, array(
            'hmwp_last_login' => self::USER_SUCCESS,
        ));
    }

    public function manageUsersColumnQuery($args) {

        if (isset($args['orderby'])) {
            if (is_string($args['orderby'])) {
                if ($args['orderby'] == self::USER_SUCCESS) {
                    $args['meta_key'] = self::USER_SUCCESS;
                    $args['orderby'] = 'meta_value';
                }
            }elseif (array_key_exists(self::USER_SUCCESS, $args['orderby'])) {
                $args['meta_key'] = self::USER_SUCCESS;
                $args['orderby']['meta_value'] = $args['orderby'][self::USER_SUCCESS];
                unset($args['orderby'][self::USER_SUCCESS]);
            }
        }
        return $args;
    }


    /**
     * Filter the columns on the Users admin screen.
     *
     * @param  array $columns Available columns.
     * @return array          Updated array of columns.
     */
    public static function manageUsersColumnHeader( array $columns ) {

        if(HMWPP_Classes_Tools::getOption('hmwp_2falogin_status')){
            $columns['hmwp_status'] = __( '2FA Settings', 'hide-my-wp-pack' );
            if( !HMWPP_Classes_Tools::isPluginActive('wordfence/wordfence.php') ){
                $columns['hmwp_last_login'] = esc_html__('Last Login', 'hide-my-wp-pack');
            }
        }

        return $columns;
    }

    /**
     * Output the 2FA column data on the Users screen.
     *
     * @param  string $output      The column output.
     * @param  string $column_name The column ID.
     * @param  int    $user_id     The user ID.
     * @return string              The column output.
     */
    public static function manageUsersColumn( $output, $column_name, $user_id ) {

        if ( 'hmwp_status' == $column_name ) {
            /** @var HMWPP_Models_Services_Tftotp $twoFactorService */
            $twoFactorService = HMWPP_Classes_ObjController::getClass('HMWPP_Models_Services_Tftotp');

            /** @var HMWPP_Models_Services_Email $emailService */
            $emailService = HMWPP_Classes_ObjController::getClass('HMWPP_Models_Services_Email');

            $user = get_userdata($user_id);

            if (!$twoFactorService->isServiceActive( $user ) && !$emailService->isServiceActive( $user )) {
                return sprintf('<span style="color: darkgrey">%s</span>', esc_html__('Disabled', 'hide-my-wp-pack'));
            } else {
                return sprintf('<span style="color: darkgreen;" class="dashicons-before dashicons-yes-alt"> %s</span>', esc_html__('Active', 'hide-my-wp-pack'));
            }
        }elseif ( 'hmwp_last_login' == $column_name ) {
            if($last_totp_login = HMWPP_Classes_Tools::getUserMeta( self::USER_SUCCESS, $user_id )) {
                return sprintf('<span>%s</span>', gmdate(get_option('date_format') . ' ' . get_option('time_format'), $last_totp_login));
            }else{
                return esc_html__('Not yet logged in', 'hide-my-wp-pack');
            }
        }

        return $output;
    }

    /**
     * Delete all 2FA logins
     *
     * @since 1.0.0
     */
    public function deleteTwoFactorLogins() {
        global $wpdb;

        $transient = '_hmwp_totp_%';
        $sql    = "DELETE FROM $wpdb->usermeta WHERE `meta_key` LIKE '%s'";
        $wpdb->query( $wpdb->prepare( $sql, $transient ) );

        $transient = '_hmwp_email_%';
        $sql    = "DELETE FROM $wpdb->usermeta WHERE `meta_key` LIKE '%s'";
        $wpdb->query( $wpdb->prepare( $sql, $transient ) );

        $transient = '_hmwp_backup_%';
        $sql    = "DELETE FROM $wpdb->usermeta WHERE `meta_key` LIKE '%s'";
        $wpdb->query( $wpdb->prepare( $sql, $transient ) );

    }

}
