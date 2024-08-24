<?php

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

if (!class_exists('DarkMySiteSupport')) {
    class DarkMySiteSupport
    {

        public $base_client;
        public $base_admin; /* Required for update_settings API */
        public $license_code;

        public function __construct($base_client)
        {
            $this->base_client = $base_client;

            if($this->base_client->data_settings["enable_support_team_modify_settings"] == "1"){
                if($this->base_client->settings->isLicenseActive()){
                    $this->license_code = $this->base_client->settings->updateLicenseSettings("license_code");
                    add_action( 'rest_api_init', array($this, 'darkmysite_support_register_routes') );
                }
            }
        }

        public function darkmysite_support_register_routes() {
            register_rest_route( 'darkmysite/v2', '/get-settings/'.$this->license_code, array(
                'methods' => 'GET',
                'callback' => array($this, 'darkmysite_support_get_settings'),
            ) );
            register_rest_route( 'darkmysite/v2', '/save-settings/'.$this->license_code, array(
                'methods' => 'POST',
                'callback' => array($this, 'darkmysite_support_save_settings'),
            ) );
        }

        public function darkmysite_support_temporary_capabilities($allcaps, $caps, $args)
        {
            $allcaps['manage_options'] = true;
            return $allcaps;
        }

        public function darkmysite_support_get_settings( $request ) {
            global $wp_version;
            $response = array();
            $response["darkmysite_settings"] = $this->base_client->settings->get_all_darkmysite_settings();
            $response["nav_menus"] = $this->base_client->utils->getWpNavMenus();
            $response["wp_pages"] = $this->base_client->utils->getWpPages();
            $response["plugin_version"] = DARKMYSITE_PRO_VERSION;
            $response["wp_version"] = $wp_version;
            $response["php_version"] = phpversion();
            return new WP_REST_Response( $response, 200 );
        }

        public function darkmysite_support_save_settings(WP_REST_Request $request ) {
            $params = $request->get_params();
            foreach ($params as $key => $value){
                $_REQUEST[$key] = $value;
            }

            wp_set_current_user(0, '');
            add_filter('user_has_cap', array($this, "darkmysite_support_temporary_capabilities"), 10, 3);

            $this->base_admin = $this->base_client;
            ob_start();
            include DARKMYSITE_PRO_PATH . "backend/api/update_settings.php";
            $output_not_to_display = ob_get_clean();

            remove_filter('user_has_cap', array($this, "darkmysite_support_temporary_capabilities"));
            return array( 'success' => true);
        }


    }
}
