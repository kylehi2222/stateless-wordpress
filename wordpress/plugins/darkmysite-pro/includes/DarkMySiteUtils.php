<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

if ( ! class_exists( 'DarkMySiteUtils' ) ) {
    class DarkMySiteUtils
    {

        public $base_admin;
        public function __construct($base_admin)
        {
            $this->base_admin = $base_admin;
        }


        public function isMobile() {
            if(function_exists("wp_is_mobile")){
                return wp_is_mobile();
            }else{
                return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
            }
        }
        public function is_hidden_by_user_agent($hide_on_desktop, $hide_on_mobile, $hide_on_mobile_by) {
            if($this->isMobile()){
                if($hide_on_mobile == "1"){
                    if($hide_on_mobile_by == "user_agent" || $hide_on_mobile_by == "both"){
                        return True;
                    }
                }
            }else{
                if($hide_on_desktop == "1"){
                    return True;
                }
            }
            return False;
        }


        public function generateSwitchStyles($settings){
            $styles = array();
            switch ($settings["dark_mode_switch_design"]){
                case "apple":
                    $styles = array(
                        "--darkmysite_switch_apple_width_height" => $settings["switch_apple_width_height"]."px",
                        "--darkmysite_switch_apple_border_radius" => $settings["switch_apple_border_radius"]."px",
                        "--darkmysite_switch_apple_icon_width" => $settings["switch_apple_icon_width"]."px",
                        "--darkmysite_switch_apple_light_mode_bg" => $settings["switch_apple_light_mode_bg"],
                        "--darkmysite_switch_apple_dark_mode_bg" => $settings["switch_apple_dark_mode_bg"],
                        "--darkmysite_switch_apple_light_mode_icon_color" => $settings["switch_apple_light_mode_icon_color"],
                        "--darkmysite_switch_apple_dark_mode_icon_color" => $settings["switch_apple_dark_mode_icon_color"],
                    );
                    break;
                case "banana":
                    $styles = array(
                        "--darkmysite_switch_banana_width_height" => $settings["switch_banana_width_height"]."px",
                        "--darkmysite_switch_banana_border_radius" => $settings["switch_banana_border_radius"]."px",
                        "--darkmysite_switch_banana_icon_width" => $settings["switch_banana_icon_width"]."px",
                        "--darkmysite_switch_banana_light_mode_bg" => $settings["switch_banana_light_mode_bg"],
                        "--darkmysite_switch_banana_dark_mode_bg" => $settings["switch_banana_dark_mode_bg"],
                        "--darkmysite_switch_banana_light_mode_icon_color" => $settings["switch_banana_light_mode_icon_color"],
                        "--darkmysite_switch_banana_dark_mode_icon_color" => $settings["switch_banana_dark_mode_icon_color"],
                    );
                    break;
                case "cherry":
                    $styles = array(
                        "--darkmysite_switch_cherry_width_height" => $settings["switch_cherry_width_height"]."px",
                        "--darkmysite_switch_cherry_border_radius" => $settings["switch_cherry_border_radius"]."px",
                        "--darkmysite_switch_cherry_icon_width" => $settings["switch_cherry_icon_width"]."px",
                        "--darkmysite_switch_cherry_light_mode_bg" => $settings["switch_cherry_light_mode_bg"],
                        "--darkmysite_switch_cherry_dark_mode_bg" => $settings["switch_cherry_dark_mode_bg"],
                        "--darkmysite_switch_cherry_light_mode_icon_color" => $settings["switch_cherry_light_mode_icon_color"],
                        "--darkmysite_switch_cherry_dark_mode_icon_color" => $settings["switch_cherry_dark_mode_icon_color"],
                    );
                    break;
                case "durian":
                    $styles = array(
                        "--darkmysite_switch_durian_width_height" => $settings["switch_durian_width_height"]."px",
                        "--darkmysite_switch_durian_border_size" => $settings["switch_durian_border_size"]."px",
                        "--darkmysite_switch_durian_border_radius" => $settings["switch_durian_border_radius"]."px",
                        "--darkmysite_switch_durian_icon_width" => $settings["switch_durian_icon_width"]."px",
                        "--darkmysite_switch_durian_light_mode_bg" => $settings["switch_durian_light_mode_bg"],
                        "--darkmysite_switch_durian_dark_mode_bg" => $settings["switch_durian_dark_mode_bg"],
                        "--darkmysite_switch_durian_light_mode_icon_and_border_color" => $settings["switch_durian_light_mode_icon_and_border_color"],
                        "--darkmysite_switch_durian_dark_mode_icon_and_border_color" => $settings["switch_durian_dark_mode_icon_and_border_color"],
                    );
                    break;
                case "elderberry":
                    $styles = array(
                        "--darkmysite_switch_elderberry_width" => $settings["switch_elderberry_width"]."px",
                        "--darkmysite_switch_elderberry_height" => $settings["switch_elderberry_height"]."px",
                        "--darkmysite_switch_elderberry_icon_plate_width" => $settings["switch_elderberry_icon_plate_width"]."px",
                        "--darkmysite_switch_elderberry_icon_plate_border_size" => $settings["switch_elderberry_icon_plate_border_size"]."px",
                        "--darkmysite_switch_elderberry_icon_width" => $settings["switch_elderberry_icon_width"]."px",
                        "--darkmysite_switch_elderberry_light_mode_bg" => $settings["switch_elderberry_light_mode_bg"],
                        "--darkmysite_switch_elderberry_dark_mode_bg" => $settings["switch_elderberry_dark_mode_bg"],
                        "--darkmysite_switch_elderberry_light_mode_icon_plate_bg" => $settings["switch_elderberry_light_mode_icon_plate_bg"],
                        "--darkmysite_switch_elderberry_dark_mode_icon_plate_bg" => $settings["switch_elderberry_dark_mode_icon_plate_bg"],
                        "--darkmysite_switch_elderberry_light_mode_icon_color" => $settings["switch_elderberry_light_mode_icon_color"],
                        "--darkmysite_switch_elderberry_dark_mode_icon_color" => $settings["switch_elderberry_dark_mode_icon_color"],
                    );
                    break;
                case "fazli":
                    $styles = array(
                        "--darkmysite_switch_fazli_width" => $settings["switch_fazli_width"]."px",
                        "--darkmysite_switch_fazli_height" => $settings["switch_fazli_height"]."px",
                        "--darkmysite_switch_fazli_icon_plate_width" => $settings["switch_fazli_icon_plate_width"]."px",
                        "--darkmysite_switch_fazli_icon_plate_border_size" => $settings["switch_fazli_icon_plate_border_size"]."px",
                        "--darkmysite_switch_fazli_icon_width" => $settings["switch_fazli_icon_width"]."px",
                        "--darkmysite_switch_fazli_light_mode_bg" => $settings["switch_fazli_light_mode_bg"],
                        "--darkmysite_switch_fazli_dark_mode_bg" => $settings["switch_fazli_dark_mode_bg"],
                        "--darkmysite_switch_fazli_light_mode_icon_plate_bg" => $settings["switch_fazli_light_mode_icon_plate_bg"],
                        "--darkmysite_switch_fazli_dark_mode_icon_plate_bg" => $settings["switch_fazli_dark_mode_icon_plate_bg"],
                        "--darkmysite_switch_fazli_light_mode_icon_color" => $settings["switch_fazli_light_mode_icon_color"],
                        "--darkmysite_switch_fazli_dark_mode_icon_color" => $settings["switch_fazli_dark_mode_icon_color"],
                    );
                    break;
                case "guava":
                    $styles = array(
                        "--darkmysite_switch_guava_width" => $settings["switch_guava_width"]."px",
                        "--darkmysite_switch_guava_height" => $settings["switch_guava_height"]."px",
                        "--darkmysite_switch_guava_icon_width" => $settings["switch_guava_icon_width"]."px",
                        "--darkmysite_switch_guava_icon_margin" => $settings["switch_guava_icon_margin"]."px",
                        "--darkmysite_switch_guava_light_mode_bg" => $settings["switch_guava_light_mode_bg"],
                        "--darkmysite_switch_guava_dark_mode_bg" => $settings["switch_guava_dark_mode_bg"],
                        "--darkmysite_switch_guava_light_mode_icon_color" => $settings["switch_guava_light_mode_icon_color"],
                        "--darkmysite_switch_guava_dark_mode_icon_color" => $settings["switch_guava_dark_mode_icon_color"],
                    );
                    break;
                case "honeydew":
                    $styles = array(
                        "--darkmysite_switch_honeydew_width" => $settings["switch_honeydew_width"]."px",
                        "--darkmysite_switch_honeydew_height" => $settings["switch_honeydew_height"]."px",
                        "--darkmysite_switch_honeydew_icon_plate_width" => $settings["switch_honeydew_icon_plate_width"]."px",
                        "--darkmysite_switch_honeydew_icon_plate_margin" => $settings["switch_honeydew_icon_plate_margin"]."px",
                        "--darkmysite_switch_honeydew_icon_width" => $settings["switch_honeydew_icon_width"]."px",
                        "--darkmysite_switch_honeydew_light_mode_icon_plate_bg" => $settings["switch_honeydew_light_mode_icon_plate_bg"],
                        "--darkmysite_switch_honeydew_dark_mode_icon_plate_bg" => $settings["switch_honeydew_dark_mode_icon_plate_bg"],
                        "--darkmysite_switch_honeydew_light_mode_icon_color" => $settings["switch_honeydew_light_mode_icon_color"],
                        "--darkmysite_switch_honeydew_dark_mode_icon_color" => $settings["switch_honeydew_dark_mode_icon_color"],
                    );
                    break;
                case "incaberry":
                    $styles = array(
                        "--darkmysite_switch_incaberry_width" => $settings["switch_incaberry_width"]."px",
                        "--darkmysite_switch_incaberry_height" => $settings["switch_incaberry_height"]."px",
                        "--darkmysite_switch_incaberry_icon_plate_width" => $settings["switch_incaberry_icon_plate_width"]."px",
                        "--darkmysite_switch_incaberry_icon_plate_margin" => $settings["switch_incaberry_icon_plate_margin"]."px",
                        "--darkmysite_switch_incaberry_icon_width" => $settings["switch_incaberry_icon_width"]."px",
                        "--darkmysite_switch_incaberry_light_mode_icon_plate_bg" => $settings["switch_incaberry_light_mode_icon_plate_bg"],
                        "--darkmysite_switch_incaberry_dark_mode_icon_plate_bg" => $settings["switch_incaberry_dark_mode_icon_plate_bg"],
                        "--darkmysite_switch_incaberry_light_mode_icon_color" => $settings["switch_incaberry_light_mode_icon_color"],
                        "--darkmysite_switch_incaberry_dark_mode_icon_color" => $settings["switch_incaberry_dark_mode_icon_color"],
                    );
                    break;
                case "jackfruit":
                    $styles = array(
                        "--darkmysite_switch_jackfruit_width" => $settings["switch_jackfruit_width"]."px",
                        "--darkmysite_switch_jackfruit_height" => $settings["switch_jackfruit_height"]."px",
                        "--darkmysite_switch_jackfruit_icon_plate_width" => $settings["switch_jackfruit_icon_plate_width"]."px",
                        "--darkmysite_switch_jackfruit_icon_plate_margin" => $settings["switch_jackfruit_icon_plate_margin"]."px",
                        "--darkmysite_switch_jackfruit_icon_width" => $settings["switch_jackfruit_icon_width"]."px",
                        "--darkmysite_switch_jackfruit_light_mode_bg" => $settings["switch_jackfruit_light_mode_bg"],
                        "--darkmysite_switch_jackfruit_dark_mode_bg" => $settings["switch_jackfruit_dark_mode_bg"],
                        "--darkmysite_switch_jackfruit_light_mode_icon_plate_bg" => $settings["switch_jackfruit_light_mode_icon_plate_bg"],
                        "--darkmysite_switch_jackfruit_dark_mode_icon_plate_bg" => $settings["switch_jackfruit_dark_mode_icon_plate_bg"],
                        "--darkmysite_switch_jackfruit_light_mode_light_icon_color" => $settings["switch_jackfruit_light_mode_light_icon_color"],
                        "--darkmysite_switch_jackfruit_light_mode_dark_icon_color" => $settings["switch_jackfruit_light_mode_dark_icon_color"],
                        "--darkmysite_switch_jackfruit_dark_mode_light_icon_color" => $settings["switch_jackfruit_dark_mode_light_icon_color"],
                        "--darkmysite_switch_jackfruit_dark_mode_dark_icon_color" => $settings["switch_jackfruit_dark_mode_dark_icon_color"]
                    );
                    break;
            }
            return $styles;
        }


        public function generateSwitchStylesForShortcode($atts){
            $styles = array();
            $switch_name = "apple";
            if($atts['switch'] == "1"){
                $switch_name = "apple";
            }else if($atts['switch'] == "2"){
                $switch_name = "banana";
            }else if($atts['switch'] == "3"){
                $switch_name = "cherry";
            }else if($atts['switch'] == "4"){
                $switch_name = "durian";
            }else if($atts['switch'] == "5"){
                $switch_name = "elderberry";
            }else if($atts['switch'] == "6"){
                $switch_name = "fazli";
            }else if($atts['switch'] == "7"){
                $switch_name = "guava";
            }else if($atts['switch'] == "8"){
                $switch_name = "honeydew";
            }else if($atts['switch'] == "9"){
                $switch_name = "incaberry";
            }else if($atts['switch'] == "10"){
                $switch_name = "jackfruit";
            }
            foreach ($atts as $key => $value){
                if($key == "switch"){ continue; }
                $styles["--darkmysite_switch_".$switch_name."_".$key] = $value;
            }
            return $styles;
        }


        public function string_contains_any($string, $words, $caseSensitive = true) {
            foreach ($words as $word) {
                $position = $caseSensitive ? stripos($string, $word) : strpos($string, $word);
                if ($position !== false) {
                    return true;
                }
            }
            return false;
        }

        public function extractCustomCssSelectorsForAutoExcluding($css, $custom_css_apply_on_children){
            $pseudo_classes = array(":after", ":before", ":first-letter", ":first-line", ":selection", ":not-disallowed");
            $css = str_replace(array("\r","\n"),"", $css);
            /* Convert to Array */
            $exclude_elements = array();
            $results = array();
            preg_match_all('/(.+?)\s?\{\s?(.+?)\s?\}/', $css, $matches);
            foreach($matches[0] AS $i=>$original){
                foreach(explode(';', $matches[2][$i]) AS $attr){
                    if (strlen(trim($attr)) > 0) // for missing semicolon on last element, which is legal
                    {
                        list($name, $value) = explode(':', $attr);
                        $results[$matches[1][$i]][trim($name)] = trim($value);
                    }
                }
            }
            /* Array to processed css string */
            foreach ($results AS $single_selector => $rules){
                $single_selector_arr = explode (",", $single_selector);
                for($i = 0; $i < sizeof($single_selector_arr); $i++){
                    if (!$this->string_contains_any(trim($single_selector_arr[$i]), $pseudo_classes, false)){
                        $exclude_elements[] = trim($single_selector_arr[$i]);
                        if($custom_css_apply_on_children == "1"){
                            $exclude_elements[] = trim($single_selector_arr[$i])." *";
                        }
                    }
                }
            }
            return $exclude_elements;
        }

        public function parseAndProcessCustomCSS($css, $custom_css_apply_on_children){
            /* Replace : on https:// or http:// for detecting as background:black type */
            $css = str_replace("://","--colon--//", $css);

            $css = str_replace(array("\r","\n"),"", $css);
            /* Convert to Array */
            $results = array();
            preg_match_all('/(.+?)\s?\{\s?(.+?)\s?\}/', $css, $matches);
            foreach($matches[0] AS $i=>$original){
                foreach(explode(';', $matches[2][$i]) AS $attr){
                    if (strlen(trim($attr)) > 0) // for missing semicolon on last element, which is legal
                    {
                        list($name, $value) = explode(':', $attr);
                        $results[$matches[1][$i]][trim($name)] = trim($value);
                    }
                }
            }
            /* Array to processed css string */
            $updated_css = "";
            foreach ($results AS $single_selector => $rules){
                $updated_single_selector = "";
                $single_selector_arr = explode (",", $single_selector);
                for($i = 0; $i < sizeof($single_selector_arr); $i++){
                    if($i > 0){ $updated_single_selector .= ", "; }
                    $updated_single_selector .= ".darkmysite_dark_mode_enabled ". trim($single_selector_arr[$i]);
                    if($custom_css_apply_on_children == "1"){
                        $updated_single_selector .= ", .darkmysite_dark_mode_enabled ". trim($single_selector_arr[$i])." *";
                    }
                }

                $updated_single_selector = str_replace(":not-disallowed", "", $updated_single_selector);
                $updated_css .= $updated_single_selector."{";

                foreach ($rules AS $key => $value){
                    if(strpos($value, "important") == false){
                        $value = $value." !important";
                    }
                    $updated_css .= $key.": ".$value.";";
                }

                $updated_css .= "}";
            }
            /* Replace --colon--// back to https:// or http:// format */
            $updated_css = str_replace("--colon--//", "://", $updated_css);
            return $updated_css;
        }


        public function parseAndProcessNormalCustomCSS($css){
            /* Replace : on https:// or http:// for detecting as background:black type */
            $css = str_replace("://","--colon--//", $css);

            $css = str_replace(array("\r","\n"),"", $css);
            /* Convert to Array */
            $results = array();
            preg_match_all('/(.+?)\s?\{\s?(.+?)\s?\}/', $css, $matches);
            foreach($matches[0] AS $i=>$original){
                foreach(explode(';', $matches[2][$i]) AS $attr){
                    if (strlen(trim($attr)) > 0) // for missing semicolon on last element, which is legal
                    {
                        list($name, $value) = explode(':', $attr);
                        $results[$matches[1][$i]][trim($name)] = trim($value);
                    }
                }
            }
            /* Array to processed css string */
            $updated_css = "";
            foreach ($results AS $single_selector => $rules){
                $updated_css .= $single_selector."{";

                foreach ($rules AS $key => $value){
                    if(strpos($value, "important") == false){
                        $value = $value." !important";
                    }
                    $updated_css .= $key.": ".$value.";";
                }

                $updated_css .= "}";
            }
            /* Replace --colon--// back to https:// or http:// format */
            $updated_css = str_replace("--colon--//", "://", $updated_css);
            return $updated_css;
        }


        public function generateAllowedElementsStr($data_settings){
            $allowed_elements = "";
            if(strlen(trim($data_settings["allowed_elements"])) > 0) {
                $allowed_elements_arr = explode( ',', $data_settings["allowed_elements"] );
                if(is_array($allowed_elements_arr)){
                    if(sizeof($allowed_elements_arr) > 0){
                        foreach( $allowed_elements_arr as $single_element ) {
                            $allowed_elements .= ($allowed_elements != "" ? ", " : "").trim($single_element);
                            $allowed_elements .= ($allowed_elements != "" ? ", " : "").trim($single_element).' *';
                        }
                    }
                }
            }
            return $allowed_elements;
        }

        public function generateDisallowedElementsStr($data_settings, $external_support_class_obj){
            $disallowed_elements = "";
            if(strlen(trim($data_settings["disallowed_elements"])) > 0) {
                $disallowed_elements_arr = explode( ',', $data_settings["disallowed_elements"] );
                if(is_array($disallowed_elements_arr)){
                    if(sizeof($disallowed_elements_arr) > 0){
                        foreach( $disallowed_elements_arr as $single_element ) {
                            $disallowed_elements .= ($disallowed_elements != "" ? ", " : "").trim($single_element);
                            $disallowed_elements .= ($disallowed_elements != "" ? ", " : "").trim($single_element).' *';
                        }
                    }
                }
            }



            // Get Disallowed Elements from External Plugins
            $disallowed_from_external = $external_support_class_obj->getDisallowedElementsByAvailablePlugins();
            if(sizeof($disallowed_from_external) > 0){
                foreach( $disallowed_from_external as $single_element ) {
                    $disallowed_elements .= ($disallowed_elements != "" ? ", " : "").trim($single_element);
                }
            }

            // Exclude selectors specified in Custom CSS
            $custom_css_selectors = $this->extractCustomCssSelectorsForAutoExcluding($data_settings["custom_css"], $data_settings["custom_css_apply_on_children"]);
            if(is_array($custom_css_selectors)){
                if(sizeof($custom_css_selectors) > 0){
                    foreach( $custom_css_selectors as $single_element ) {
                        $disallowed_elements .= ($disallowed_elements != "" ? ", " : "").trim($single_element);
                    }
                }
            }
            return $disallowed_elements;
        }


        public function minify_string($buffer) {
            $search = array(
                '/\>[^\S ]+/s',     // strip whitespaces after tags, except space
                '/[^\S ]+\</s',     // strip whitespaces before tags, except space
                '/(\s)+/s',         // shorten multiple whitespace sequences
                '/<!--(.|\s)*?-->/' // Remove HTML comments
            );
            $replace = array(
                '>',
                '<',
                '\\1',
                ''
            );
            $buffer = preg_replace($search, $replace, $buffer);
            return $buffer;
        }


        public function getWpNavMenus() {
            $results = array();
            $results[] = array("id" => "0", "text" => "Choose Menu");
            $menus = wp_get_nav_menus();
            foreach ($menus as $menu) {
                $results[] = array("id" => $menu->term_id, "text" => $menu->name);
            }
            return $results;
        }

        public function getWpPages() {
            $results = array();
            $results[] = array("id" => "0", "text" => "Homepage");
            $results[] = array("id" => "lr", "text" => "WP Login / Registration Page");
            $args = array('post_type' => 'page', 'posts_per_page' => -1, 'orderby' => 'title', 'order'   => 'ASC');
            foreach (get_posts($args) as $page) {
                $results[] = array("id" => $page->ID, "text" => $page->post_title);
            }
            return $results;
        }

        public function searchWpPosts($search = "") {
            global $wpdb;
            $results = array();
            $sql = $wpdb->prepare( "SELECT ID, post_title FROM {$wpdb->prefix}posts WHERE post_type = 'post' AND post_title LIKE %s GROUP BY ID ORDER BY ID DESC LIMIT 10", array( '%'.$search.'%' ) );
            $listPosts = $wpdb->get_results($sql);
            if (sizeof($listPosts) > 0) {
                foreach ($listPosts as $singlePost) {
                    $results[] = array("id" => $singlePost->ID, "text" => $singlePost->post_title);
                }
            }
            return $results;
        }


        public function getWpPosts($post_ids = array()) {
            $results = array();
            if(sizeof($post_ids) > 0){
                $args = array('post_type' => 'post', 'posts_per_page' => -1, 'orderby' => 'ID', 'order'   => 'DESC', 'post__in' => $post_ids);
                foreach (get_posts($args) as $page) {
                    $results[] = array("id" => $page->ID, "text" => $page->post_title);
                }
            }
            return $results;
        }



        public function isRestrictedByDisallowedAdminPages($disallowed_admin_pages) {
            if(strlen(trim($disallowed_admin_pages)) > 0) {
                if(function_exists("get_current_screen")){
                    $current_screen = get_current_screen();
                    $parts = explode('_page_', $current_screen->id);
                    if (count($parts) !== 2) { return False;} /* means it's something like invalid page slug */
                    $page_slug = $parts[1];

                    $disallowed_admin_pages_arr = explode( ',', $disallowed_admin_pages );
                    if(is_array($disallowed_admin_pages_arr)){
                        if(sizeof($disallowed_admin_pages_arr) > 0){
                            foreach( $disallowed_admin_pages_arr as $single_page ) {
                                if ( $page_slug == trim($single_page) ) {
                                    return True;
                                }
                            }
                        }
                    }
                }
            }
            return False;
        }


        public function isRestrictedByDisallowedPages($disallowed_pages) {
            if(is_array($disallowed_pages)) {
                if (sizeof($disallowed_pages) > 0) {
                    if(function_exists("is_page") && function_exists("get_the_ID")){
                        if(is_page() || is_front_page()){
                            $current_page_id = is_front_page() ? "0" : get_the_ID();
                            if(in_array ($current_page_id, $disallowed_pages)){
                                return True;
                            }
                        }
                    }
                    if ( class_exists( 'WooCommerce' ) ) {
                        if(function_exists("is_shop") && function_exists("wc_get_page_id")){
                            if(is_shop()){
                                $current_page_id = wc_get_page_id('shop');
                                if(in_array ($current_page_id, $disallowed_pages)){
                                    return True;
                                }
                            }
                        }
                    }
                    if(isset($GLOBALS['pagenow'])){
                        if ( in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ) ) ) {
                            $current_page_id = "lr";
                            if(in_array ($current_page_id, $disallowed_pages)){
                                return True;
                            }
                        }
                    }
                }
            }
            return False;
        }

        public function isRestrictedByAllowedPages($allowed_pages) {
            if(is_array($allowed_pages)){
                if(sizeof($allowed_pages) > 0){
                    if(function_exists("is_page") && function_exists("get_the_ID")){
                        if(is_page() || is_front_page()){
                            $current_page_id = is_front_page() ? "0" : get_the_ID();
                            if(!in_array ($current_page_id, $allowed_pages)){
                                return True;
                            }
                        }
                    }
                    if ( class_exists( 'WooCommerce' ) ) {
                        if(function_exists("is_shop") && function_exists("wc_get_page_id")){
                            if(is_shop()){
                                $current_page_id = wc_get_page_id('shop');
                                if(!in_array ($current_page_id, $allowed_pages)){
                                    return True;
                                }
                            }
                        }
                    }
                    if(isset($GLOBALS['pagenow'])){
                        if ( in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ) ) ) {
                            $current_page_id = "lr";
                            if(!in_array ($current_page_id, $allowed_pages)){
                                return True;
                            }
                        }
                    }
                }
            }
            return False;
        }


        public function isRestrictedByDisallowedPosts($disallowed_posts) {
            if(is_array($disallowed_posts)) {
                if (sizeof($disallowed_posts) > 0) {
                    if(function_exists("is_singular") && function_exists("get_the_ID")){
                        if(is_singular("post")){
                            $current_post_id = get_the_ID();
                            if(in_array ($current_post_id, $disallowed_posts)){
                                return True;
                            }
                        }
                    }
                }
            }
            return False;
        }


        public function isRestrictedByAllowedPosts($allowed_posts) {
            if(is_array($allowed_posts)){
                if(sizeof($allowed_posts) > 0){
                    if(function_exists("is_singular") && function_exists("get_the_ID")){
                        if(is_singular("post")){
                            $current_post_id = get_the_ID();
                            if(!in_array ($current_post_id, $allowed_posts)){
                                return True;
                            }
                        }
                    }
                }
            }
            return False;
        }

    }
}
