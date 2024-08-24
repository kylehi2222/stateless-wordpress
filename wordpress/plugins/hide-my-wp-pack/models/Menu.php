<?php
/**
 * Plugin Menu Configuration Model
 * Called when the user is logged in as admin or with the proper capabilities
 *
 * @file  The Menu Model file
 * @package HMWPP/MenuModel
 * @since 1.0.0
 */

defined('ABSPATH') || die('Cheatin\' uh?');

class HMWPP_Models_Menu
{

    /**
     * Get the admin Menu Tabs
     * @param array $menu
     * @return array
     * @throws Exception
     */
    public function getMenu($menu)
    {

        if(!HMWP_Classes_Tools::getOption('hmwp_token')){
            return $menu;
        }

        $hmwpp_menu = array();

        if(!isset($menu['hmwp_twofactor'])){
            $hmwpp_menu['hmwp_templogin'] = array(
                'name' => esc_html__("Temporary Login", 'hide-my-wp-pack'),
                'title' => esc_html__("Temporary Login", 'hide-my-wp-pack'),
                'capability' => 'hmwp_manage_settings',
                'parent' => 'hmwp_settings',
                'show' => HMWPP_Classes_Tools::getOption('hmwp_templogin'),
                'function' => array(HMWPP_Classes_ObjController::getClass('HMWPP_Controllers_Settings'), 'init'),
            );
        }

        $hmwpp_menu['hmwp_twofactor'] = array(
            'name' => esc_html__("2FA Login", 'hide-my-wp-pack'),
            'title' => esc_html__("Two-factor Authentication", 'hide-my-wp-pack'),
            'capability' => 'hmwp_manage_settings',
            'parent' => 'hmwp_settings',
            'show' => HMWPP_Classes_Tools::getOption('hmwp_2falogin'),
            'function' => array(HMWPP_Classes_ObjController::getClass('HMWPP_Controllers_Settings'), 'init'),
        );

        //if the menu doesn't exist
        if(!isset($menu['hmwp_twofactor'])){
            $splitIndex = 7;
            $menu = array_merge(
                array_slice($menu, 0, $splitIndex),
                $hmwpp_menu,
                array_slice($menu, $splitIndex)
            );
        }else{
            $menu = array_merge($menu, $hmwpp_menu);
        }


        //Remove the menu when the feature in hidden by the user
        foreach ($menu as $key => $value){
            $keys = array_keys(HMWPP_Classes_Tools::$options);
            if (!empty($keys) && in_array($key . '_menu_show', $keys)) {
                if (!HMWPP_Classes_Tools::getOption($key . '_menu_show')) {
                    unset($menu[$key]);
                }
            }
        }


        //Return the menu array
        return $menu;
    }

    /**
     * Get the Submenu section for each menu
     *
     * @param string $current
     * @return array|mixed
     */
    public function getSubMenu($current)
    {
        $submenu = array();

        $subtabs = array(
            'hmwp_templogin' => array(
                array(
                    'title' => esc_html__("Temporary Logins", 'hide-my-wp-pack'),
                    'tab' => 'logins',
                ),
                array(
                    'title' => esc_html__("Settings", 'hide-my-wp-pack'),
                    'tab' => 'settings',
                ),
            ),
            'hmwp_twofactor' => array(
                array(
                    'title' => esc_html__("2FA Logins", 'hide-my-wp-pack'),
                    'tab' => 'logins',
                ),
                array(
                    'title' => esc_html__("Settings", 'hide-my-wp-pack'),
                    'tab' => 'settings',
                ),
            ),
        );

        //Remove the submenu is the user hides it from all features
        foreach ($subtabs as $key => &$values) {
            foreach ($values as $index => $value) {
                if (in_array($key . '_' . $value['tab'] . '_show', array_keys(HMWPP_Classes_Tools::$options))) {
                    if (!HMWPP_Classes_Tools::getOption($key . '_' . $value['tab'] . '_show')) {
                        unset($values[$index]);
                    }
                }
            }
        }

        //Return all submenus
        if(isset($subtabs[$current])) {
            $submenu =  $subtabs[$current];
        }

        return $submenu;
    }

