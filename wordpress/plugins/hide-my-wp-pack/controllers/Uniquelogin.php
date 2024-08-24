<?php
/**
 * Unique Login Class
 * Called on Unique Logins
 *
 * @file The Unique Logins file
 * @package HMWPP/Templogin
 * @since 1.3.0
 */

defined('ABSPATH') || die('Cheatin\' uh?');

class HMWPP_Controllers_Uniquelogin extends HMWPP_Classes_FrontController
{

    public function __construct() {
        parent::__construct();

        //If safe parameter is set, clear the banned IPs and let the default paths
        if (HMWPP_Classes_Tools::getIsset(HMWPP_Classes_Tools::getOption('hmwp_disable_name')) ) {
            if (HMWPP_Classes_Tools::getValue(HMWPP_Classes_Tools::getOption('hmwp_disable_name')) == HMWPP_Classes_Tools::getOption('hmwp_disable') ) {
                return;
            }
        }

        //Add unique login option if validation failed
        add_filter('authenticate', array( $this, 'hookLogin' ), 1);

        //Add login & validation hooks
        add_action( 'login_form', array( $this, 'hookLoginForm' ), 100 );

        //add support for WooCommerce
        if(HMWPP_Classes_Tools::getOption('hmwp_uniquelogin_woocommerce')){
            add_filter( 'woocommerce_before_customer_login_form', array( $this, 'hookWoocommerceLogin' ), 100);
            add_action( 'woocommerce_login_form', array( $this, 'hookLoginForm' ), 100 );
        }

    }

    /**
     * Hook admin init
     *
     * @return void
     */
    public function hookInit() {

        add_filter('user_row_actions', array($this, 'userRowAction'), 10, 2);

        if(HMWPP_Classes_Tools::isMultisites()){
            add_filter('ms_user_row_actions', array($this, 'userRowAction'), 10, 2);
        }

    }

    /**
     * @param string $actions Current user row action
     * @param WP_User $user
     *
     * @return mixed
     */
    public function userRowAction($actions, $user) {

        if (HMWPP_Classes_Tools::userCan('hmwp_manage_settings')) {
            $url = add_query_arg(array('hmwp_nonce' => wp_create_nonce('hmwpp_uniquelogin_new'), 'action' => 'hmwpp_uniquelogin_new', 'user_email' => $user->user_email));
            $actions['uniquelogin'] = '<a href="' . esc_url($url) . '" aria-label="' . esc_attr(sprintf(__('Send magic login url to %s', 'hide-my-wp-pack'), $user->user_email)) . '">' . esc_html__('Send magic login link', 'hide-my-wp-pack') . '</a>';
        }

        return $actions;
    }

    /**
     * Hook the init in Frontend
     * @return void
     */
    public function hookFrontinit() {

        //If there is a unique login request
        if ( HMWPP_Classes_Tools::getValue($this->model::USER_TOKEN_PARAM) <> '') {

            //initialize the redirect
            $redirect_to = add_query_arg('hmwp_login', 'success', admin_url());
            add_filter('hmwp_option_hmwp_hide_wplogin', '__return_false');
            add_filter('hmwp_option_hmwp_hide_login', '__return_false');

            //check if token is set
            $token = sanitize_key( HMWPP_Classes_Tools::getValue($this->model::USER_TOKEN_PARAM) );

            if ( !$user = $this->model->findUserByToken( $token ) ) {

                $redirect_to = home_url(); //redirect to home page

            }else{

                $do_login = true;
                if ( function_exists('is_user_logged_in') && is_user_logged_in() ) {
                    if ( $user->ID !== get_current_user_id() ) {
                        wp_logout();
                    } else {
                        $do_login = false;
                    }
                }

                if ( $do_login ) {

                    //remove other filters on authenticate
                    remove_all_filters( 'authenticate' );
                    remove_all_actions( 'wp_login_failed' );

                    //disable brute force reCaptcha on temporary login
                    add_filter('hmwp_option_brute_use_math', '__return_false');
                    add_filter('hmwp_option_brute_use_captcha', '__return_false');
                    add_filter('hmwp_option_brute_use_captcha_v3', '__return_false');

                    //login process
                    wp_clear_auth_cookie();
                    if(! wp_set_current_user( $user->ID, $user->login )){
                        wp_die(
                            esc_html__('Could not login with this user.', 'hide-my-wp'),
                            esc_html__('Temporary Login', 'hide-my-wp'),
                            array('response' => 403)
                        );
                    }
                    wp_set_auth_cookie( $user->ID, true );

                    //set expired when logged in as it's a unique login
                    $this->model->setExpired($user->ID);

                    //save login log
                    $this->model->sendToLog('login');

                    //save the current login time
                    $this->model->saveUserLastLogin( $user );

                    //trigger wp_login from WordPress
                    do_action( 'wp_login', $user->login, $user );

                    if($user->details->redirect_to <> ''){
                        $redirect_to = $user->details->redirect_to;
                    }
                }

            }

            wp_safe_redirect( $redirect_to ); // Redirect to given url after successful login.
            exit();
        }

    }

    /**
     * Handle the browser-based login.
     *
     */
    public function hookLoginForm(  ) {
        $this->model->loginHtml();
    }

    /**
     * Add compatibility with Woocommerce
     * @return void
     */
    public function hookWoocommerceLogin() {

        $user = $this->hookLogin( false );

        if($user){
            if(is_wp_error($user) && function_exists('wc_add_notice')){
                wc_add_notice($user->get_error_message(), 'error');
                wc_print_notices();
            }
        }

    }

