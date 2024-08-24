<?php

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

if (!class_exists('DarkMySiteAdmin')) {
    class DarkMySiteAdmin
    {

        public $utils;
        public $settings;
        public $external_support;

        public $data_settings;
        public $unique_id;

        public function __construct()
        {
            $this->utils = new DarkMySiteUtils($this);
            $this->settings = new DarkMySiteSettings($this);
            $this->external_support = new DarkMySiteExternalSupport($this);
            new DarkMySiteAdminAjax($this);

            $this->data_settings = $this->settings->get_all_darkmysite_settings();
            $this->unique_id = rand();

            add_action("admin_menu", array($this, 'darkmysite_admin_menu'));
            add_action('admin_enqueue_scripts', array($this, 'darkmysite_admin_enqueue'));
            add_action( 'plugin_action_links_' . DARKMYSITE_PRO_BASE_PATH, array( $this, 'darkmysite_action_links') );

            if($this->data_settings["enable_admin_dark_mode"] == "1"){
                add_action('admin_bar_menu',  array($this, 'darkmysite_admin_bar_switch'), 9999);
                add_action( 'admin_print_scripts', array( $this, 'darkmysite_admin_header_script' ), 1);
                add_action( 'admin_footer', array( $this, 'darkmysite_admin_footer_script' ) );
            }
        }

        function darkmysite_action_links($links) {
            $settings_url = add_query_arg( 'page', 'darkmysite-dashboard', get_admin_url() . 'admin.php' );
            $setting_arr = array('<a href="' . esc_url( $settings_url ) . '">Dashboard</a>');
            $links = array_merge($setting_arr, $links);
            return $links;
        }


        function darkmysite_admin_menu()
        {
            if($this->data_settings["display_in_admin_settings_menu"] == "0"){
                $icon_url = DARKMYSITE_PRO_IMG_DIR . "darkmysite_icon.svg";
                add_menu_page("DarkMySite", "DarkMySite", 'manage_options', "darkmysite-dashboard", array($this, 'darkmysite_admin_dashboard'), $icon_url, 6);
                add_submenu_page("darkmysite-dashboard", "DarkMySite", 'Dashboard', "manage_options", 'darkmysite-dashboard', array($this, 'darkmysite_admin_dashboard'));

            }else if($this->data_settings["display_in_admin_settings_menu"] == "1"){
                add_options_page("DarkMySite", "DarkMySite", 'manage_options', "darkmysite-dashboard", array($this, 'darkmysite_admin_dashboard'));
            }
        }



        function darkmysite_admin_enqueue( $page )
        {
            if($page == "toplevel_page_darkmysite-dashboard" || $page == "settings_page_darkmysite-dashboard"){
                wp_enqueue_style('darkmysite-admin-switch', DARKMYSITE_PRO_CSS_DIR.'client_main.css', array(), DARKMYSITE_PRO_VERSION);
                wp_enqueue_style('darkmysite-admin-main', DARKMYSITE_PRO_CSS_DIR.'admin_main.css', array(), DARKMYSITE_PRO_VERSION);
                wp_enqueue_style('darkmysite-admin-select2', DARKMYSITE_PRO_CSS_DIR.'select2.min.css', array(), DARKMYSITE_PRO_VERSION);
                wp_enqueue_style('darkmysite-admin-license-form', DARKMYSITE_PRO_CSS_DIR.'admin_license_form.css', array(), DARKMYSITE_PRO_VERSION);

                wp_enqueue_script( 'darkmysite-admin-main', DARKMYSITE_PRO_JS_DIR.'admin_main.js', array('jquery'), DARKMYSITE_PRO_VERSION );
                wp_enqueue_script( 'darkmysite-admin-select2', DARKMYSITE_PRO_JS_DIR.'select2.min.js', array('jquery'), DARKMYSITE_PRO_VERSION );
                wp_enqueue_script( 'darkmysite-admin-license-form', DARKMYSITE_PRO_JS_DIR.'admin_license_form.js', array('jquery'), DARKMYSITE_PRO_VERSION );

                wp_enqueue_media();
            }

            if($this->data_settings["enable_admin_dark_mode"] == "1"){
                if($this->darkmysite_is_dark_mode_allowed()){
                    if (!wp_style_is('darkmysite-admin-switch', 'enqueued')) {
                        wp_enqueue_style('darkmysite-admin-switch', DARKMYSITE_PRO_CSS_DIR.'client_main.css', array(), DARKMYSITE_PRO_VERSION);
                    }
                    wp_enqueue_script( 'darkmysite-admin-client-main', DARKMYSITE_PRO_JS_DIR.'client_main.js', array(), DARKMYSITE_PRO_VERSION);
                }
            }
        }

        function darkmysite_admin_dashboard()
        {
            if($this->settings->isLicenseActive()){
                include_once DARKMYSITE_PRO_PATH . "backend/templates/dashboard.php";
            }else{
                include_once DARKMYSITE_PRO_PATH . "backend/templates/license_form.php";
            }
        }

        function darkmysite_admin_bar_switch($wp_admin_bar) {
            if($this->darkmysite_is_dark_mode_allowed()){
                $args = array(
                    'parent' => 'top-secondary',
                    'id' => 'darkmysite_admin_bar_switch_container',
                    'meta' => array(
                        'class' => 'darkmysite_admin_bar_switch_container',
                        'onclick' => 'darkmysite_switch_trigger()',
                    )
                );
                $wp_admin_bar->add_node($args);
            }
        }

        function darkmysite_is_dark_mode_allowed()
        {
            /* Disable on Disallowed Admin Plugins */
            if($this->utils->isRestrictedByDisallowedAdminPages($this->data_settings["disallowed_admin_pages"])){
                return False;
            }
            return True;
        }

        function darkmysite_admin_header_script()
        {
            if($this->darkmysite_is_dark_mode_allowed()){
                include_once DARKMYSITE_PRO_PATH . "frontend/templates/header_script.php";
            }
        }

        function darkmysite_admin_footer_script()
        {
            if($this->darkmysite_is_dark_mode_allowed()){
                include_once DARKMYSITE_PRO_PATH . "frontend/templates/footer_script.php";
            }
        }

    }
}




if(is_admin()){
    new DarkMySiteAdmin();
}