    public function getFeatures( $features ){

        if(!HMWPP_Classes_Tools::getOption('hmwp_token')){
            return $features;
        }

        $feature = array(
            'title' => esc_html__("Magic Link Login", 'hide-my-wp-pack') . ' (New)',
            'description' => esc_html__("Allow users to log in to the website using their email address and a unique login URL delivered via email.", 'hide-my-wp-pack'),
            'free' => true,
            'option' => 'hmwp_uniquelogin',
            'active' => HMWPP_Classes_Tools::getOption('hmwp_uniquelogin'),
            'optional' => true,
            'connection' => false,
            'logo' => 'fa fa-clock-o',
            'link' => false,
            'details' => HMWPP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/magic-link-login/',
            'show' => true,
        );

        if($index = array_search('hmwp_uniquelogin', array_column($features,'option'))){
            $features[$index] = $feature;
        }else{
            $features[] = $feature;
        }

        $feature = array(
            'title' => esc_html__("Woocommerce Magic Link", 'hide-my-wp-pack') . ' (New)',
            'description' => esc_html__("Allow users to log in to the website using their email address and a unique login URL delivered via email.", 'hide-my-wp-pack'),
            'free' => true,
            'option' => 'hmwp_uniquelogin_woocommerce',
            'active' => HMWPP_Classes_Tools::getOption('hmwp_uniquelogin_woocommerce'),
            'optional' => true,
            'connection' => false,
            'logo' => 'fa fa-clock-o',
            'link' => false,
            'details' => HMWPP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/magic-link-login/#integrations-d08bdc1e-e4ea-4138-9e69-6c7133950313',
            'show' => HMWPP_Classes_Tools::isPluginActive('woocommerce/woocommerce.php'),
        );

        if($index = array_search('hmwp_uniquelogin_woocommerce', array_column($features,'option'))){
            $features[$index] = $feature;
        }else{
            $features[] = $feature;
        }

        $feature = array(
            'title' => esc_html__("Temporary Logins", 'hide-my-wp-pack') ,
            'description' => esc_html__("Create a temporary login URL with any user role to access the website dashboard without username and password for a limited period of time.", 'hide-my-wp-pack'),
            'free' => true,
            'option' => 'hmwp_templogin',
            'active' => HMWPP_Classes_Tools::getOption('hmwp_templogin'),
            'optional' => true,
            'connection' => false,
            'logo' => 'fa fa-clock-o',
            'link' => HMWPP_Classes_Tools::getSettingsUrl('hmwp_templogin#tab=logins', true),
            'details' => HMWPP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/temporary-logins/',
            'show' => true,
        );

        if($index = array_search('hmwp_templogin', array_column($features,'option'))){
            $features[$index] = $feature;
        }else{
            $features[] = $feature;
        }

        if(!array_search('hmwp_2falogin', array_column($features,'option'))){
            $features[] = array(
                'title' => esc_html__("2FA", 'hide-my-wp-pack') ,
                'description' => esc_html__("Add Two Factor security on login page with Code Scan or Email Code authentication.", 'hide-my-wp-pack'),
                'free' => true,
                'option' => 'hmwp_2falogin',
                'active' => HMWPP_Classes_Tools::getOption('hmwp_2falogin'),
                'optional' => true,
                'connection' => false,
                'logo' => 'fa fa-window-maximize',
                'link' => HMWPP_Classes_Tools::getSettingsUrl('hmwp_twofactor', true),
                'details' => HMWPP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/two-factor/',
                'show' => true,
            );
        }

        $feature = array(
            'title' => esc_html__("2FA", 'hide-my-wp-pack') ,
            'description' => esc_html__("Add Two Factor security on login page with Code Scan or Email Code authentication.", 'hide-my-wp-pack'),
            'free' => true,
            'option' => 'hmwp_2falogin',
            'active' => HMWPP_Classes_Tools::getOption('hmwp_2falogin'),
            'optional' => true,
            'connection' => false,
            'logo' => 'fa fa-window-maximize',
            'link' => HMWPP_Classes_Tools::getSettingsUrl('hmwp_twofactor', true),
            'details' => HMWPP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/two-factor/',
            'show' => true,
        );

        if($index = array_search('hmwp_2falogin', array_column($features,'option'))){
            $features[$index] = $feature;
        }else{
            $features[] = $feature;
        }

        return $features;
    }


    /**
     * Load the Settings class when the plugin settings are loaded
     * Used for loading the CSS and JS only in the settings area
     *
     * @param string $classes
     * @return string
     * @throws Exception
     */
    public function addSettingsClass( $classes )
    {
        return HMWP_Classes_ObjController::getClass('HMWP_Models_Menu')->addSettingsClass( $classes );
    }

    /**
     * Add compatibility on CSS and JS with other plugins and themes
     * Called in Menu Controller to fix teh CSS and JS compatibility
     */
    public function fixEnqueueErrors()
    {
        HMWP_Classes_ObjController::getClass('HMWP_Models_Menu')->fixEnqueueErrors();
    }

}
