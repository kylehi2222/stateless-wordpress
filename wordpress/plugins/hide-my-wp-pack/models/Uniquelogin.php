<?php
/**
 * Unique Login Model
 *
 * @file  The Unique Login file
 * @package HMWPP/Unique Login
 * @since 1.3.0
 */
defined('ABSPATH') || die('Cheatin\' uh?');

class HMWPP_Models_Uniquelogin
{

    /** @var array Define all session expire options */
    public $expires;

    /** @var string login success */
    const USER_NONCE = '_hmwp_nonce';
    const USER_TOKEN_PARAM = 'hmwpml_token';
    /** @var string the user session expire timestamp  */
    const USER_SESSION_EXPIRE = '_hmwpml_expire';
    const USER_SESSION_CREATED = '_hmwpml_created';
    const USER_TOKEN_KEY = '_hmwpml_token';
    const USER_LAST_LOGIN = '_hmwpml_last_login';
    const USER_REDIRECT = '_hmwpml_redirect_to';

    public function __construct()
    {
        $this->expires = array(
            'hour'  => array( 'label' => esc_html__( 'One Hour', 'hide-my-wp-pack' ), 'timestamp' => HOUR_IN_SECONDS ),
            '3_hours' => array( 'label' => esc_html__( 'Three Hours', 'hide-my-wp-pack' ), 'timestamp' => HOUR_IN_SECONDS * 3 ),
            'day' => array( 'label' => esc_html__( 'One Day', 'hide-my-wp-pack' ), 'timestamp' => DAY_IN_SECONDS ),
            '3_days' => array( 'label' => esc_html__( 'Three Days', 'hide-my-wp-pack' ), 'timestamp' => DAY_IN_SECONDS * 3 ),
            'week' => array( 'label' => esc_html__( 'One Week', 'hide-my-wp-pack' ), 'timestamp' => WEEK_IN_SECONDS ),
            'month' => array( 'label' => esc_html__( 'One Month', 'hide-my-wp-pack' ), 'timestamp' => MONTH_IN_SECONDS ),
            'halfyear' => array( 'label' => esc_html__( 'Six Months', 'hide-my-wp-pack' ), 'timestamp' => (6* MONTH_IN_SECONDS) ),
            'year' => array( 'label' => esc_html__( 'One Year', 'hide-my-wp-pack' ), 'timestamp' => YEAR_IN_SECONDS ),
        );
    }

    /**
     * Get valid temporary user based on token
     *
     * @param string $token
     *
     * @return array|bool
     * @since 1.3.0
     *
     */
    public function findUserByToken( $token = '') {

        if ( empty( $token ) ) {
            return false;
        }

        $args = array(
            'fields'     => 'all',
            'meta_key'   => self::USER_SESSION_EXPIRE,
            'order'      => 'DESC',
            'orderby'    => 'meta_value',
            'meta_query' => array(
                0 => array(
                    'key'     => self::USER_TOKEN_KEY,
                    'value'   => sanitize_text_field( $token ),
                    'compare' => '=',
                ),
            ),
        );

        $users = new WP_User_Query( $args );

        $users = $users->get_results();
        if ( empty( $users ) ) {
            return false;
        }

        foreach ( $users as $user ) {
            //check if the link is expired
            if($this->isExpired($user->ID)){
                return false;
            }

            //get user details
            $user->details = $this->getUserDetails($user);
            return $user;
        }

        return false;

    }

    /**
     * Get user temp login details
     *
     * @param $user
     * @return mixed
     *
     * @since 1.0.0
     */
    public function getUserDetails($user){
        $details = array();

        $details['redirect_to']  = HMWPP_Classes_Tools::getUserMeta(self::USER_REDIRECT, $user->ID);
        $details['expire']  = HMWPP_Classes_Tools::getUserMeta(self::USER_SESSION_EXPIRE, $user->ID);

        $details['last_login_time'] = HMWPP_Classes_Tools::getUserMeta(self::USER_LAST_LOGIN, $user->ID);
        $details['last_login'] = esc_html__( 'Not yet logged in', 'hide-my-wp-pack' );
        if ( ! empty( $details['last_login_time'] ) ) {
            $details['last_login'] = $this->timeElapsed( $details['last_login_time'], true );
        }

        return json_decode(json_encode($details));
    }