    /**
     * @param WP_User $user The WP_User instance representing the currently logged-in user.
     *
     * @return false|mixed|WP_User
     */
    public function hookLogin( $user ) {

        if(!$_SERVER['REQUEST_METHOD'] == 'POST' || !isset($_POST['user_email'])){
            return $user;
        }

        //validate nonce
        if(!wp_verify_nonce(wp_unslash( HMWPP_Classes_Tools::getValue($this->model::USER_NONCE) ), 'validate_magic_link')){
            return $user;
        }
;

        $user_email = sanitize_text_field($_POST['user_email']);
        $action = HMWPP_Classes_Tools::getValue('action');

        //If the call is valid
        if($user_email <> '' && $action == 'validate_magic_link'){

            remove_all_filters( 'authenticate' );
            remove_all_actions( 'wp_login_failed' );

            add_filter('hmwp_option_brute_use_math', '__return_false');
            add_filter('hmwp_option_brute_use_captcha', '__return_false');
            add_filter('hmwp_option_brute_use_captcha_v3', '__return_false');

            $found_user = false;

            //Sanitize and search if user exists
            if($email = sanitize_email( $user_email )){
                $found_user = get_user_by( 'email', $email );
            }
            if(!$found_user && $login = sanitize_user( wp_unslash( $user_email ) )){
                $found_user      = get_user_by( 'login', $login );
            }

            //if user exists
            if ( $found_user ) {

                // Also clear the cookies which are no longer valid.
                if (function_exists('wp_clear_auth_cookie')) {
                    wp_clear_auth_cookie();
                }

                $data = array(
                    'user_email' => $found_user->user_email,
                    'redirect_to' => HMWPP_Classes_Tools::getValue('redirect_to'),
                );

                //create a unique link for the current user
                $login = $this->model->createUniqueLogin( $data );

                if ( $login['error'] ) {

                    //if user was not found or incorrect data
                    $user = new WP_Error(
                        'authentication_failed',
                        sprintf($login['message'], '<strong>', '</strong>')
                    );

                    //trigger fail attempt
                    $this->model->setFailAttempt();
                }else{

                    //Get the unique login URL
                    $unique_login_url = $this->model->getUniqueLoginUrl($found_user->ID);

                    //Send the login by email
                    if($this->model->sendLoginUrl($found_user, $unique_login_url)){

                        //email sent with success
                        $success = esc_html__('Please verify your email and click on the magic login URL provided in the email.','hide-my-wp-pack');
                        if(function_exists('wc_add_notice') && $this->model->isWooCommerceLoginPage()){
                            //show notification in woocommerce
                            wc_add_notice($success);
                            wc_print_notices();
                        }else{
                            //show notification and stop the authentication process
                            $this->model->showNotices($success);
                        }
                    }else{
                        //if there is a problem sending the email
                        $user = new WP_Error(
                            'authentication_failed',
                            sprintf(esc_html__('The server was unable to send the email.','hide-my-wp-pack'), '<strong>', '</strong>')
                        );
                    }
                }


            }else{
                //If the user was not found
                $user = new WP_Error(
                    'authentication_failed',
                    sprintf(esc_html__('User does not exists.','hide-my-wp-pack'), '<strong>', '</strong>')
                );

                //trigger fail attempt
                $this->model->setFailAttempt();
            }

        }

        return $user;

    }

    /**
     * Admin actions
     */
    public function action()
    {
        parent::action();

        switch ( HMWPP_Classes_Tools::getValue('action') ) {
            case 'hmwpp_uniquelogin_settings':

                //if current user can't manage settings
                if (!HMWPP_Classes_Tools::userCan('hmwp_manage_settings') ) {
                    return;
                }

                HMWPP_Classes_Tools::saveOptions('hmwp_uniquelogin', HMWPP_Classes_Tools::getValue('hmwp_uniquelogin', 0));
                HMWPP_Classes_Tools::saveOptions('hmwp_uniquelogin_expires', HMWPP_Classes_Tools::getValue('hmwp_uniquelogin_expires', 'hour'));

                break;

            case 'hmwpp_uniquelogin_new':
                $user_email = HMWPP_Classes_Tools::getValue('user_email', false);

                $return = $this->doUniqueLogin(array('user_email' => $user_email));

                if(!is_wp_error($return)){
                    add_action( 'admin_notices', function(){
                        ?>
                        <div class="notice notice-success is-dismissible">
                            <p><?php echo esc_html__('Magic login link sent!','hide-my-wp-pack'); ?></p>
                        </div>
                        <?php
                    } );
                }else{
                    add_action( 'admin_notices', function() use ($return){
                        ?>
                        <div class="notice notice-error is-dismissible">
                            <p><?php echo $return->get_error_message(); ?></p>
                        </div>
                        <?php
                    } );
                }



                break;

        }
    }

    /**
     * Generate a unique login URL and send it by email
     *
     * @param array $data 'user_email', 'expire', 'redirect_to'
     *
     * @return true|WP_Error
     */
    public function doUniqueLogin($data) {

        //create a unique link for the current user
        $login = $this->model->createUniqueLogin( $data );

        if(!$login['error'] && $user = get_user_by('email', $data['user_email'])) {

            //Get the unique login URL
            $unique_login_url = $this->model->getUniqueLoginUrl($user->ID);

            //Send the login by email
            if(!$this->model->sendLoginUrl($user, $unique_login_url)){
                //if there is a problem sending the email
                return new WP_Error(
                    'authentication_failed',
                    sprintf(esc_html__('The server was unable to send the email.','hide-my-wp-pack'), '<strong>', '</strong>')
                );
            }
        }else{
            return new WP_Error(
                'authentication_failed',
                sprintf($login['message'], '<strong>', '</strong>')
            );
        }

        return true;
    }

}
