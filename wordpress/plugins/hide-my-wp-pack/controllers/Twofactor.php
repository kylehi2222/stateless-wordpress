<?php
/**
 * @package HMWPP/Twofactor
 * @since 1.0.0
 */

use WordfenceLS\Controller_CAPTCHA;
use WordfenceLS\Controller_Permissions;
use WordfenceLS\Controller_Settings;

defined('ABSPATH') || die('Cheatin\' uh?');

class HMWPP_Controllers_Twofactor extends HMWPP_Classes_FrontController
{
    /** @var array Two Factor Options */
    public $options = array();
    /** @var array Backup Codes */
    public $codes = array();
    public $downloadLinks;

    public function __construct() {
        parent::__construct();

        //save the last login user
        add_filter('authenticate', array( $this->model, 'collectAuthLogin' ), PHP_INT_MAX, 1);

        //If safe parameter is set, clear the banned IPs and let the default paths
        if (HMWPP_Classes_Tools::getIsset(HMWPP_Classes_Tools::getOption('hmwp_disable_name')) ) {
            if (HMWPP_Classes_Tools::getValue(HMWPP_Classes_Tools::getOption('hmwp_disable_name')) == HMWPP_Classes_Tools::getOption('hmwp_disable') ) {
                return;
            }
        }

        //Add login & validation hooks
        add_action( 'wp_login', array( $this, 'hookLogin' ), 10, 2 );
        add_action( 'set_auth_cookie', array( $this->model, 'collectAuthCookieTokens' ) );
        add_action( 'set_logged_in_cookie', array( $this->model, 'collectAuthCookieTokens' ) );
        add_action( 'init', array( $this->model, 'validateTwoFactor' ) );

        //user list
        add_filter( 'manage_users_columns', array( $this->model, 'manageUsersColumnHeader' ) );
        add_filter( 'wpmu_users_columns', array( $this->model, 'manageUsersColumnHeader' ) );
        add_filter( 'manage_users_custom_column', array( $this->model, 'manageUsersColumn' ), 10, 3 );
        add_filter( 'users_list_table_query_args', array( $this->model, 'manageUsersColumnQuery' ));
        add_filter( 'manage_users_sortable_columns', array( $this->model, 'manageUsersColumnSort' ));

        if (is_multisite()) {
            add_filter('manage_users-network_sortable_columns', array( $this->model, 'manageUsersColumnSort' ));
        }

        //admin dashboard hooks
        add_action( 'admin_notices', array( $this->model, 'adminNotices' ) );

    }

    /**
     * Load 2FA settings in the user profile
     *
     * @param $user
     * @return void
     * @throws Exception
     */
    public function hookUserSettings( $user ){

        HMWPP_Classes_ObjController::getClass('HMWPP_Classes_DisplayController')->loadMedia('twofactor');
        HMWPP_Classes_ObjController::getClass('HMWPP_Classes_DisplayController')->loadMedia('qrcode');

        //add 2FA with Code Scan in user settings View
        add_action('hmwp_two_factor_user_options', function () use ($user){

            if( ! HMWPP_Classes_Tools::getOption('hmwp_2fa_totp') ){
                return false;
            }

            /** @var HMWPP_Models_Services_Tftotp $twoFactorService */
            $twoFactorService = HMWPP_Classes_ObjController::getClass('HMWPP_Models_Services_Tftotp');

            $this->options = $twoFactorService->getTwoFactorOption( $user );

            //Show the two factor block
            $this->show('blocks/Totp');
        });

        //add 2FA with Email Code in user settings View
        add_action('hmwp_two_factor_user_options', function () use ($user){

            if( ! HMWPP_Classes_Tools::getOption('hmwp_2fa_email') ){
                return false;
            }

            /** @var HMWPP_Models_Services_Email $emailService */
            $emailService = HMWPP_Classes_ObjController::getClass('HMWPP_Models_Services_Email');

            $this->options = $emailService->getEmailOption( $user );

            //Show the two factor block
            $this->show('blocks/Email');
        });

        //Show 2FA in user settings profile
        $this->show('TwofactorUser');

		do_action( 'hmwp_user_security_settings_after', $user );
    }

    /**
     * Handle the browser-based login.
     *
     * @param string  $user_login Username.
     * @param WP_User $user The WP_User instance representing the currently logged-in user.
     */
    public function hookLogin( $user_login, $user ) {

        if(isset($_SERVER['REMOTE_ADDR'])){
            $ip = $_SERVER['REMOTE_ADDR'];

            if(HMWPP_Classes_Tools::isWhitelistedIP($ip)){
                return;
            }
        }

        if ( ! $user ) {
            $user = wp_get_current_user();
        }

        /** @var HMWPP_Models_Services_Tftotp $twoFactorService */
        $twoFactorService = HMWPP_Classes_ObjController::getClass('HMWPP_Models_Services_Tftotp');

        /** @var HMWPP_Models_Services_Email $emailService */
        $emailService = HMWPP_Classes_ObjController::getClass('HMWPP_Models_Services_Email');

        // If none of the services are active
        if( !$twoFactorService->isServiceActive( $user ) && !$emailService->isServiceActive( $user ) ){
            return;
        }

        // Invalidate the current login session to prevent from being re-used.
        $this->model->destroyCurrentSession( $user );

        // Also clear the cookies which are no longer valid.
        if(function_exists('wp_clear_auth_cookie')){
            wp_clear_auth_cookie();
        }

        $this->model->showTwoFactorLogin( $user );
        exit();
    }