    /**
     * Create a Unique login
     *
     * @return array
     *
     * @since 1.3.0
     */
    public function createUniqueLogin( $data ) {

        $result = array(
            'error' => true
        );

        $expire = ! empty( $data['expire'] ) ? $data['expire'] : HMWPP_Classes_Tools::getOption('hmwp_uniquelogin_expires');
        $email       = isset( $data['user_email'] ) ? sanitize_email( $data['user_email'] ) : '';
        $redirect_to = ! empty( $data['redirect_to'] ) ? sanitize_text_field( $data['redirect_to'] ) : '';

        if(empty($data['user_email'])) {
            $result['errcode'] = 'invalid_user';
            $result['message'] = esc_html__('Empty email address.','hide-my-wp-pack');
        }elseif ( ! is_email( $data['user_email'] ) ) {
            $result['errcode'] = 'invalid_user';
            $result['message'] = esc_html__('Invalid email address.','hide-my-wp-pack');
        }elseif($email <> '' && !email_exists($email)){
            $result['errcode'] = 'invalid_user';
            $result['message'] = esc_html__('User does not exist.','hide-my-wp-pack');
        }else{
            $user = get_user_by('email', $email );

            if ( is_wp_error( $user ) ) {
                $code = $user->get_error_code();

                $result['errcode'] = $code;
                $result['message'] = $user->get_error_message( $code );

            } else {

                $user_id = $user->ID;

                if(HMWPP_Classes_ObjController::getClass('HMWPP_Models_Templogin')->isValidTempLogin($user_id)){
                    $result['errcode'] = 'invalid_user';
                    $result['message'] = esc_html__('This user is a temporary user.','hide-my-wp-pack');
                    return $result;
                }

                HMWPP_Classes_Tools::saveUserMeta(self::USER_SESSION_CREATED, $this->gtmTimestamp(), $user_id);
                HMWPP_Classes_Tools::saveUserMeta(self::USER_SESSION_EXPIRE, $expire, $user_id);
                HMWPP_Classes_Tools::saveUserMeta(self::USER_TOKEN_KEY, $this->generateToken( $user_id ), $user_id);
                HMWPP_Classes_Tools::saveUserMeta(self::USER_REDIRECT, $redirect_to, $user_id);

                $result['error']   = false;
                $result['user_id'] = $user_id;
            }
        }

        return $result;

    }

    /**
     * Save the user last login time for the activity log
     * @param $user
     *
     * @return void
     */
    public function saveUserLastLogin( $user ){
        HMWPP_Classes_Tools::saveUserMeta(self::USER_LAST_LOGIN, $this->gtmTimestamp(), $user->ID);
    }

