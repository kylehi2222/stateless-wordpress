<?php
/**
 * Settings Class
 * Called when the plugin setting is loaded
 *
 * @file The Settings file
 * @package HMWPP/Settings
 * @since 1.0.0
 */

defined('ABSPATH') || die('Cheatin\' uh?');

class HMWPP_Controllers_Settings extends HMWPP_Classes_FrontController
{

    public function __construct()
    {
        parent::__construct();

        //Add the Settings class only for the plugin settings page
        add_filter('admin_body_class', array(HMWPP_Classes_ObjController::getClass('HMWPP_Models_Menu'), 'addSettingsClass'));

    }

    /**
     * Called on Menu hook
     * Init the Settings page
     *
     * @return void
     * @throws Exception
     */
    public function init()
    {
        /////////////////////////////////////////////////
        //Get the current Page
        $page = HMWPP_Classes_Tools::getValue('page');

        if (strpos($page, '_') !== false ) {
            $tab = substr($page, (strpos($page, '_') + 1));

            if (method_exists($this, $tab)) {
                call_user_func(array($this, $tab));
            }
        }
        /////////////////////////////////////////////////

        //We need that function so make sure is loaded
        if (!function_exists('is_plugin_active_for_network') ) {
            include_once ABSPATH . '/wp-admin/includes/plugin.php';
        }

        //Load the css for Settings
        HMWPP_Classes_ObjController::getClass('HMWPP_Classes_DisplayController')->loadMedia('popper');

        if (is_rtl() ) {
            HMWPP_Classes_ObjController::getClass('HMWPP_Classes_DisplayController')->loadMedia('bootstrap.rtl');
            HMWPP_Classes_ObjController::getClass('HMWPP_Classes_DisplayController')->loadMedia('rtl');
        } else {
            HMWPP_Classes_ObjController::getClass('HMWPP_Classes_DisplayController')->loadMedia('bootstrap');
        }

        HMWPP_Classes_ObjController::getClass('HMWPP_Classes_DisplayController')->loadMedia('bootstrap-select');
        HMWPP_Classes_ObjController::getClass('HMWPP_Classes_DisplayController')->loadMedia('font-awesome');
        HMWPP_Classes_ObjController::getClass('HMWPP_Classes_DisplayController')->loadMedia('switchery');
        HMWPP_Classes_ObjController::getClass('HMWPP_Classes_DisplayController')->loadMedia('alert');
        HMWPP_Classes_ObjController::getClass('HMWPP_Classes_DisplayController')->loadMedia('clipboard');
        HMWPP_Classes_ObjController::getClass('HMWPP_Classes_DisplayController')->loadMedia('settings');

        //Show errors on top
        HMWPP_Classes_ObjController::getClass('HMWPP_Classes_Error')->hookNotices();

        echo '<noscript><div class="alert-danger text-center py-3">'. sprintf(esc_html__("Javascript is disabled on your browser! You need to activate the javascript in order to use %s plugin.", 'hide-my-wp-pack'), HMWPP_Classes_Tools::getOption('hmwp_plugin_name')) .'</div></noscript>';

        $this->show(ucfirst(str_replace('hmwp_', '', $page)));

    }

    public function twofactor(){

        HMWPP_Classes_ObjController::getClass('HMWPP_Classes_DisplayController')->loadMedia('qrcode');
        HMWPP_Classes_ObjController::getClass('HMWPP_Classes_DisplayController')->loadMedia('twofactor');

    }

    /**
     * Get all 2FA logs
     *
     * @return array The array of 2FA logs
     * @throws Exception
     */
    public function getLogs(){

        $logs = array();

        if (apply_filters('hmwp_showlogins', true) ) {

            /** @var HMWPP_Models_Services_Tftotp $twoFactorService */
            $twoFactorService = HMWPP_Classes_ObjController::getClass('HMWPP_Models_Services_Tftotp');

            /** @var HMWPP_Models_Services_Email $emailService */
            $emailService = HMWPP_Classes_ObjController::getClass('HMWPP_Models_Services_Email');

            /** @var HMWPP_Models_Twofactor $twoFactorModel */
            $twoFactorModel = HMWPP_Classes_ObjController::getClass('HMWPP_Models_Twofactor');

            /** @var WP_User[] $users */
            $users = get_users();

            foreach ($users as $user){

                if($last_totp_login = $twoFactorService->getLastLoginTimestamp( $user->ID )){
                    $logs[] = array(
                        'user' => $user,
                        'email' => $user->user_email,
                        'last_login' => $twoFactorModel->timeElapsed($last_totp_login),
                        'success' => true,
                        'mode' =>  esc_html__('2FA Code', 'hide-my-wp-pack'),
                    );
                }

                if($last_totp_login = $emailService->getLastLoginTimestamp( $user->ID )){
                    $logs[] = array(
                        'user' => $user,
                        'email' => $user->user_email,
                        'last_login' => $twoFactorModel->timeElapsed($last_totp_login),
                        'success' => true,
                        'mode' =>  esc_html__("Email Code", 'hide-my-wp-pack'),
                    );
                }

                /** @var HMWPP_Models_Twofactor $twoFactorModel */
                $twoFactorModel = HMWPP_Classes_ObjController::getClass('HMWPP_Models_Twofactor');

                if( $last_failed = $twoFactorModel->getLastUserLoginFail( $user ) ){
                    $logs[] = array(
                        'user' => $user,
                        'email' => $user->user_email,
                        'last_login' => gmdate(get_option('date_format') . ' ' . get_option('time_format'), $last_failed),
                        'success' => false,
                    );
                }

            }

            return $logs;

        }

        return $logs;

    }