    /**
     * Login form validation.
     *
     */
    public function action()
    {
        parent::action();

        //if current user can't manage settings
        if (!HMWPP_Classes_Tools::userCan('hmwp_manage_settings') ) {
            return;
        }

        switch ( HMWPP_Classes_Tools::getValue('action') ) {
            case 'hmwpp_2fasettings':

                if( 'POST' === strtoupper( $_SERVER['REQUEST_METHOD'] ) ){
                    $this->saveValues($_POST);
                }

                HMWPP_Classes_Error::setNotification(esc_html__('Saved', 'hide-my-wp-pack'));

                break;
            case 'hmwpp_totp_submit':
                $user_id = HMWPP_Classes_Tools::getValue('user_id');
                $key = HMWPP_Classes_Tools::getValue('key');
                $code = HMWPP_Classes_Tools::getValue('authcode');

                /** @var HMWPP_Models_Services_Tftotp $twoFactorService */
                $twoFactorService = HMWPP_Classes_ObjController::getClass('HMWPP_Models_Services_Tftotp');
                $response = $twoFactorService->setupTotp($user_id,$key,$code);

                if(!is_wp_error($response)){
                    $user = get_user_by('ID', $user_id);

                    $this->options = $twoFactorService->getTwoFactorOption( $user );

                    //Show the two factor block
                    wp_send_json_success($this->getView('blocks/Totp'));
                }else{
                    /** @var WP_Error $response */
                    wp_send_json_error($response->get_error_message());
                }
                break;
            case 'hmwpp_totp_reset':
                $user_id = HMWPP_Classes_Tools::getValue('user_id');

                /** @var HMWPP_Models_Services_Tftotp $twoFactorService */
                $twoFactorService = HMWPP_Classes_ObjController::getClass('HMWPP_Models_Services_Tftotp');

                if($twoFactorService->deleteUserTotpKey($user_id)){
                    $user = get_user_by('ID', $user_id);

                    $this->options = $twoFactorService->getTwoFactorOption( $user );

                    //Show the two factor block
                    wp_send_json_success($this->getView('blocks/Totp'));
                }else{
                    wp_send_json_error('Error');
                }
                break;
            case 'hmwpp_codes_generate':
                $user_id = HMWPP_Classes_Tools::getValue('user_id');

                /** @var HMWPP_Models_Services_Codes $codesService */
                $codesService = HMWPP_Classes_ObjController::getClass('HMWPP_Models_Services_Codes');

                if($user = get_user_by('ID', (int)$user_id)){
                    if($this->codes = $codesService->generateCodes( $user )){
                        $this->downloadLinks = $codesService->getDownloadLink($this->codes);

                        //Show the two factor block
                        wp_send_json_success($this->getView('blocks/Codes'));
                    }else{
                        wp_send_json_error('Error');
                    }
                }

                break;

            case 'hmwpp_email_submit':
                $user_id = HMWPP_Classes_Tools::getValue('user_id');
                $email = HMWPP_Classes_Tools::getValue('email');

                /** @var HMWPP_Models_Services_Email $emailService */
                $emailService = HMWPP_Classes_ObjController::getClass('HMWPP_Models_Services_Email');

                if($user_id){
                    if($emailService->setUserEmail( $user_id, $email )){
                        $user = get_user_by('ID', $user_id);

                        $this->options = $emailService->getEmailOption( $user );

                        wp_send_json_success($this->getView('blocks/Email'));
                    }else{
                        wp_send_json_error('Error');
                    }
                }

                break;

            case 'hmwpp_email_reset':
                $user_id = HMWPP_Classes_Tools::getValue('user_id');

                /** @var HMWPP_Models_Services_Email $emailService */
                $emailService = HMWPP_Classes_ObjController::getClass('HMWPP_Models_Services_Email');

                if($user_id){
                    if($emailService->deleteUserEmail( $user_id )){
                        $user = get_user_by('ID', $user_id);

                        $this->options = $emailService->getEmailOption( $user );

                        wp_send_json_success($this->getView('blocks/Email'));
                    }else{
                        wp_send_json_error('Error');
                    }
                }

                break;

        }
    }

    public function saveValues($params) {

        //Save the option values
        foreach ($params as $key => $value) {
            if (in_array($key, array_keys(HMWPP_Classes_Tools::$options))) {

                //Sanitize each value from subarray
                HMWPP_Classes_Tools::$options[$key] = HMWPP_Classes_Tools::getValue($key);
            }
        }

        //sanitize the value and save it
        HMWPP_Classes_Tools::saveOptions();
    }

}