    /**
     * Get current GMT date time
     *
     * @return false|int
     * @since 1.0.0
     *
     */
    public function gtmTimestamp() {
        return strtotime( gmdate( 'Y-m-d H:i:s', time() ) );
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
    public function timeElapsed( $time, $ago = false ) {

        if ( is_numeric( $time ) ) {

            if ( $ago ) {
                $etime = $this->gtmTimestamp() - $time;
            } else {
                $etime = $time - $this->gtmTimestamp();
            }

            if ( $etime < 1 ) {
                return esc_html__( 'Expired', 'hide-my-wp-pack' );
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

            return __( 'Expired', 'hide-my-wp-pack' );
        } else {

            return ! empty( $expiry_options[ $time ] ) ? $this->expires[ $time ]['label'] : '';
        }

    }

    /**
     * Check if unique login expired
     *
     * @param int $user_id
     *
     * @return bool
     *
     * @since 1.0.0
     */
    public function isExpired( $user_id = 0 ) {

        if ( empty( $user_id ) ) {
            $user_id = get_current_user_id();
        }

        if ( empty( $user_id ) ) {
            return false;
        }

        $expire = HMWPP_Classes_Tools::getUserMeta(self::USER_SESSION_EXPIRE, $user_id);

        return ! empty( $expire ) && is_numeric( $expire ) && $this->gtmTimestamp() >= floatval( $expire ) ? true : false;

    }

    /**
     * Set the current unique login as expired
     *
     * @param int $user_id
     *
     * @return bool
     *
     * @since 1.0.0
     */
    public function setExpired( $user_id = 0 ) {

        if ( empty( $user_id ) ) {
            $user_id = get_current_user_id();
        }

        if ( empty( $user_id ) ) {
            return false;
        }

        HMWPP_Classes_Tools::saveUserMeta(self::USER_SESSION_EXPIRE, time() - 3600 * 24 , $user_id);

        return true;

    }

    /**
     * Get unique login url
     *
     * @param $user_id
     *
     * @return string
     * @since 1.0.0
     *
     */
    public function getUniqueLoginUrl( $user_id ) {

        if ( empty( $user_id ) ) {
            return '';
        }

        $token = HMWPP_Classes_Tools::getUserMeta(self::USER_TOKEN_KEY, $user_id);
        if ( empty( $token ) ) {
            return '';
        }

        $login_url = add_query_arg( self::USER_TOKEN_PARAM, $token, trailingslashit( home_url() ) );

        // Make it compatible with iThemes Security plugin with Custom URL Login enabled
        $login_url = apply_filters( 'itsec_notify_admin_page_url', $login_url );

        return apply_filters( 'hmwp_unique_login_link', $login_url, $user_id );

    }

    /**
     * Generate and email the user unique login.
     *
     * @param WP_User $user The WP_User instance representing the currently logged-in user.
     * @param string $url The URL of the unique login
     * @return bool Whether the email contents were sent successfully.
     */
    public function sendLoginUrl( $user, $url ) {

        $subject = wp_strip_all_tags( sprintf( __( "[%s] Your Magic Login URL", 'hide-my-wp-pack' ), wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ) ) );
        $message = wp_strip_all_tags( sprintf( __( "Click on this magic link %s to log in to your account.", 'hide-my-wp-pack' ), PHP_EOL . PHP_EOL . $url . PHP_EOL . PHP_EOL) );

        $subject = apply_filters( 'hmwp_unique_login_subject', $subject, $user->ID );
        $message = apply_filters( 'hmwp_unique_login_message', $message, $url, $user->ID );

        return wp_mail( $user->user_email, $subject, $message );

    }

    /**
     * Generate unique Login Token
     *
     * @param $user_id
     *
     * @return false|string
     *
     * @since 1.0.0
     */
    public function generateToken( $user_id ) {
        $byte_length = 32;

        if ( function_exists( 'random_bytes' ) ) {
            try {
                return bin2hex( random_bytes( $byte_length ) ); // phpcs:ignore
            } catch ( \Exception $e ) {
            }
        }

        if ( function_exists( 'openssl_random_pseudo_bytes' ) ) {
            $crypto_strong = false;

            $bytes = openssl_random_pseudo_bytes( $byte_length, $crypto_strong );
            if ( true === $crypto_strong ) {
                return bin2hex( $bytes );
            }
        }

        // Fallback
        $str  = $user_id . microtime() . uniqid( '', true );
        $salt = substr( md5( $str ), 0, 32 );

        return hash( "sha256", $str . $salt );
    }


    /**
     * Show Notice on the login page
     *
     * @param $message
     * @param $error
     *
     * @return void
     */
    public function showNotices( $message, $error = false) {

        if ( ! function_exists( 'login_header' ) ) {
            // We really should migrate login_header() out of `wp-login.php` so it can be called from an includes file.
            include_once _HMWPP_THEME_DIR_ . 'login/header.php';
        }

        login_header();

        //Show errors on top
        ?>
        <div id="login_notice" class="message <?php echo ($error ? 'notice-error' : '') ?>"><strong><?php echo $message?></strong></div>
        <?php

        if ( ! function_exists( 'login_footer' ) ) {
            include_once _HMWPP_THEME_DIR_ . 'login/footer.php';
        }

        login_footer();
        exit();
    }

    /**
     * Generates the html form for the second step of the authentication process.
     *
     * @param string        $error_msg Optional. Login error message.
     */
    public function loginHtml( $error_msg = '' ) {

        $redirect_to = HMWPP_Classes_Tools::getValue('redirect_to', admin_url());

        if($page_name = $this->isWooCommerceLoginPage()){
            $redirect_to = wp_validate_redirect( apply_filters( 'woocommerce_login_redirect', $page_name ), admin_url()) ;
        }

        if(HMWPP_Classes_Tools::getValue('interim-login')){
            return;
        }

        if ( ! empty( $error_msg ) ) {
            echo '<div id="login_error"><strong>' . esc_html( $error_msg ) . '</strong><br /></div>';
        }
        ?>
        <div id="unique_login_wrap" >
            <div id="unique_login_separator">
                <hr>
                <span>OR</span>
            </div>
            <div id="unique_login">
                <input type="button" name="unique_login_button" id="unique_login_button" value="<?php echo esc_html__('Login using a magic link', 'hide-my-wp-pack') ?>">
            </div>
        </div>
        <div id="unique_login_form">
            <p style="font-size: 1rem; font-weight: 600; margin: 10px 0;">
                <?php echo esc_html__( 'Login without password:', 'hide-my-wp-pack'); ?>
            </p>
            <p>
                <input type="hidden" name="action" value="validate_magic_link" />
                <?php wp_nonce_field( 'validate_magic_link', self::USER_NONCE ); ?>
                <label for="user_email"><?php _e( 'Email Address' ); ?></label>
                <input type="text" name="user_email" id="user_email" class="input" value="<?php echo HMWPP_Classes_Tools::getValue('log'); ?>" size="20" autocapitalize="off" autocomplete="username" required="required" />
                <input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect_to ); ?>" />
            </p>
            <p class="submit">
                <input type="submit" id="unique_login_submit" value="<?php echo esc_html__( 'Send', 'hide-my-wp-pack'); ?>" />
                <input type="button" id="unique_login_cancel" value="< <?php echo esc_html__( 'Back', 'hide-my-wp-pack'); ?>" />
            </p>
        </div>
        <style>
            form #unique_login_wrap {
                display: none;
            }
            form #unique_login_wrap #unique_login_separator {
                position: relative;
                margin-bottom: 1.5rem;
                margin-top: 1.5rem;
            }
            form #unique_login_wrap #unique_login_separator hr{
                border-width: 0;
                border-top: 1px solid #cbd5e1;
                color: #fff;
                height: 0;
            }
            form #unique_login_wrap #unique_login_separator span{
                position: absolute;
                font-size: .9rem;
                color: #cbd5e1;
                padding-right: .5rem;
                padding-left: .5rem;
                background-color: #ffffff;
                display: inline-block;
                top: -10px;
                left: 42%;
            }
            form #unique_login_wrap #unique_login {
                margin: 5px 0 20px;
                clear: both;
            }
            form #unique_login_wrap #unique_login input#unique_login_button,
            #unique_login_form input#unique_login_submit,
            #unique_login_form input#unique_login_cancel{
                min-height: 32px;
                line-height: 2.30769231;
                padding: 0 15px;
                color: #2271b1;
                border-color: #404040;
                background: transparent;
                vertical-align: top;
                display: inline-block;
                text-decoration: none;
                font-size: 13px;
                margin: 0;
                cursor: pointer;
                border-width: 1px;
                border-style: solid;
                -webkit-appearance: none;
                border-radius: 3px;
                white-space: nowrap;
                box-sizing: border-box;
            }
            form #unique_login_wrap #unique_login input#unique_login_button{
                width: 100%;
            }
            #unique_login_form input#unique_login_submit{
                float: right;
            }
            #unique_login_form input#unique_login_cancel{
                border-width: 0px;
            }
            #unique_login_form{
                display: none;
            }
            #unique_login_form #user_email{
                font-size: 24px;
                line-height: 1.33333333;
                width: 100%;
                border-width: 0.0625rem;
                padding: 0.1875rem 0.3125rem;
                margin: 0 6px 16px 0;
                min-height: 40px;
                max-height: none;
            }
        </style>
        <script>
            (function() {
                const unique_login_form = document.querySelector( '#unique_login_form' ),
                    wrap = document.querySelector( '#unique_login_wrap' ),
                    button = document.querySelector( '#unique_login_button' );

                var login_form = document.querySelector( '#loginform' );
                if (document.querySelector( '.woocommerce-form' ) !== null){
                    login_form = document.querySelector( '.woocommerce-form' );
                }

                if (login_form !== null){
                    wrap.style.display = 'block';

                    login_form.parentElement.insertBefore(unique_login_form, login_form);
                    unique_login_form.innerHTML = '<form method="post">'+unique_login_form.innerHTML+'</form>';

                    button.addEventListener("click", function() {
                        login_form.style.display = 'none';
                        unique_login_form.style.display = 'block';

                        document.querySelector( '#unique_login_cancel' ).addEventListener("click", function() {
                            unique_login_form.style.display = 'none';
                            login_form.style.display = 'block';
                        });
                    });
                }
            })();
        </script>
        <?php
    }

    /**
     * Generate the two-factor login form URL.
     *
     * @param  array  $params List of query argument pairs to add to the URL.
     * @param  string $scheme URL scheme context.
     *
     * @return string
     */
    public function loginUrl( $params = array(), $scheme = 'login' ) {
        if ( ! is_array( $params ) ) {
            $params = array();
        }

        $params = urlencode_deep( $params );

        if($myaccount = $this->isWooCommerceLoginPage()){
            return add_query_arg( $params, site_url($myaccount, $scheme ) );
        }

        return add_query_arg( $params, site_url( 'wp-login.php', $scheme ) );
    }

    /**
     * Check if the current page is an woocommerce account page
     *
     * @return false|string return the woocommerce account page
     */
    public function isWooCommerceLoginPage() {

        if(HMWPP_Classes_Tools::isPluginActive('woocommerce/woocommerce.php')){
            global $wp;
            if( isset($wp->request) && $post_id = get_option('woocommerce_myaccount_page_id')){
                if($post = get_post($post_id)) {
                    if(basename($wp->request) == $post->post_name){
                        return $post->post_name;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Set attempt as brute force
     *
     * @return void
     * @throws Exception
     */
    public function setFailAttempt() {
        if (HMWP_Classes_Tools::getOption('hmwp_bruteforce')) {
            HMWP_Classes_ObjController::getClass('HMWP_Models_Brute')->brute_call('failed_attempt');
        }
    }

    /**
     * Send the log with the magic link
     *
     * @param $action
     *
     * @return void
     * @throws Exception
     */
    public function sendToLog($action) {
        if (HMWP_Classes_Tools::getOption('hmwp_activity_log')) {

            $values = array(
                 'referer' => 'magic_link',
            );

            HMWP_Classes_ObjController::getClass('HMWP_Models_Log')->hmwp_log_actions($action, $values);
        }
    }
}