    /**
     * Get log table
     * @return string
     * @throws Exception
     */
    public function getLogListTable(  )
    {
        $logs = $this->getLogs();

        $data = '<table class="table table-striped" >';
        $data .= "<tr>
                    <th>" . esc_html__('Email', 'hide-my-wp-pack') . "</th>
                    <th>" . esc_html__('Last Access', 'hide-my-wp-pack') . "</th>
                    <th>" . esc_html__('Mode', 'hide-my-wp-pack') . "</th>
                    <th>" . esc_html__('Login', 'hide-my-wp-pack') . "</th>
                 </tr>";

        if (!empty($logs)) {
            foreach ($logs as $log) {
                $user = $log['user'];

                $user_details = '<div><span>';
                if ( ( esc_attr( $user->first_name ) ) ) {
                    $user_details .= '<span>' . esc_attr( $user->first_name ) . '</span>';
                }

                if ( ( esc_attr( $user->last_name ) ) ) {
                    $user_details .= '<span> ' . esc_attr( $user->last_name ) . '</span>';
                }

                $user_details .= "  (<span class='user-login'>" . esc_attr( $user->user_login ) . ')</span><br />';

                if ( ( esc_attr( $user->user_email ) ) ) {
                    $user_details .= '<p class="inline-block pt-1 font-medium text-black-50">' . esc_attr( $user->user_email ) . '</p>';
                }

                $user_details .= '</span></div>';

                $status = ($log['success'] ? esc_attr__('Success', 'hide-my-wp-pack') : esc_attr__('Failed', 'hide-my-wp-pack'));
                $data .= "<tr>
                        <td>$user_details</td>
                        <td>{$log['last_login']}</td>
                        <td class='" . ( $log['success'] ? 'text-success' : 'text-danger' ) . " pl-4'>{$status}</td>
                        <td class='p-2'>" . ( isset($log['mode']) ? $log['mode'] : '') . "</td>
                     </tr>";
            }
        } else {
            $data .= "<tr><td colspan='5'>" . esc_html__('No logins with 2FA.','hide-my-wp-pack') . "</td></tr>";
        }
        $data .= "</table>";

        return $data;
    }


    /**
     * Log the user event
     *
     * @throws Exception
     */
    public function templogin()
    {
        //clear previous alerts
        HMWPP_Classes_Error::clearErrors();

        if (HMWPP_Classes_Tools::getValue('action') == 'hmwp_update' && HMWPP_Classes_Tools::getValue('user_id') ) {
            $user_id = HMWPP_Classes_Tools::getValue('user_id') ;

            $this->user = get_user_by('ID', $user_id);
            $this->user->details = HMWPP_Classes_ObjController::getClass('HMWPP_Models_Templogin')->getUserDetails($this->user);
        }

        if(HMWPP_Classes_Tools::getValue('hmwp_message')){
            HMWPP_Classes_Error::setNotification(HMWPP_Classes_Tools::getValue('hmwp_message', false, true),'success');
        }

    }

    /**
     * Get the Admin Toolbar
     *
     * @param  null $current
     * @return string $content
     * @throws Exception
     */
    public function getAdminTabs( $current = null )
    {
        add_filter('hmwp_submenu', function ($submenu) use ($current){
            return HMWPP_Classes_ObjController::getClass('HMWPP_Models_Menu')->getSubMenu($current);
        });

        return HMWP_Classes_ObjController::getClass('HMWP_Controllers_Settings')->getAdminTabs($current);
    }

    /**
     * Called when an action is triggered
     *
     * @throws Exception
     */
    public function action()
    {
        parent::action();

        if (!HMWPP_Classes_Tools::userCan('hmwp_manage_settings') ) {
            return;
        }

        switch ( HMWPP_Classes_Tools::getValue('action') ) {
            case 'hmwpp_settings':

            break;
        }

    }

    /**
     * If javascript is not loaded
     * @return void
     */
    public function hookFooter()
    {
        echo '<noscript><style>.tab-panel {display: block;}</style></noscript>';
    }

}
