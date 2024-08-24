<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

if ( ! class_exists( 'DarkMySiteSettings' ) ) {
    class DarkMySiteSettings
    {

        public $base_admin;

        function __construct($base_admin)
        {

            $this->base_admin = $base_admin;

            $defaultOption = array();
            if (!get_option("darkmysite_license")) {
                update_option('darkmysite_license', $defaultOption);
            }
            if (!get_option("darkmysite_settings")) {
                update_option('darkmysite_settings', $defaultOption);
            }

        }




        /* ****************** License Operations ****************** */

        public function isLicenseActive()
        {
            $is_active = False;
            $licenseEmail = $this->updateLicenseSettings("email");
            $licenseCode = $this->updateLicenseSettings("license_code");
            if($licenseEmail == Null || $licenseCode == Null){
                $is_active = False;
            }else{
                $last_checked_time = $this->updateLicenseSettings("last_checked_time");
                if($last_checked_time == Null){
                    $is_active = False;
                }else if(time() - $last_checked_time > 3600){
                    if($this->isLicenseValid($licenseEmail, $licenseCode)){
                        $this->updateLicenseSettings("email", $licenseEmail);
                        $this->updateLicenseSettings("license_code", $licenseCode);
                        $this->updateLicenseSettings("last_checked_time", time());
                        $is_active = True;
                    }else {
                        $this->updateLicenseSettings("email", "");
                        $this->updateLicenseSettings("license_code", "");
                        $this->updateLicenseSettings("last_checked_time", 0);
                        $is_active = False;
                    }
                }else{
                    $is_active = True;
                }
            }
            return $is_active;
        }



        public function isLicenseValid($email, $license_code)
        {
            $domain = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : DARKMYSITE_PRO_SERVER;
            $response = wp_remote_post( DARKMYSITE_PRO_SERVER.'/api/license/validate_license.php',
                array('method' => 'POST',
                    'body' => array('email' => $email, 'license_code' => $license_code, 'domain' => $domain)
                )
            );

            if (! is_wp_error( $response ) ) {
                $response_body = json_decode(wp_remote_retrieve_body($response));
                if(isset($response_body->status)){
                    if($response_body->status == "true"){
                        return True;
                    }
                }
            }else{
                $data_via_another_way = file_get_contents(DARKMYSITE_PRO_SERVER.'/api/license/validate_license.php?email='.$email.'&license_code='.$license_code.'&domain='.$domain);
                $response_body = json_decode($data_via_another_way);
                if(isset($response_body->status)){
                    if($response_body->status == "true"){
                        return True;
                    }
                }
            }
            return False;
        }


        public function removeLicense($email, $license_code)
        {
            $domain = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : DARKMYSITE_PRO_SERVER;
            $response = wp_remote_post( DARKMYSITE_PRO_SERVER.'/api/license/remove_license_domain.php',
                array('method' => 'POST',
                    'body' => array('email' => $email, 'license_code' => $license_code, 'domain' => $domain)
                )
            );

            if (! is_wp_error( $response ) ) {
                $response_body = json_decode(wp_remote_retrieve_body($response));
                if(isset($response_body->status)){
                    if($response_body->status == "true"){
                        return True;
                    }
                }
            }else{
                $data_via_another_way = file_get_contents(DARKMYSITE_PRO_SERVER.'/api/license/remove_license_domain.php?email='.$email.'&license_code='.$license_code.'&domain='.$domain);
                $response_body = json_decode($data_via_another_way);
                if(isset($response_body->status)){
                    if($response_body->status == "true"){
                        return True;
                    }
                }
            }
            return False;
        }

        public function updateLicenseSettings($key, $value = "<<darkmysite_empty_value>>")
        {
            $exits = false;
            $exitingValue = Null;
            $dataLicenseSettings = get_option("darkmysite_license");
            $dataNewLicenseSettings = array();
            foreach ($dataLicenseSettings as $singleSettings) {
                if (isset($singleSettings['key'])) {
                    if ($singleSettings['key'] == $key) {
                        $exits = true;
                        $exitingValue = $singleSettings['value'];
                        $singleSettings['value'] = ($value != "<<darkmysite_empty_value>>") ? $value : $singleSettings['value'];
                    }
                }
                if ($value != "<<darkmysite_empty_value>>") {
                    $dataNewLicenseSettings[] = $singleSettings;
                }
            }
            if ($exits && $value != "<<darkmysite_empty_value>>") {
                update_option('darkmysite_license', $dataNewLicenseSettings);
            } else if (!$exits && $value != "<<darkmysite_empty_value>>") {
                $dataNewLicenseSettings[] = array("key" => $key, "value" => $value);
                update_option('darkmysite_license', $dataNewLicenseSettings);
            } else if ($exits && $value == "<<darkmysite_empty_value>>") {
                return stripslashes($exitingValue);
            }else{
                return Null;
            }
        }




        public function updateSettings($key, $value = "<<darkmysite_empty_value>>")
        {
            $exits = false;
            $exitingValue = Null;
            $dataSettings = get_option("darkmysite_settings");
            $dataNewSettings = array();
            foreach ($dataSettings as $singleSettings) {
                if (isset($singleSettings['key'])) {
                    if ($singleSettings['key'] == $key) {
                        $exits = true;
                        $exitingValue = $singleSettings['value'];
                        $singleSettings['value'] = ($value != "<<darkmysite_empty_value>>") ? $value : $singleSettings['value'];
                    }
                }
                if ($value != "<<darkmysite_empty_value>>") {
                    $dataNewSettings[] = $singleSettings;
                }
            }
            if ($exits && $value != "<<darkmysite_empty_value>>") {
                update_option('darkmysite_settings', $dataNewSettings);
            } else if (!$exits && $value != "<<darkmysite_empty_value>>") {
                $dataNewSettings[] = array("key" => $key, "value" => $value);
                update_option('darkmysite_settings', $dataNewSettings);
            } else if ($exits && $value == "<<darkmysite_empty_value>>") {
                return stripslashes($exitingValue);
            }else{
                return Null;
            }
        }



        public function get_all_darkmysite_settings(){
            $settings = array();

            /* Control */

            $settings["show_rating_block"] = $this->updateSettings("show_rating_block");
            $settings["show_rating_block"] = ($settings["show_rating_block"] == Null) ? "1" : $settings["show_rating_block"];

            $settings["show_support_msg_block"] = $this->updateSettings("show_support_msg_block");
            $settings["show_support_msg_block"] = ($settings["show_support_msg_block"] == Null) ? "1" : $settings["show_support_msg_block"];

            $settings["enable_dark_mode_switch"] = $this->updateSettings("enable_dark_mode_switch");
            $settings["enable_dark_mode_switch"] = ($settings["enable_dark_mode_switch"] == Null) ? "1" : $settings["enable_dark_mode_switch"];

            $settings["enable_default_dark_mode"] = $this->updateSettings("enable_default_dark_mode");
            $settings["enable_default_dark_mode"] = ($settings["enable_default_dark_mode"] == Null) ? "0" : $settings["enable_default_dark_mode"];

            $settings["enable_os_aware"] = $this->updateSettings("enable_os_aware");
            $settings["enable_os_aware"] = ($settings["enable_os_aware"] == Null) ? "1" : $settings["enable_os_aware"];

            $settings["enable_keyboard_shortcut"] = $this->updateSettings("enable_keyboard_shortcut");
            $settings["enable_keyboard_shortcut"] = ($settings["enable_keyboard_shortcut"] == Null) ? "1" : $settings["enable_keyboard_shortcut"];

            $settings["enable_time_based_dark"] = $this->updateSettings("enable_time_based_dark");
            $settings["enable_time_based_dark"] = ($settings["enable_time_based_dark"] == Null) ? "0" : $settings["enable_time_based_dark"];

            $settings["time_based_dark_start"] = $this->updateSettings("time_based_dark_start");
            $settings["time_based_dark_start"] = ($settings["time_based_dark_start"] == Null) ? "19:00" : $settings["time_based_dark_start"];

            $settings["time_based_dark_stop"] = $this->updateSettings("time_based_dark_stop");
            $settings["time_based_dark_stop"] = ($settings["time_based_dark_stop"] == Null) ? "07:00" : $settings["time_based_dark_stop"];

            $settings["hide_on_desktop"] = $this->updateSettings("hide_on_desktop");
            $settings["hide_on_desktop"] = ($settings["hide_on_desktop"] == Null) ? "0" : $settings["hide_on_desktop"];

            $settings["hide_on_mobile"] = $this->updateSettings("hide_on_mobile");
            $settings["hide_on_mobile"] = ($settings["hide_on_mobile"] == Null) ? "0" : $settings["hide_on_mobile"];

            $settings["hide_on_mobile_by"] = $this->updateSettings("hide_on_mobile_by");
            $settings["hide_on_mobile_by"] = ($settings["hide_on_mobile_by"] == Null) ? "user_agent" : $settings["hide_on_mobile_by"];

            $settings["enable_switch_in_menu"] = $this->updateSettings("enable_switch_in_menu");
            $settings["enable_switch_in_menu"] = ($settings["enable_switch_in_menu"] == Null) ? "0" : $settings["enable_switch_in_menu"];

            $settings["switch_in_menu_location"] = $this->updateSettings("switch_in_menu_location");
            $settings["switch_in_menu_location"] = ($settings["switch_in_menu_location"] == Null) ? "0" : $settings["switch_in_menu_location"];

            $settings["switch_in_menu_shortcode"] = $this->updateSettings("switch_in_menu_shortcode");
            $settings["switch_in_menu_shortcode"] = ($settings["switch_in_menu_shortcode"] == Null) ? "[darkmysite switch=\"1\"]" : $settings["switch_in_menu_shortcode"];


            /* Admin */

            $settings["enable_admin_dark_mode"] = $this->updateSettings("enable_admin_dark_mode");
            $settings["enable_admin_dark_mode"] = ($settings["enable_admin_dark_mode"] == Null) ? "1" : $settings["enable_admin_dark_mode"];

            $settings["display_in_admin_settings_menu"] = $this->updateSettings("display_in_admin_settings_menu");
            $settings["display_in_admin_settings_menu"] = ($settings["display_in_admin_settings_menu"] == Null) ? "0" : $settings["display_in_admin_settings_menu"];

            $settings["disallowed_admin_pages"] = $this->updateSettings("disallowed_admin_pages");
            $settings["disallowed_admin_pages"] = ($settings["disallowed_admin_pages"] == Null) ? "" : $settings["disallowed_admin_pages"];


            /* Switch */

            $settings["dark_mode_switch_design"] = $this->updateSettings("dark_mode_switch_design");
            $settings["dark_mode_switch_design"] = ($settings["dark_mode_switch_design"] == Null) ? "apple" : $settings["dark_mode_switch_design"];

            $settings["dark_mode_switch_position"] = $this->updateSettings("dark_mode_switch_position");
            $settings["dark_mode_switch_position"] = ($settings["dark_mode_switch_position"] == Null) ? "bottom_right" : $settings["dark_mode_switch_position"];

            $settings["dark_mode_switch_margin_top"] = $this->updateSettings("dark_mode_switch_margin_top");
            $settings["dark_mode_switch_margin_top"] = ($settings["dark_mode_switch_margin_top"] == Null) ? "40" : $settings["dark_mode_switch_margin_top"];

            $settings["dark_mode_switch_margin_bottom"] = $this->updateSettings("dark_mode_switch_margin_bottom");
            $settings["dark_mode_switch_margin_bottom"] = ($settings["dark_mode_switch_margin_bottom"] == Null) ? "40" : $settings["dark_mode_switch_margin_bottom"];

            $settings["dark_mode_switch_margin_left"] = $this->updateSettings("dark_mode_switch_margin_left");
            $settings["dark_mode_switch_margin_left"] = ($settings["dark_mode_switch_margin_left"] == Null) ? "40" : $settings["dark_mode_switch_margin_left"];

            $settings["dark_mode_switch_margin_right"] = $this->updateSettings("dark_mode_switch_margin_right");
            $settings["dark_mode_switch_margin_right"] = ($settings["dark_mode_switch_margin_right"] == Null) ? "40" : $settings["dark_mode_switch_margin_right"];

            $settings["enable_switch_position_different_in_mobile"] = $this->updateSettings("enable_switch_position_different_in_mobile");
            $settings["enable_switch_position_different_in_mobile"] = ($settings["enable_switch_position_different_in_mobile"] == Null) ? "0" : $settings["enable_switch_position_different_in_mobile"];

            $settings["dark_mode_switch_position_in_mobile"] = $this->updateSettings("dark_mode_switch_position_in_mobile");
            $settings["dark_mode_switch_position_in_mobile"] = ($settings["dark_mode_switch_position_in_mobile"] == Null) ? "bottom_right" : $settings["dark_mode_switch_position_in_mobile"];

            $settings["dark_mode_switch_margin_top_in_mobile"] = $this->updateSettings("dark_mode_switch_margin_top_in_mobile");
            $settings["dark_mode_switch_margin_top_in_mobile"] = ($settings["dark_mode_switch_margin_top_in_mobile"] == Null) ? "40" : $settings["dark_mode_switch_margin_top_in_mobile"];

            $settings["dark_mode_switch_margin_bottom_in_mobile"] = $this->updateSettings("dark_mode_switch_margin_bottom_in_mobile");
            $settings["dark_mode_switch_margin_bottom_in_mobile"] = ($settings["dark_mode_switch_margin_bottom_in_mobile"] == Null) ? "40" : $settings["dark_mode_switch_margin_bottom_in_mobile"];

            $settings["dark_mode_switch_margin_left_in_mobile"] = $this->updateSettings("dark_mode_switch_margin_left_in_mobile");
            $settings["dark_mode_switch_margin_left_in_mobile"] = ($settings["dark_mode_switch_margin_left_in_mobile"] == Null) ? "40" : $settings["dark_mode_switch_margin_left_in_mobile"];

            $settings["dark_mode_switch_margin_right_in_mobile"] = $this->updateSettings("dark_mode_switch_margin_right_in_mobile");
            $settings["dark_mode_switch_margin_right_in_mobile"] = ($settings["dark_mode_switch_margin_right_in_mobile"] == Null) ? "40" : $settings["dark_mode_switch_margin_right_in_mobile"];

            $settings["enable_absolute_position"] = $this->updateSettings("enable_absolute_position");
            $settings["enable_absolute_position"] = ($settings["enable_absolute_position"] == Null) ? "0" : $settings["enable_absolute_position"];

            $settings["enable_switch_dragging"] = $this->updateSettings("enable_switch_dragging");
            $settings["enable_switch_dragging"] = ($settings["enable_switch_dragging"] == Null) ? "0" : $settings["enable_switch_dragging"];

            //========== Switch Extras ===============
            $settings["enable_floating_switch_tooltip"] = $this->updateSettings("enable_floating_switch_tooltip");
            $settings["enable_floating_switch_tooltip"] = ($settings["enable_floating_switch_tooltip"] == Null) ? "0" : $settings["enable_floating_switch_tooltip"];

            $settings["floating_switch_tooltip_position"] = $this->updateSettings("floating_switch_tooltip_position");
            $settings["floating_switch_tooltip_position"] = ($settings["floating_switch_tooltip_position"] == Null) ? "top" : $settings["floating_switch_tooltip_position"];

            $settings["floating_switch_tooltip_text"] = $this->updateSettings("floating_switch_tooltip_text");
            $settings["floating_switch_tooltip_text"] = ($settings["floating_switch_tooltip_text"] == Null) ? "Toggle Dark Mode" : $settings["floating_switch_tooltip_text"];

            $settings["floating_switch_tooltip_bg_color"] = $this->updateSettings("floating_switch_tooltip_bg_color");
            $settings["floating_switch_tooltip_bg_color"] = ($settings["floating_switch_tooltip_bg_color"] == Null) ? "#142434" : $settings["floating_switch_tooltip_bg_color"];

            $settings["floating_switch_tooltip_text_color"] = $this->updateSettings("floating_switch_tooltip_text_color");
            $settings["floating_switch_tooltip_text_color"] = ($settings["floating_switch_tooltip_text_color"] == Null) ? "#B0CBE7" : $settings["floating_switch_tooltip_text_color"];

            $settings["alternative_dark_mode_switch"] = $this->updateSettings("alternative_dark_mode_switch");
            $settings["alternative_dark_mode_switch"] = ($settings["alternative_dark_mode_switch"] == Null) ? "" : $settings["alternative_dark_mode_switch"];


            //========== Switch Apple ===============
            $settings["switch_apple_width_height"] = $this->updateSettings("switch_apple_width_height");
            $settings["switch_apple_width_height"] = ($settings["switch_apple_width_height"] == Null) ? "60" : $settings["switch_apple_width_height"];

            $settings["switch_apple_border_radius"] = $this->updateSettings("switch_apple_border_radius");
            $settings["switch_apple_border_radius"] = ($settings["switch_apple_border_radius"] == Null) ? "7" : $settings["switch_apple_border_radius"];

            $settings["switch_apple_icon_width"] = $this->updateSettings("switch_apple_icon_width");
            $settings["switch_apple_icon_width"] = ($settings["switch_apple_icon_width"] == Null) ? "30" : $settings["switch_apple_icon_width"];

            $settings["switch_apple_light_mode_bg"] = $this->updateSettings("switch_apple_light_mode_bg");
            $settings["switch_apple_light_mode_bg"] = ($settings["switch_apple_light_mode_bg"] == Null) ? "#121116" : $settings["switch_apple_light_mode_bg"];

            $settings["switch_apple_dark_mode_bg"] = $this->updateSettings("switch_apple_dark_mode_bg");
            $settings["switch_apple_dark_mode_bg"] = ($settings["switch_apple_dark_mode_bg"] == Null) ? "#ffffff" : $settings["switch_apple_dark_mode_bg"];

            $settings["switch_apple_light_mode_icon_color"] = $this->updateSettings("switch_apple_light_mode_icon_color");
            $settings["switch_apple_light_mode_icon_color"] = ($settings["switch_apple_light_mode_icon_color"] == Null) ? "#ffffff" : $settings["switch_apple_light_mode_icon_color"];

            $settings["switch_apple_dark_mode_icon_color"] = $this->updateSettings("switch_apple_dark_mode_icon_color");
            $settings["switch_apple_dark_mode_icon_color"] = ($settings["switch_apple_dark_mode_icon_color"] == Null) ? "#121116" : $settings["switch_apple_dark_mode_icon_color"];

            // ======== Switch Banana ===========
            $settings["switch_banana_width_height"] = $this->updateSettings("switch_banana_width_height");
            $settings["switch_banana_width_height"] = ($settings["switch_banana_width_height"] == Null) ? "60" : $settings["switch_banana_width_height"];

            $settings["switch_banana_border_radius"] = $this->updateSettings("switch_banana_border_radius");
            $settings["switch_banana_border_radius"] = ($settings["switch_banana_border_radius"] == Null) ? "7" : $settings["switch_banana_border_radius"];

            $settings["switch_banana_icon_width"] = $this->updateSettings("switch_banana_icon_width");
            $settings["switch_banana_icon_width"] = ($settings["switch_banana_icon_width"] == Null) ? "38" : $settings["switch_banana_icon_width"];

            $settings["switch_banana_light_mode_bg"] = $this->updateSettings("switch_banana_light_mode_bg");
            $settings["switch_banana_light_mode_bg"] = ($settings["switch_banana_light_mode_bg"] == Null) ? "#121116" : $settings["switch_banana_light_mode_bg"];

            $settings["switch_banana_dark_mode_bg"] = $this->updateSettings("switch_banana_dark_mode_bg");
            $settings["switch_banana_dark_mode_bg"] = ($settings["switch_banana_dark_mode_bg"] == Null) ? "#ffffff" : $settings["switch_banana_dark_mode_bg"];

            $settings["switch_banana_light_mode_icon_color"] = $this->updateSettings("switch_banana_light_mode_icon_color");
            $settings["switch_banana_light_mode_icon_color"] = ($settings["switch_banana_light_mode_icon_color"] == Null) ? "#ffffff" : $settings["switch_banana_light_mode_icon_color"];

            $settings["switch_banana_dark_mode_icon_color"] = $this->updateSettings("switch_banana_dark_mode_icon_color");
            $settings["switch_banana_dark_mode_icon_color"] = ($settings["switch_banana_dark_mode_icon_color"] == Null) ? "#121116" : $settings["switch_banana_dark_mode_icon_color"];

            //========== Switch Cherry ===============
            $settings["switch_cherry_width_height"] = $this->updateSettings("switch_cherry_width_height");
            $settings["switch_cherry_width_height"] = ($settings["switch_cherry_width_height"] == Null) ? "60" : $settings["switch_cherry_width_height"];

            $settings["switch_cherry_border_radius"] = $this->updateSettings("switch_cherry_border_radius");
            $settings["switch_cherry_border_radius"] = ($settings["switch_cherry_border_radius"] == Null) ? "7" : $settings["switch_cherry_border_radius"];

            $settings["switch_cherry_icon_width"] = $this->updateSettings("switch_cherry_icon_width");
            $settings["switch_cherry_icon_width"] = ($settings["switch_cherry_icon_width"] == Null) ? "30" : $settings["switch_cherry_icon_width"];

            $settings["switch_cherry_light_mode_bg"] = $this->updateSettings("switch_cherry_light_mode_bg");
            $settings["switch_cherry_light_mode_bg"] = ($settings["switch_cherry_light_mode_bg"] == Null) ? "#121116" : $settings["switch_cherry_light_mode_bg"];

            $settings["switch_cherry_dark_mode_bg"] = $this->updateSettings("switch_cherry_dark_mode_bg");
            $settings["switch_cherry_dark_mode_bg"] = ($settings["switch_cherry_dark_mode_bg"] == Null) ? "#ffffff" : $settings["switch_cherry_dark_mode_bg"];

            $settings["switch_cherry_light_mode_icon_color"] = $this->updateSettings("switch_cherry_light_mode_icon_color");
            $settings["switch_cherry_light_mode_icon_color"] = ($settings["switch_cherry_light_mode_icon_color"] == Null) ? "#ffffff" : $settings["switch_cherry_light_mode_icon_color"];

            $settings["switch_cherry_dark_mode_icon_color"] = $this->updateSettings("switch_cherry_dark_mode_icon_color");
            $settings["switch_cherry_dark_mode_icon_color"] = ($settings["switch_cherry_dark_mode_icon_color"] == Null) ? "#121116" : $settings["switch_cherry_dark_mode_icon_color"];

            //========== Switch Durian ===============
            $settings["switch_durian_width_height"] = $this->updateSettings("switch_durian_width_height");
            $settings["switch_durian_width_height"] = ($settings["switch_durian_width_height"] == Null) ? "60" : $settings["switch_durian_width_height"];

            $settings["switch_durian_border_size"] = $this->updateSettings("switch_durian_border_size");
            $settings["switch_durian_border_size"] = ($settings["switch_durian_border_size"] == Null) ? "2" : $settings["switch_durian_border_size"];

            $settings["switch_durian_border_radius"] = $this->updateSettings("switch_durian_border_radius");
            $settings["switch_durian_border_radius"] = ($settings["switch_durian_border_radius"] == Null) ? "7" : $settings["switch_durian_border_radius"];

            $settings["switch_durian_icon_width"] = $this->updateSettings("switch_durian_icon_width");
            $settings["switch_durian_icon_width"] = ($settings["switch_durian_icon_width"] == Null) ? "38" : $settings["switch_durian_icon_width"];

            $settings["switch_durian_light_mode_bg"] = $this->updateSettings("switch_durian_light_mode_bg");
            $settings["switch_durian_light_mode_bg"] = ($settings["switch_durian_light_mode_bg"] == Null) ? "#ffffff" : $settings["switch_durian_light_mode_bg"];

            $settings["switch_durian_dark_mode_bg"] = $this->updateSettings("switch_durian_dark_mode_bg");
            $settings["switch_durian_dark_mode_bg"] = ($settings["switch_durian_dark_mode_bg"] == Null) ? "#121116" : $settings["switch_durian_dark_mode_bg"];

            $settings["switch_durian_light_mode_icon_and_border_color"] = $this->updateSettings("switch_durian_light_mode_icon_and_border_color");
            $settings["switch_durian_light_mode_icon_and_border_color"] = ($settings["switch_durian_light_mode_icon_and_border_color"] == Null) ? "#121116" : $settings["switch_durian_light_mode_icon_and_border_color"];

            $settings["switch_durian_dark_mode_icon_and_border_color"] = $this->updateSettings("switch_durian_dark_mode_icon_and_border_color");
            $settings["switch_durian_dark_mode_icon_and_border_color"] = ($settings["switch_durian_dark_mode_icon_and_border_color"] == Null) ? "#ffffff" : $settings["switch_durian_dark_mode_icon_and_border_color"];


             //========== Switch Elderberry ===============
            $settings["switch_elderberry_width"] = $this->updateSettings("switch_elderberry_width");
            $settings["switch_elderberry_width"] = ($settings["switch_elderberry_width"] == Null) ? "100" : $settings["switch_elderberry_width"];

            $settings["switch_elderberry_height"] = $this->updateSettings("switch_elderberry_height");
            $settings["switch_elderberry_height"] = ($settings["switch_elderberry_height"] == Null) ? "40" : $settings["switch_elderberry_height"];

            $settings["switch_elderberry_icon_plate_width"] = $this->updateSettings("switch_elderberry_icon_plate_width");
            $settings["switch_elderberry_icon_plate_width"] = ($settings["switch_elderberry_icon_plate_width"] == Null) ? "50" : $settings["switch_elderberry_icon_plate_width"];

            $settings["switch_elderberry_icon_plate_border_size"] = $this->updateSettings("switch_elderberry_icon_plate_border_size");
            $settings["switch_elderberry_icon_plate_border_size"] = ($settings["switch_elderberry_icon_plate_border_size"] == Null) ? "2" : $settings["switch_elderberry_icon_plate_border_size"];

            $settings["switch_elderberry_icon_width"] = $this->updateSettings("switch_elderberry_icon_width");
            $settings["switch_elderberry_icon_width"] = ($settings["switch_elderberry_icon_width"] == Null) ? "28" : $settings["switch_elderberry_icon_width"];

            $settings["switch_elderberry_light_mode_bg"] = $this->updateSettings("switch_elderberry_light_mode_bg");
            $settings["switch_elderberry_light_mode_bg"] = ($settings["switch_elderberry_light_mode_bg"] == Null) ? "#121116" : $settings["switch_elderberry_light_mode_bg"];

            $settings["switch_elderberry_dark_mode_bg"] = $this->updateSettings("switch_elderberry_dark_mode_bg");
            $settings["switch_elderberry_dark_mode_bg"] = ($settings["switch_elderberry_dark_mode_bg"] == Null) ? "#ffffff" : $settings["switch_elderberry_dark_mode_bg"];

            $settings["switch_elderberry_light_mode_icon_plate_bg"] = $this->updateSettings("switch_elderberry_light_mode_icon_plate_bg");
            $settings["switch_elderberry_light_mode_icon_plate_bg"] = ($settings["switch_elderberry_light_mode_icon_plate_bg"] == Null) ? "#ffffff" : $settings["switch_elderberry_light_mode_icon_plate_bg"];

            $settings["switch_elderberry_dark_mode_icon_plate_bg"] = $this->updateSettings("switch_elderberry_dark_mode_icon_plate_bg");
            $settings["switch_elderberry_dark_mode_icon_plate_bg"] = ($settings["switch_elderberry_dark_mode_icon_plate_bg"] == Null) ? "#121116" : $settings["switch_elderberry_dark_mode_icon_plate_bg"];

            $settings["switch_elderberry_light_mode_icon_color"] = $this->updateSettings("switch_elderberry_light_mode_icon_color");
            $settings["switch_elderberry_light_mode_icon_color"] = ($settings["switch_elderberry_light_mode_icon_color"] == Null) ? "#121116" : $settings["switch_elderberry_light_mode_icon_color"];

            $settings["switch_elderberry_dark_mode_icon_color"] = $this->updateSettings("switch_elderberry_dark_mode_icon_color");
            $settings["switch_elderberry_dark_mode_icon_color"] = ($settings["switch_elderberry_dark_mode_icon_color"] == Null) ? "#ffffff" : $settings["switch_elderberry_dark_mode_icon_color"];

            //========== Switch Fazli ===============
            $settings["switch_fazli_width"] = $this->updateSettings("switch_fazli_width");
            $settings["switch_fazli_width"] = ($settings["switch_fazli_width"] == Null) ? "100" : $settings["switch_fazli_width"];

            $settings["switch_fazli_height"] = $this->updateSettings("switch_fazli_height");
            $settings["switch_fazli_height"] = ($settings["switch_fazli_height"] == Null) ? "40" : $settings["switch_fazli_height"];

            $settings["switch_fazli_icon_plate_width"] = $this->updateSettings("switch_fazli_icon_plate_width");
            $settings["switch_fazli_icon_plate_width"] = ($settings["switch_fazli_icon_plate_width"] == Null) ? "50" : $settings["switch_fazli_icon_plate_width"];

            $settings["switch_fazli_icon_plate_border_size"] = $this->updateSettings("switch_fazli_icon_plate_border_size");
            $settings["switch_fazli_icon_plate_border_size"] = ($settings["switch_fazli_icon_plate_border_size"] == Null) ? "2" : $settings["switch_fazli_icon_plate_border_size"];

            $settings["switch_fazli_icon_width"] = $this->updateSettings("switch_fazli_icon_width");
            $settings["switch_fazli_icon_width"] = ($settings["switch_fazli_icon_width"] == Null) ? "30" : $settings["switch_fazli_icon_width"];

            $settings["switch_fazli_light_mode_bg"] = $this->updateSettings("switch_fazli_light_mode_bg");
            $settings["switch_fazli_light_mode_bg"] = ($settings["switch_fazli_light_mode_bg"] == Null) ? "#121116" : $settings["switch_fazli_light_mode_bg"];

            $settings["switch_fazli_dark_mode_bg"] = $this->updateSettings("switch_fazli_dark_mode_bg");
            $settings["switch_fazli_dark_mode_bg"] = ($settings["switch_fazli_dark_mode_bg"] == Null) ? "#ffffff" : $settings["switch_fazli_dark_mode_bg"];

            $settings["switch_fazli_light_mode_icon_plate_bg"] = $this->updateSettings("switch_fazli_light_mode_icon_plate_bg");
            $settings["switch_fazli_light_mode_icon_plate_bg"] = ($settings["switch_fazli_light_mode_icon_plate_bg"] == Null) ? "#ffffff" : $settings["switch_fazli_light_mode_icon_plate_bg"];

            $settings["switch_fazli_dark_mode_icon_plate_bg"] = $this->updateSettings("switch_fazli_dark_mode_icon_plate_bg");
            $settings["switch_fazli_dark_mode_icon_plate_bg"] = ($settings["switch_fazli_dark_mode_icon_plate_bg"] == Null) ? "#121116" : $settings["switch_fazli_dark_mode_icon_plate_bg"];

            $settings["switch_fazli_light_mode_icon_color"] = $this->updateSettings("switch_fazli_light_mode_icon_color");
            $settings["switch_fazli_light_mode_icon_color"] = ($settings["switch_fazli_light_mode_icon_color"] == Null) ? "#121116" : $settings["switch_fazli_light_mode_icon_color"];

            $settings["switch_fazli_dark_mode_icon_color"] = $this->updateSettings("switch_fazli_dark_mode_icon_color");
            $settings["switch_fazli_dark_mode_icon_color"] = ($settings["switch_fazli_dark_mode_icon_color"] == Null) ? "#ffffff" : $settings["switch_fazli_dark_mode_icon_color"];

            //========== Switch Guava ===============
            $settings["switch_guava_width"] = $this->updateSettings("switch_guava_width");
            $settings["switch_guava_width"] = ($settings["switch_guava_width"] == Null) ? "100" : $settings["switch_guava_width"];

            $settings["switch_guava_height"] = $this->updateSettings("switch_guava_height");
            $settings["switch_guava_height"] = ($settings["switch_guava_height"] == Null) ? "40" : $settings["switch_guava_height"];

            $settings["switch_guava_icon_width"] = $this->updateSettings("switch_guava_icon_width");
            $settings["switch_guava_icon_width"] = ($settings["switch_guava_icon_width"] == Null) ? "26" : $settings["switch_guava_icon_width"];

            $settings["switch_guava_icon_margin"] = $this->updateSettings("switch_guava_icon_margin");
            $settings["switch_guava_icon_margin"] = ($settings["switch_guava_icon_margin"] == Null) ? "10" : $settings["switch_guava_icon_margin"];

            $settings["switch_guava_light_mode_bg"] = $this->updateSettings("switch_guava_light_mode_bg");
            $settings["switch_guava_light_mode_bg"] = ($settings["switch_guava_light_mode_bg"] == Null) ? "#121116" : $settings["switch_guava_light_mode_bg"];

            $settings["switch_guava_dark_mode_bg"] = $this->updateSettings("switch_guava_dark_mode_bg");
            $settings["switch_guava_dark_mode_bg"] = ($settings["switch_guava_dark_mode_bg"] == Null) ? "#ffffff" : $settings["switch_guava_dark_mode_bg"];

            $settings["switch_guava_light_mode_icon_color"] = $this->updateSettings("switch_guava_light_mode_icon_color");
            $settings["switch_guava_light_mode_icon_color"] = ($settings["switch_guava_light_mode_icon_color"] == Null) ? "#ffffff" : $settings["switch_guava_light_mode_icon_color"];

            $settings["switch_guava_dark_mode_icon_color"] = $this->updateSettings("switch_guava_dark_mode_icon_color");
            $settings["switch_guava_dark_mode_icon_color"] = ($settings["switch_guava_dark_mode_icon_color"] == Null) ? "#121116" : $settings["switch_guava_dark_mode_icon_color"];

            //========== Switch Honeydew ===============
            $settings["switch_honeydew_width"] = $this->updateSettings("switch_honeydew_width");
            $settings["switch_honeydew_width"] = ($settings["switch_honeydew_width"] == Null) ? "100" : $settings["switch_honeydew_width"];

            $settings["switch_honeydew_height"] = $this->updateSettings("switch_honeydew_height");
            $settings["switch_honeydew_height"] = ($settings["switch_honeydew_height"] == Null) ? "40" : $settings["switch_honeydew_height"];

            $settings["switch_honeydew_icon_plate_width"] = $this->updateSettings("switch_honeydew_icon_plate_width");
            $settings["switch_honeydew_icon_plate_width"] = ($settings["switch_honeydew_icon_plate_width"] == Null) ? "32" : $settings["switch_honeydew_icon_plate_width"];

            $settings["switch_honeydew_icon_plate_margin"] = $this->updateSettings("switch_honeydew_icon_plate_margin");
            $settings["switch_honeydew_icon_plate_margin"] = ($settings["switch_honeydew_icon_plate_margin"] == Null) ? "5" : $settings["switch_honeydew_icon_plate_margin"];

            $settings["switch_honeydew_icon_width"] = $this->updateSettings("switch_honeydew_icon_width");
            $settings["switch_honeydew_icon_width"] = ($settings["switch_honeydew_icon_width"] == Null) ? "22" : $settings["switch_honeydew_icon_width"];

            $settings["switch_honeydew_light_mode_icon_plate_bg"] = $this->updateSettings("switch_honeydew_light_mode_icon_plate_bg");
            $settings["switch_honeydew_light_mode_icon_plate_bg"] = ($settings["switch_honeydew_light_mode_icon_plate_bg"] == Null) ? "#ffffff" : $settings["switch_honeydew_light_mode_icon_plate_bg"];

            $settings["switch_honeydew_dark_mode_icon_plate_bg"] = $this->updateSettings("switch_honeydew_dark_mode_icon_plate_bg");
            $settings["switch_honeydew_dark_mode_icon_plate_bg"] = ($settings["switch_honeydew_dark_mode_icon_plate_bg"] == Null) ? "#ffffff" : $settings["switch_honeydew_dark_mode_icon_plate_bg"];

            $settings["switch_honeydew_light_mode_icon_color"] = $this->updateSettings("switch_honeydew_light_mode_icon_color");
            $settings["switch_honeydew_light_mode_icon_color"] = ($settings["switch_honeydew_light_mode_icon_color"] == Null) ? "#121116" : $settings["switch_honeydew_light_mode_icon_color"];

            $settings["switch_honeydew_dark_mode_icon_color"] = $this->updateSettings("switch_honeydew_dark_mode_icon_color");
            $settings["switch_honeydew_dark_mode_icon_color"] = ($settings["switch_honeydew_dark_mode_icon_color"] == Null) ? "#121116" : $settings["switch_honeydew_dark_mode_icon_color"];

            //========== Switch Incaberry ===============
            $settings["switch_incaberry_width"] = $this->updateSettings("switch_incaberry_width");
            $settings["switch_incaberry_width"] = ($settings["switch_incaberry_width"] == Null) ? "100" : $settings["switch_incaberry_width"];

            $settings["switch_incaberry_height"] = $this->updateSettings("switch_incaberry_height");
            $settings["switch_incaberry_height"] = ($settings["switch_incaberry_height"] == Null) ? "40" : $settings["switch_incaberry_height"];

            $settings["switch_incaberry_icon_plate_width"] = $this->updateSettings("switch_incaberry_icon_plate_width");
            $settings["switch_incaberry_icon_plate_width"] = ($settings["switch_incaberry_icon_plate_width"] == Null) ? "32" : $settings["switch_incaberry_icon_plate_width"];

            $settings["switch_incaberry_icon_plate_margin"] = $this->updateSettings("switch_incaberry_icon_plate_margin");
            $settings["switch_incaberry_icon_plate_margin"] = ($settings["switch_incaberry_icon_plate_margin"] == Null) ? "5" : $settings["switch_incaberry_icon_plate_margin"];

            $settings["switch_incaberry_icon_width"] = $this->updateSettings("switch_incaberry_icon_width");
            $settings["switch_incaberry_icon_width"] = ($settings["switch_incaberry_icon_width"] == Null) ? "22" : $settings["switch_incaberry_icon_width"];

            $settings["switch_incaberry_light_mode_icon_plate_bg"] = $this->updateSettings("switch_incaberry_light_mode_icon_plate_bg");
            $settings["switch_incaberry_light_mode_icon_plate_bg"] = ($settings["switch_incaberry_light_mode_icon_plate_bg"] == Null) ? "#ffffff" : $settings["switch_incaberry_light_mode_icon_plate_bg"];

            $settings["switch_incaberry_dark_mode_icon_plate_bg"] = $this->updateSettings("switch_incaberry_dark_mode_icon_plate_bg");
            $settings["switch_incaberry_dark_mode_icon_plate_bg"] = ($settings["switch_incaberry_dark_mode_icon_plate_bg"] == Null) ? "#ffffff" : $settings["switch_incaberry_dark_mode_icon_plate_bg"];

            $settings["switch_incaberry_light_mode_icon_color"] = $this->updateSettings("switch_incaberry_light_mode_icon_color");
            $settings["switch_incaberry_light_mode_icon_color"] = ($settings["switch_incaberry_light_mode_icon_color"] == Null) ? "#121116" : $settings["switch_incaberry_light_mode_icon_color"];

            $settings["switch_incaberry_dark_mode_icon_color"] = $this->updateSettings("switch_incaberry_dark_mode_icon_color");
            $settings["switch_incaberry_dark_mode_icon_color"] = ($settings["switch_incaberry_dark_mode_icon_color"] == Null) ? "#121116" : $settings["switch_incaberry_dark_mode_icon_color"];

            //========== Switch Jackfruit ===============
            $settings["switch_jackfruit_width"] = $this->updateSettings("switch_jackfruit_width");
            $settings["switch_jackfruit_width"] = ($settings["switch_jackfruit_width"] == Null) ? "80" : $settings["switch_jackfruit_width"];

            $settings["switch_jackfruit_height"] = $this->updateSettings("switch_jackfruit_height");
            $settings["switch_jackfruit_height"] = ($settings["switch_jackfruit_height"] == Null) ? "40" : $settings["switch_jackfruit_height"];

            $settings["switch_jackfruit_icon_plate_width"] = $this->updateSettings("switch_jackfruit_icon_plate_width");
            $settings["switch_jackfruit_icon_plate_width"] = ($settings["switch_jackfruit_icon_plate_width"] == Null) ? "30" : $settings["switch_jackfruit_icon_plate_width"];

            $settings["switch_jackfruit_icon_plate_margin"] = $this->updateSettings("switch_jackfruit_icon_plate_margin");
            $settings["switch_jackfruit_icon_plate_margin"] = ($settings["switch_jackfruit_icon_plate_margin"] == Null) ? "5" : $settings["switch_jackfruit_icon_plate_margin"];

            $settings["switch_jackfruit_icon_width"] = $this->updateSettings("switch_jackfruit_icon_width");
            $settings["switch_jackfruit_icon_width"] = ($settings["switch_jackfruit_icon_width"] == Null) ? "18" : $settings["switch_jackfruit_icon_width"];

            $settings["switch_jackfruit_light_mode_bg"] = $this->updateSettings("switch_jackfruit_light_mode_bg");
            $settings["switch_jackfruit_light_mode_bg"] = ($settings["switch_jackfruit_light_mode_bg"] == Null) ? "#121116" : $settings["switch_jackfruit_light_mode_bg"];

            $settings["switch_jackfruit_dark_mode_bg"] = $this->updateSettings("switch_jackfruit_dark_mode_bg");
            $settings["switch_jackfruit_dark_mode_bg"] = ($settings["switch_jackfruit_dark_mode_bg"] == Null) ? "#ffffff" : $settings["switch_jackfruit_dark_mode_bg"];

            $settings["switch_jackfruit_light_mode_icon_plate_bg"] = $this->updateSettings("switch_jackfruit_light_mode_icon_plate_bg");
            $settings["switch_jackfruit_light_mode_icon_plate_bg"] = ($settings["switch_jackfruit_light_mode_icon_plate_bg"] == Null) ? "#ffffff" : $settings["switch_jackfruit_light_mode_icon_plate_bg"];

            $settings["switch_jackfruit_dark_mode_icon_plate_bg"] = $this->updateSettings("switch_jackfruit_dark_mode_icon_plate_bg");
            $settings["switch_jackfruit_dark_mode_icon_plate_bg"] = ($settings["switch_jackfruit_dark_mode_icon_plate_bg"] == Null) ? "#121116" : $settings["switch_jackfruit_dark_mode_icon_plate_bg"];

            $settings["switch_jackfruit_light_mode_light_icon_color"] = $this->updateSettings("switch_jackfruit_light_mode_light_icon_color");
            $settings["switch_jackfruit_light_mode_light_icon_color"] = ($settings["switch_jackfruit_light_mode_light_icon_color"] == Null) ? "#121116" : $settings["switch_jackfruit_light_mode_light_icon_color"];

            $settings["switch_jackfruit_light_mode_dark_icon_color"] = $this->updateSettings("switch_jackfruit_light_mode_dark_icon_color");
            $settings["switch_jackfruit_light_mode_dark_icon_color"] = ($settings["switch_jackfruit_light_mode_dark_icon_color"] == Null) ? "#ffffff" : $settings["switch_jackfruit_light_mode_dark_icon_color"];

            $settings["switch_jackfruit_dark_mode_light_icon_color"] = $this->updateSettings("switch_jackfruit_dark_mode_light_icon_color");
            $settings["switch_jackfruit_dark_mode_light_icon_color"] = ($settings["switch_jackfruit_dark_mode_light_icon_color"] == Null) ? "#121116" : $settings["switch_jackfruit_dark_mode_light_icon_color"];

            $settings["switch_jackfruit_dark_mode_dark_icon_color"] = $this->updateSettings("switch_jackfruit_dark_mode_dark_icon_color");
            $settings["switch_jackfruit_dark_mode_dark_icon_color"] = ($settings["switch_jackfruit_dark_mode_dark_icon_color"] == Null) ? "#ffffff" : $settings["switch_jackfruit_dark_mode_dark_icon_color"];



            /* Preset */

            $settings["dark_mode_color_preset"] = $this->updateSettings("dark_mode_color_preset");
            $settings["dark_mode_color_preset"] = ($settings["dark_mode_color_preset"] == Null) ? "black" : $settings["dark_mode_color_preset"];

            $settings["dark_mode_bg"] = $this->updateSettings("dark_mode_bg");
            $settings["dark_mode_bg"] = ($settings["dark_mode_bg"] == Null) ? "#0F0F0F" : $settings["dark_mode_bg"];

            $settings["dark_mode_secondary_bg"] = $this->updateSettings("dark_mode_secondary_bg");
            $settings["dark_mode_secondary_bg"] = ($settings["dark_mode_secondary_bg"] == Null) ? "#171717" : $settings["dark_mode_secondary_bg"];

            $settings["dark_mode_text_color"] = $this->updateSettings("dark_mode_text_color");
            $settings["dark_mode_text_color"] = ($settings["dark_mode_text_color"] == Null) ? "#BEBEBE" : $settings["dark_mode_text_color"];

            $settings["dark_mode_link_color"] = $this->updateSettings("dark_mode_link_color");
            $settings["dark_mode_link_color"] = ($settings["dark_mode_link_color"] == Null) ? "#FFFFFF" : $settings["dark_mode_link_color"];

            $settings["dark_mode_link_hover_color"] = $this->updateSettings("dark_mode_link_hover_color");
            $settings["dark_mode_link_hover_color"] = ($settings["dark_mode_link_hover_color"] == Null) ? "#CCCCCC" : $settings["dark_mode_link_hover_color"];

            $settings["dark_mode_input_bg"] = $this->updateSettings("dark_mode_input_bg");
            $settings["dark_mode_input_bg"] = ($settings["dark_mode_input_bg"] == Null) ? "#2D2D2D" : $settings["dark_mode_input_bg"];

            $settings["dark_mode_input_text_color"] = $this->updateSettings("dark_mode_input_text_color");
            $settings["dark_mode_input_text_color"] = ($settings["dark_mode_input_text_color"] == Null) ? "#BEBEBE" : $settings["dark_mode_input_text_color"];

            $settings["dark_mode_input_placeholder_color"] = $this->updateSettings("dark_mode_input_placeholder_color");
            $settings["dark_mode_input_placeholder_color"] = ($settings["dark_mode_input_placeholder_color"] == Null) ? "#989898" : $settings["dark_mode_input_placeholder_color"];

            $settings["dark_mode_border_color"] = $this->updateSettings("dark_mode_border_color");
            $settings["dark_mode_border_color"] = ($settings["dark_mode_border_color"] == Null) ? "#4A4A4A" : $settings["dark_mode_border_color"];

            $settings["dark_mode_btn_bg"] = $this->updateSettings("dark_mode_btn_bg");
            $settings["dark_mode_btn_bg"] = ($settings["dark_mode_btn_bg"] == Null) ? "#2D2D2D" : $settings["dark_mode_btn_bg"];

            $settings["dark_mode_btn_text_color"] = $this->updateSettings("dark_mode_btn_text_color");
            $settings["dark_mode_btn_text_color"] = ($settings["dark_mode_btn_text_color"] == Null) ? "#BEBEBE" : $settings["dark_mode_btn_text_color"];

            $settings["enable_scrollbar_dark"] = $this->updateSettings("enable_scrollbar_dark");
            $settings["enable_scrollbar_dark"] = ($settings["enable_scrollbar_dark"] == Null) ? "1" : $settings["enable_scrollbar_dark"];

            $settings["dark_mode_scrollbar_track_bg"] = $this->updateSettings("dark_mode_scrollbar_track_bg");
            $settings["dark_mode_scrollbar_track_bg"] = ($settings["dark_mode_scrollbar_track_bg"] == Null) ? "#29292a" : $settings["dark_mode_scrollbar_track_bg"];

            $settings["dark_mode_scrollbar_thumb_bg"] = $this->updateSettings("dark_mode_scrollbar_thumb_bg");
            $settings["dark_mode_scrollbar_thumb_bg"] = ($settings["dark_mode_scrollbar_thumb_bg"] == Null) ? "#52565a" : $settings["dark_mode_scrollbar_thumb_bg"];



            /* Media */

            $settings["enable_low_image_brightness"] = $this->updateSettings("enable_low_image_brightness");
            $settings["enable_low_image_brightness"] = ($settings["enable_low_image_brightness"] == Null) ? "1" : $settings["enable_low_image_brightness"];

            $settings["image_brightness_to"] = $this->updateSettings("image_brightness_to");
            $settings["image_brightness_to"] = ($settings["image_brightness_to"] == Null) ? "80" : $settings["image_brightness_to"];

            $settings["disallowed_low_brightness_images"] = $this->updateSettings("disallowed_low_brightness_images");
            $settings["disallowed_low_brightness_images"] = ($settings["disallowed_low_brightness_images"] == Null) ? "" : $settings["disallowed_low_brightness_images"];

            $settings["enable_image_grayscale"] = $this->updateSettings("enable_image_grayscale");
            $settings["enable_image_grayscale"] = ($settings["enable_image_grayscale"] == Null) ? "0" : $settings["enable_image_grayscale"];

            $settings["image_grayscale_to"] = $this->updateSettings("image_grayscale_to");
            $settings["image_grayscale_to"] = ($settings["image_grayscale_to"] == Null) ? "80" : $settings["image_grayscale_to"];

            $settings["disallowed_grayscale_images"] = $this->updateSettings("disallowed_grayscale_images");
            $settings["disallowed_grayscale_images"] = ($settings["disallowed_grayscale_images"] == Null) ? "" : $settings["disallowed_grayscale_images"];

            $settings["enable_bg_image_darken"] = $this->updateSettings("enable_bg_image_darken");
            $settings["enable_bg_image_darken"] = ($settings["enable_bg_image_darken"] == Null) ? "1" : $settings["enable_bg_image_darken"];

            $settings["bg_image_darken_to"] = $this->updateSettings("bg_image_darken_to");
            $settings["bg_image_darken_to"] = ($settings["bg_image_darken_to"] == Null) ? "60" : $settings["bg_image_darken_to"];

            $settings["enable_invert_inline_svg"] = $this->updateSettings("enable_invert_inline_svg");
            $settings["enable_invert_inline_svg"] = ($settings["enable_invert_inline_svg"] == Null) ? "0" : $settings["enable_invert_inline_svg"];

            $settings["enable_invert_images"] = $this->updateSettings("enable_invert_images");
            $settings["enable_invert_images"] = ($settings["enable_invert_images"] == Null) ? "0" : $settings["enable_invert_images"];

            $settings["invert_images_allowed_urls"] = $this->updateSettings("invert_images_allowed_urls");
            $settings["invert_images_allowed_urls"] = ($settings["invert_images_allowed_urls"] == Null) ? "[]" : $settings["invert_images_allowed_urls"];

            $settings["image_replacements"] = $this->updateSettings("image_replacements");
            $settings["image_replacements"] = ($settings["image_replacements"] == Null) ? "[]" : $settings["image_replacements"];


            /* Video */

            $settings["enable_low_video_brightness"] = $this->updateSettings("enable_low_video_brightness");
            $settings["enable_low_video_brightness"] = ($settings["enable_low_video_brightness"] == Null) ? "1" : $settings["enable_low_video_brightness"];

            $settings["video_brightness_to"] = $this->updateSettings("video_brightness_to");
            $settings["video_brightness_to"] = ($settings["video_brightness_to"] == Null) ? "80" : $settings["video_brightness_to"];

            $settings["enable_video_grayscale"] = $this->updateSettings("enable_video_grayscale");
            $settings["enable_video_grayscale"] = ($settings["enable_video_grayscale"] == Null) ? "0" : $settings["enable_video_grayscale"];

            $settings["video_grayscale_to"] = $this->updateSettings("video_grayscale_to");
            $settings["video_grayscale_to"] = ($settings["video_grayscale_to"] == Null) ? "80" : $settings["video_grayscale_to"];

            $settings["video_replacements"] = $this->updateSettings("video_replacements");
            $settings["video_replacements"] = ($settings["video_replacements"] == Null) ? "[]" : $settings["video_replacements"];



            /* Restriction */

            $settings["allowed_elements"] = $this->updateSettings("allowed_elements");
            $settings["allowed_elements"] = ($settings["allowed_elements"] == Null) ? "" : $settings["allowed_elements"];

            $settings["allowed_elements_force_to_correct"] = $this->updateSettings("allowed_elements_force_to_correct");
            $settings["allowed_elements_force_to_correct"] = ($settings["allowed_elements_force_to_correct"] == Null) ? "1" : $settings["allowed_elements_force_to_correct"];

            $settings["disallowed_elements"] = $this->updateSettings("disallowed_elements");
            $settings["disallowed_elements"] = ($settings["disallowed_elements"] == Null) ? "" : $settings["disallowed_elements"];

            $settings["disallowed_elements_force_to_correct"] = $this->updateSettings("disallowed_elements_force_to_correct");
            $settings["disallowed_elements_force_to_correct"] = ($settings["disallowed_elements_force_to_correct"] == Null) ? "1" : $settings["disallowed_elements_force_to_correct"];

            $settings["allowed_pages"] = $this->updateSettings("allowed_pages");
            $settings["allowed_pages"] = ($settings["allowed_pages"] == Null) ? array() : explode(",", $settings["allowed_pages"]);

            $settings["disallowed_pages"] = $this->updateSettings("disallowed_pages");
            $settings["disallowed_pages"] = ($settings["disallowed_pages"] == Null) ? array() : explode(",", $settings["disallowed_pages"]);

            $settings["allowed_posts"] = $this->updateSettings("allowed_posts");
            $settings["allowed_posts"] = ($settings["allowed_posts"] == Null) ? array() : explode(",", $settings["allowed_posts"]);

            $settings["disallowed_posts"] = $this->updateSettings("disallowed_posts");
            $settings["disallowed_posts"] = ($settings["disallowed_posts"] == Null) ? array() : explode(",", $settings["disallowed_posts"]);

            $settings["custom_css"] = $this->updateSettings("custom_css");
            $settings["custom_css"] = ($settings["custom_css"] == Null) ? "" : $settings["custom_css"];

            $settings["custom_css_apply_on_children"] = $this->updateSettings("custom_css_apply_on_children");
            $settings["custom_css_apply_on_children"] = ($settings["custom_css_apply_on_children"] == Null) ? "1" : $settings["custom_css_apply_on_children"];

            $settings["normal_custom_css"] = $this->updateSettings("normal_custom_css");
            $settings["normal_custom_css"] = ($settings["normal_custom_css"] == Null) ? "" : $settings["normal_custom_css"];


            /* License */

            $settings["enable_support_team_modify_settings"] = $this->updateSettings("enable_support_team_modify_settings");
            $settings["enable_support_team_modify_settings"] = ($settings["enable_support_team_modify_settings"] == Null) ? "0" : $settings["enable_support_team_modify_settings"];

            return $settings;
        }


    }

}