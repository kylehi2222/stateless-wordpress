<?php

$result = array();

/* Check if user has admin capabilities */
if(current_user_can('manage_options')){

    if(isset($_REQUEST['enable_dark_mode_switch'])){

        /* Control */
        if(isset($_REQUEST['show_rating_block'])){
            $this->base_admin->settings->updateSettings("show_rating_block", sanitize_text_field($_REQUEST['show_rating_block']));
        }
        if(isset($_REQUEST['show_support_msg_block'])){
            $this->base_admin->settings->updateSettings("show_support_msg_block", sanitize_text_field($_REQUEST['show_support_msg_block']));
        }
        if(isset($_REQUEST['enable_dark_mode_switch'])){
            $this->base_admin->settings->updateSettings("enable_dark_mode_switch", sanitize_text_field($_REQUEST['enable_dark_mode_switch']));
        }
        if(isset($_REQUEST['enable_default_dark_mode'])){
            $this->base_admin->settings->updateSettings("enable_default_dark_mode", sanitize_text_field($_REQUEST['enable_default_dark_mode']));
        }
        if(isset($_REQUEST['enable_os_aware'])){
            $this->base_admin->settings->updateSettings("enable_os_aware", sanitize_text_field($_REQUEST['enable_os_aware']));
        }
        if(isset($_REQUEST['enable_keyboard_shortcut'])){
            $this->base_admin->settings->updateSettings("enable_keyboard_shortcut", sanitize_text_field($_REQUEST['enable_keyboard_shortcut']));
        }
        if(isset($_REQUEST['enable_time_based_dark'])){
            $this->base_admin->settings->updateSettings("enable_time_based_dark", sanitize_text_field($_REQUEST['enable_time_based_dark']));
        }
        if(isset($_REQUEST['time_based_dark_start'])){
            $this->base_admin->settings->updateSettings("time_based_dark_start", sanitize_text_field($_REQUEST['time_based_dark_start']));
        }
        if(isset($_REQUEST['time_based_dark_stop'])){
            $this->base_admin->settings->updateSettings("time_based_dark_stop", sanitize_text_field($_REQUEST['time_based_dark_stop']));
        }
        if(isset($_REQUEST['hide_on_desktop'])){
            $this->base_admin->settings->updateSettings("hide_on_desktop", sanitize_text_field($_REQUEST['hide_on_desktop']));
        }
        if(isset($_REQUEST['hide_on_mobile'])){
            $this->base_admin->settings->updateSettings("hide_on_mobile", sanitize_text_field($_REQUEST['hide_on_mobile']));
        }
        if(isset($_REQUEST['hide_on_mobile_by'])){
            $this->base_admin->settings->updateSettings("hide_on_mobile_by", sanitize_text_field($_REQUEST['hide_on_mobile_by']));
        }
        if(isset($_REQUEST['enable_switch_in_menu'])){
            $this->base_admin->settings->updateSettings("enable_switch_in_menu", sanitize_text_field($_REQUEST['enable_switch_in_menu']));
        }
        if(isset($_REQUEST['switch_in_menu_location'])){
            $this->base_admin->settings->updateSettings("switch_in_menu_location", sanitize_text_field($_REQUEST['switch_in_menu_location']));
        }
        if(isset($_REQUEST['switch_in_menu_shortcode'])){
            $this->base_admin->settings->updateSettings("switch_in_menu_shortcode", wp_filter_post_kses($_REQUEST['switch_in_menu_shortcode']));
        }

        /* Admin */
        if(isset($_REQUEST['enable_admin_dark_mode'])){
            $this->base_admin->settings->updateSettings("enable_admin_dark_mode", sanitize_text_field($_REQUEST['enable_admin_dark_mode']));
        }
        if(isset($_REQUEST['display_in_admin_settings_menu'])){
            $this->base_admin->settings->updateSettings("display_in_admin_settings_menu", sanitize_text_field($_REQUEST['display_in_admin_settings_menu']));
        }
        if(isset($_REQUEST['disallowed_admin_pages'])){
            $this->base_admin->settings->updateSettings("disallowed_admin_pages", sanitize_text_field($_REQUEST['disallowed_admin_pages']));
        }

        /* Switch */
        if(isset($_REQUEST['dark_mode_switch_design'])){
            $this->base_admin->settings->updateSettings("dark_mode_switch_design", sanitize_text_field($_REQUEST['dark_mode_switch_design']));
        }
        if(isset($_REQUEST['dark_mode_switch_position'])){
            $this->base_admin->settings->updateSettings("dark_mode_switch_position", sanitize_text_field($_REQUEST['dark_mode_switch_position']));
        }
        if(isset($_REQUEST['dark_mode_switch_margin_top'])){
            $this->base_admin->settings->updateSettings("dark_mode_switch_margin_top", sanitize_text_field($_REQUEST['dark_mode_switch_margin_top']));
        }
        if(isset($_REQUEST['dark_mode_switch_margin_bottom'])){
            $this->base_admin->settings->updateSettings("dark_mode_switch_margin_bottom", sanitize_text_field($_REQUEST['dark_mode_switch_margin_bottom']));
        }
        if(isset($_REQUEST['dark_mode_switch_margin_left'])){
            $this->base_admin->settings->updateSettings("dark_mode_switch_margin_left", sanitize_text_field($_REQUEST['dark_mode_switch_margin_left']));
        }
        if(isset($_REQUEST['dark_mode_switch_margin_right'])){
            $this->base_admin->settings->updateSettings("dark_mode_switch_margin_right", sanitize_text_field($_REQUEST['dark_mode_switch_margin_right']));
        }
        if(isset($_REQUEST['enable_switch_position_different_in_mobile'])){
            $this->base_admin->settings->updateSettings("enable_switch_position_different_in_mobile", sanitize_text_field($_REQUEST['enable_switch_position_different_in_mobile']));
        }
        if(isset($_REQUEST['dark_mode_switch_position_in_mobile'])){
            $this->base_admin->settings->updateSettings("dark_mode_switch_position_in_mobile", sanitize_text_field($_REQUEST['dark_mode_switch_position_in_mobile']));
        }
        if(isset($_REQUEST['dark_mode_switch_margin_top_in_mobile'])){
            $this->base_admin->settings->updateSettings("dark_mode_switch_margin_top_in_mobile", sanitize_text_field($_REQUEST['dark_mode_switch_margin_top_in_mobile']));
        }
        if(isset($_REQUEST['dark_mode_switch_margin_bottom_in_mobile'])){
            $this->base_admin->settings->updateSettings("dark_mode_switch_margin_bottom_in_mobile", sanitize_text_field($_REQUEST['dark_mode_switch_margin_bottom_in_mobile']));
        }
        if(isset($_REQUEST['dark_mode_switch_margin_left_in_mobile'])){
            $this->base_admin->settings->updateSettings("dark_mode_switch_margin_left_in_mobile", sanitize_text_field($_REQUEST['dark_mode_switch_margin_left_in_mobile']));
        }
        if(isset($_REQUEST['dark_mode_switch_margin_right_in_mobile'])){
            $this->base_admin->settings->updateSettings("dark_mode_switch_margin_right_in_mobile", sanitize_text_field($_REQUEST['dark_mode_switch_margin_right_in_mobile']));
        }
        if(isset($_REQUEST['enable_absolute_position'])){
            $this->base_admin->settings->updateSettings("enable_absolute_position", sanitize_text_field($_REQUEST['enable_absolute_position']));
        }
        if(isset($_REQUEST['enable_switch_dragging'])){
            $this->base_admin->settings->updateSettings("enable_switch_dragging", sanitize_text_field($_REQUEST['enable_switch_dragging']));
        }

        /* Switch Extras*/
        if(isset($_REQUEST['enable_floating_switch_tooltip'])){
            $this->base_admin->settings->updateSettings("enable_floating_switch_tooltip", sanitize_text_field($_REQUEST['enable_floating_switch_tooltip']));
        }
        if(isset($_REQUEST['floating_switch_tooltip_position'])){
            $this->base_admin->settings->updateSettings("floating_switch_tooltip_position", sanitize_text_field($_REQUEST['floating_switch_tooltip_position']));
        }
        if(isset($_REQUEST['floating_switch_tooltip_text'])){
            $this->base_admin->settings->updateSettings("floating_switch_tooltip_text", wp_filter_post_kses($_REQUEST['floating_switch_tooltip_text']));
        }
        if(isset($_REQUEST['floating_switch_tooltip_bg_color'])){
            $this->base_admin->settings->updateSettings("floating_switch_tooltip_bg_color", sanitize_text_field($_REQUEST['floating_switch_tooltip_bg_color']));
        }
        if(isset($_REQUEST['floating_switch_tooltip_text_color'])){
            $this->base_admin->settings->updateSettings("floating_switch_tooltip_text_color", sanitize_text_field($_REQUEST['floating_switch_tooltip_text_color']));
        }
        if(isset($_REQUEST['alternative_dark_mode_switch'])){
            $this->base_admin->settings->updateSettings("alternative_dark_mode_switch", sanitize_text_field($_REQUEST['alternative_dark_mode_switch']));
        }

        /* Switch Apple*/
        if(isset($_REQUEST['switch_apple_width_height'])){
            $this->base_admin->settings->updateSettings("switch_apple_width_height", sanitize_text_field($_REQUEST['switch_apple_width_height']));
        }
        if(isset($_REQUEST['switch_apple_border_radius'])){
            $this->base_admin->settings->updateSettings("switch_apple_border_radius", sanitize_text_field($_REQUEST['switch_apple_border_radius']));
        }
        if(isset($_REQUEST['switch_apple_icon_width'])){
            $this->base_admin->settings->updateSettings("switch_apple_icon_width", sanitize_text_field($_REQUEST['switch_apple_icon_width']));
        }
        if(isset($_REQUEST['switch_apple_light_mode_bg'])){
            $this->base_admin->settings->updateSettings("switch_apple_light_mode_bg", sanitize_text_field($_REQUEST['switch_apple_light_mode_bg']));
        }
        if(isset($_REQUEST['switch_apple_dark_mode_bg'])){
            $this->base_admin->settings->updateSettings("switch_apple_dark_mode_bg", sanitize_text_field($_REQUEST['switch_apple_dark_mode_bg']));
        }
        if(isset($_REQUEST['switch_apple_light_mode_icon_color'])){
            $this->base_admin->settings->updateSettings("switch_apple_light_mode_icon_color", sanitize_text_field($_REQUEST['switch_apple_light_mode_icon_color']));
        }
        if(isset($_REQUEST['switch_apple_dark_mode_icon_color'])){
            $this->base_admin->settings->updateSettings("switch_apple_dark_mode_icon_color", sanitize_text_field($_REQUEST['switch_apple_dark_mode_icon_color']));
        }
        /* Switch Banana*/
        if(isset($_REQUEST['switch_banana_width_height'])){
            $this->base_admin->settings->updateSettings("switch_banana_width_height", sanitize_text_field($_REQUEST['switch_banana_width_height']));
        }
        if(isset($_REQUEST['switch_banana_border_radius'])){
            $this->base_admin->settings->updateSettings("switch_banana_border_radius", sanitize_text_field($_REQUEST['switch_banana_border_radius']));
        }
        if(isset($_REQUEST['switch_banana_icon_width'])){
            $this->base_admin->settings->updateSettings("switch_banana_icon_width", sanitize_text_field($_REQUEST['switch_banana_icon_width']));
        }
        if(isset($_REQUEST['switch_banana_light_mode_bg'])){
            $this->base_admin->settings->updateSettings("switch_banana_light_mode_bg", sanitize_text_field($_REQUEST['switch_banana_light_mode_bg']));
        }
        if(isset($_REQUEST['switch_banana_dark_mode_bg'])){
            $this->base_admin->settings->updateSettings("switch_banana_dark_mode_bg", sanitize_text_field($_REQUEST['switch_banana_dark_mode_bg']));
        }
        if(isset($_REQUEST['switch_banana_light_mode_icon_color'])){
            $this->base_admin->settings->updateSettings("switch_banana_light_mode_icon_color", sanitize_text_field($_REQUEST['switch_banana_light_mode_icon_color']));
        }
        if(isset($_REQUEST['switch_banana_dark_mode_icon_color'])){
            $this->base_admin->settings->updateSettings("switch_banana_dark_mode_icon_color", sanitize_text_field($_REQUEST['switch_banana_dark_mode_icon_color']));
        }
        /* Switch Cherry*/
        if(isset($_REQUEST['switch_cherry_width_height'])){
            $this->base_admin->settings->updateSettings("switch_cherry_width_height", sanitize_text_field($_REQUEST['switch_cherry_width_height']));
        }
        if(isset($_REQUEST['switch_cherry_border_radius'])){
            $this->base_admin->settings->updateSettings("switch_cherry_border_radius", sanitize_text_field($_REQUEST['switch_cherry_border_radius']));
        }
        if(isset($_REQUEST['switch_cherry_icon_width'])){
            $this->base_admin->settings->updateSettings("switch_cherry_icon_width", sanitize_text_field($_REQUEST['switch_cherry_icon_width']));
        }
        if(isset($_REQUEST['switch_cherry_light_mode_bg'])){
            $this->base_admin->settings->updateSettings("switch_cherry_light_mode_bg", sanitize_text_field($_REQUEST['switch_cherry_light_mode_bg']));
        }
        if(isset($_REQUEST['switch_cherry_dark_mode_bg'])){
            $this->base_admin->settings->updateSettings("switch_cherry_dark_mode_bg", sanitize_text_field($_REQUEST['switch_cherry_dark_mode_bg']));
        }
        if(isset($_REQUEST['switch_cherry_light_mode_icon_color'])){
            $this->base_admin->settings->updateSettings("switch_cherry_light_mode_icon_color", sanitize_text_field($_REQUEST['switch_cherry_light_mode_icon_color']));
        }
        if(isset($_REQUEST['switch_cherry_dark_mode_icon_color'])){
            $this->base_admin->settings->updateSettings("switch_cherry_dark_mode_icon_color", sanitize_text_field($_REQUEST['switch_cherry_dark_mode_icon_color']));
        }
        /* Switch Durian*/
        if(isset($_REQUEST['switch_durian_width_height'])){
            $this->base_admin->settings->updateSettings("switch_durian_width_height", sanitize_text_field($_REQUEST['switch_durian_width_height']));
        }
        if(isset($_REQUEST['switch_durian_border_size'])){
            $this->base_admin->settings->updateSettings("switch_durian_border_size", sanitize_text_field($_REQUEST['switch_durian_border_size']));
        }
        if(isset($_REQUEST['switch_durian_border_radius'])){
            $this->base_admin->settings->updateSettings("switch_durian_border_radius", sanitize_text_field($_REQUEST['switch_durian_border_radius']));
        }
        if(isset($_REQUEST['switch_durian_icon_width'])){
            $this->base_admin->settings->updateSettings("switch_durian_icon_width", sanitize_text_field($_REQUEST['switch_durian_icon_width']));
        }
        if(isset($_REQUEST['switch_durian_light_mode_bg'])){
            $this->base_admin->settings->updateSettings("switch_durian_light_mode_bg", sanitize_text_field($_REQUEST['switch_durian_light_mode_bg']));
        }
        if(isset($_REQUEST['switch_durian_dark_mode_bg'])){
            $this->base_admin->settings->updateSettings("switch_durian_dark_mode_bg", sanitize_text_field($_REQUEST['switch_durian_dark_mode_bg']));
        }
        if(isset($_REQUEST['switch_durian_light_mode_icon_and_border_color'])){
            $this->base_admin->settings->updateSettings("switch_durian_light_mode_icon_and_border_color", sanitize_text_field($_REQUEST['switch_durian_light_mode_icon_and_border_color']));
        }
        if(isset($_REQUEST['switch_durian_dark_mode_icon_and_border_color'])){
            $this->base_admin->settings->updateSettings("switch_durian_dark_mode_icon_and_border_color", sanitize_text_field($_REQUEST['switch_durian_dark_mode_icon_and_border_color']));
        }
        /* Switch Elderberry*/
        if(isset($_REQUEST['switch_elderberry_width'])){
            $this->base_admin->settings->updateSettings("switch_elderberry_width", sanitize_text_field($_REQUEST['switch_elderberry_width']));
        }
        if(isset($_REQUEST['switch_elderberry_height'])){
            $this->base_admin->settings->updateSettings("switch_elderberry_height", sanitize_text_field($_REQUEST['switch_elderberry_height']));
        }
        if(isset($_REQUEST['switch_elderberry_icon_plate_width'])){
            $this->base_admin->settings->updateSettings("switch_elderberry_icon_plate_width", sanitize_text_field($_REQUEST['switch_elderberry_icon_plate_width']));
        }
        if(isset($_REQUEST['switch_elderberry_icon_plate_border_size'])){
            $this->base_admin->settings->updateSettings("switch_elderberry_icon_plate_border_size", sanitize_text_field($_REQUEST['switch_elderberry_icon_plate_border_size']));
        }
        if(isset($_REQUEST['switch_elderberry_icon_width'])){
            $this->base_admin->settings->updateSettings("switch_elderberry_icon_width", sanitize_text_field($_REQUEST['switch_elderberry_icon_width']));
        }
        if(isset($_REQUEST['switch_elderberry_light_mode_bg'])){
            $this->base_admin->settings->updateSettings("switch_elderberry_light_mode_bg", sanitize_text_field($_REQUEST['switch_elderberry_light_mode_bg']));
        }
        if(isset($_REQUEST['switch_elderberry_dark_mode_bg'])){
            $this->base_admin->settings->updateSettings("switch_elderberry_dark_mode_bg", sanitize_text_field($_REQUEST['switch_elderberry_dark_mode_bg']));
        }
        if(isset($_REQUEST['switch_elderberry_light_mode_icon_plate_bg'])){
            $this->base_admin->settings->updateSettings("switch_elderberry_light_mode_icon_plate_bg", sanitize_text_field($_REQUEST['switch_elderberry_light_mode_icon_plate_bg']));
        }
        if(isset($_REQUEST['switch_elderberry_dark_mode_icon_plate_bg'])){
            $this->base_admin->settings->updateSettings("switch_elderberry_dark_mode_icon_plate_bg", sanitize_text_field($_REQUEST['switch_elderberry_dark_mode_icon_plate_bg']));
        }
        if(isset($_REQUEST['switch_elderberry_light_mode_icon_color'])){
            $this->base_admin->settings->updateSettings("switch_elderberry_light_mode_icon_color", sanitize_text_field($_REQUEST['switch_elderberry_light_mode_icon_color']));
        }
        if(isset($_REQUEST['switch_elderberry_dark_mode_icon_color'])){
            $this->base_admin->settings->updateSettings("switch_elderberry_dark_mode_icon_color", sanitize_text_field($_REQUEST['switch_elderberry_dark_mode_icon_color']));
        }
        /* Switch Fazli*/
        if(isset($_REQUEST['switch_fazli_width'])){
            $this->base_admin->settings->updateSettings("switch_fazli_width", sanitize_text_field($_REQUEST['switch_fazli_width']));
        }
        if(isset($_REQUEST['switch_fazli_height'])){
            $this->base_admin->settings->updateSettings("switch_fazli_height", sanitize_text_field($_REQUEST['switch_fazli_height']));
        }
        if(isset($_REQUEST['switch_fazli_icon_plate_width'])){
            $this->base_admin->settings->updateSettings("switch_fazli_icon_plate_width", sanitize_text_field($_REQUEST['switch_fazli_icon_plate_width']));
        }
        if(isset($_REQUEST['switch_fazli_icon_plate_border_size'])){
            $this->base_admin->settings->updateSettings("switch_fazli_icon_plate_border_size", sanitize_text_field($_REQUEST['switch_fazli_icon_plate_border_size']));
        }
        if(isset($_REQUEST['switch_fazli_icon_width'])){
            $this->base_admin->settings->updateSettings("switch_fazli_icon_width", sanitize_text_field($_REQUEST['switch_fazli_icon_width']));
        }
        if(isset($_REQUEST['switch_fazli_light_mode_bg'])){
            $this->base_admin->settings->updateSettings("switch_fazli_light_mode_bg", sanitize_text_field($_REQUEST['switch_fazli_light_mode_bg']));
        }
        if(isset($_REQUEST['switch_fazli_dark_mode_bg'])){
            $this->base_admin->settings->updateSettings("switch_fazli_dark_mode_bg", sanitize_text_field($_REQUEST['switch_fazli_dark_mode_bg']));
        }
        if(isset($_REQUEST['switch_fazli_light_mode_icon_plate_bg'])){
            $this->base_admin->settings->updateSettings("switch_fazli_light_mode_icon_plate_bg", sanitize_text_field($_REQUEST['switch_fazli_light_mode_icon_plate_bg']));
        }
        if(isset($_REQUEST['switch_fazli_dark_mode_icon_plate_bg'])){
            $this->base_admin->settings->updateSettings("switch_fazli_dark_mode_icon_plate_bg", sanitize_text_field($_REQUEST['switch_fazli_dark_mode_icon_plate_bg']));
        }
        if(isset($_REQUEST['switch_fazli_light_mode_icon_color'])){
            $this->base_admin->settings->updateSettings("switch_fazli_light_mode_icon_color", sanitize_text_field($_REQUEST['switch_fazli_light_mode_icon_color']));
        }
        if(isset($_REQUEST['switch_fazli_dark_mode_icon_color'])){
            $this->base_admin->settings->updateSettings("switch_fazli_dark_mode_icon_color", sanitize_text_field($_REQUEST['switch_fazli_dark_mode_icon_color']));
        }
        /* Switch Guava*/
        if(isset($_REQUEST['switch_guava_width'])){
            $this->base_admin->settings->updateSettings("switch_guava_width", sanitize_text_field($_REQUEST['switch_guava_width']));
        }
        if(isset($_REQUEST['switch_guava_height'])){
            $this->base_admin->settings->updateSettings("switch_guava_height", sanitize_text_field($_REQUEST['switch_guava_height']));
        }
        if(isset($_REQUEST['switch_guava_icon_width'])){
            $this->base_admin->settings->updateSettings("switch_guava_icon_width", sanitize_text_field($_REQUEST['switch_guava_icon_width']));
        }
        if(isset($_REQUEST['switch_guava_icon_margin'])){
            $this->base_admin->settings->updateSettings("switch_guava_icon_margin", sanitize_text_field($_REQUEST['switch_guava_icon_margin']));
        }
        if(isset($_REQUEST['switch_guava_light_mode_bg'])){
            $this->base_admin->settings->updateSettings("switch_guava_light_mode_bg", sanitize_text_field($_REQUEST['switch_guava_light_mode_bg']));
        }
        if(isset($_REQUEST['switch_guava_dark_mode_bg'])){
            $this->base_admin->settings->updateSettings("switch_guava_dark_mode_bg", sanitize_text_field($_REQUEST['switch_guava_dark_mode_bg']));
        }
        if(isset($_REQUEST['switch_guava_light_mode_icon_color'])){
            $this->base_admin->settings->updateSettings("switch_guava_light_mode_icon_color", sanitize_text_field($_REQUEST['switch_guava_light_mode_icon_color']));
        }
        if(isset($_REQUEST['switch_guava_dark_mode_icon_color'])){
            $this->base_admin->settings->updateSettings("switch_guava_dark_mode_icon_color", sanitize_text_field($_REQUEST['switch_guava_dark_mode_icon_color']));
        }
        /* Switch Honeydew*/
        if(isset($_REQUEST['switch_honeydew_width'])){
            $this->base_admin->settings->updateSettings("switch_honeydew_width", sanitize_text_field($_REQUEST['switch_honeydew_width']));
        }
        if(isset($_REQUEST['switch_honeydew_height'])){
            $this->base_admin->settings->updateSettings("switch_honeydew_height", sanitize_text_field($_REQUEST['switch_honeydew_height']));
        }
        if(isset($_REQUEST['switch_honeydew_icon_plate_width'])){
            $this->base_admin->settings->updateSettings("switch_honeydew_icon_plate_width", sanitize_text_field($_REQUEST['switch_honeydew_icon_plate_width']));
        }
        if(isset($_REQUEST['switch_honeydew_icon_plate_margin'])){
            $this->base_admin->settings->updateSettings("switch_honeydew_icon_plate_margin", sanitize_text_field($_REQUEST['switch_honeydew_icon_plate_margin']));
        }
        if(isset($_REQUEST['switch_honeydew_icon_width'])){
            $this->base_admin->settings->updateSettings("switch_honeydew_icon_width", sanitize_text_field($_REQUEST['switch_honeydew_icon_width']));
        }
        if(isset($_REQUEST['switch_honeydew_light_mode_icon_plate_bg'])){
            $this->base_admin->settings->updateSettings("switch_honeydew_light_mode_icon_plate_bg", sanitize_text_field($_REQUEST['switch_honeydew_light_mode_icon_plate_bg']));
        }
        if(isset($_REQUEST['switch_honeydew_dark_mode_icon_plate_bg'])){
            $this->base_admin->settings->updateSettings("switch_honeydew_dark_mode_icon_plate_bg", sanitize_text_field($_REQUEST['switch_honeydew_dark_mode_icon_plate_bg']));
        }
        if(isset($_REQUEST['switch_honeydew_light_mode_icon_color'])){
            $this->base_admin->settings->updateSettings("switch_honeydew_light_mode_icon_color", sanitize_text_field($_REQUEST['switch_honeydew_light_mode_icon_color']));
        }
        if(isset($_REQUEST['switch_honeydew_dark_mode_icon_color'])){
            $this->base_admin->settings->updateSettings("switch_honeydew_dark_mode_icon_color", sanitize_text_field($_REQUEST['switch_honeydew_dark_mode_icon_color']));
        }
        /* Switch Incaberry*/
        if(isset($_REQUEST['switch_incaberry_width'])){
            $this->base_admin->settings->updateSettings("switch_incaberry_width", sanitize_text_field($_REQUEST['switch_incaberry_width']));
        }
        if(isset($_REQUEST['switch_incaberry_height'])){
            $this->base_admin->settings->updateSettings("switch_incaberry_height", sanitize_text_field($_REQUEST['switch_incaberry_height']));
        }
        if(isset($_REQUEST['switch_incaberry_icon_plate_width'])){
            $this->base_admin->settings->updateSettings("switch_incaberry_icon_plate_width", sanitize_text_field($_REQUEST['switch_incaberry_icon_plate_width']));
        }
        if(isset($_REQUEST['switch_incaberry_icon_plate_margin'])){
            $this->base_admin->settings->updateSettings("switch_incaberry_icon_plate_margin", sanitize_text_field($_REQUEST['switch_incaberry_icon_plate_margin']));
        }
        if(isset($_REQUEST['switch_incaberry_icon_width'])){
            $this->base_admin->settings->updateSettings("switch_incaberry_icon_width", sanitize_text_field($_REQUEST['switch_incaberry_icon_width']));
        }
        if(isset($_REQUEST['switch_incaberry_light_mode_icon_plate_bg'])){
            $this->base_admin->settings->updateSettings("switch_incaberry_light_mode_icon_plate_bg", sanitize_text_field($_REQUEST['switch_incaberry_light_mode_icon_plate_bg']));
        }
        if(isset($_REQUEST['switch_incaberry_dark_mode_icon_plate_bg'])){
            $this->base_admin->settings->updateSettings("switch_incaberry_dark_mode_icon_plate_bg", sanitize_text_field($_REQUEST['switch_incaberry_dark_mode_icon_plate_bg']));
        }
        if(isset($_REQUEST['switch_incaberry_light_mode_icon_color'])){
            $this->base_admin->settings->updateSettings("switch_incaberry_light_mode_icon_color", sanitize_text_field($_REQUEST['switch_incaberry_light_mode_icon_color']));
        }
        if(isset($_REQUEST['switch_incaberry_dark_mode_icon_color'])){
            $this->base_admin->settings->updateSettings("switch_incaberry_dark_mode_icon_color", sanitize_text_field($_REQUEST['switch_incaberry_dark_mode_icon_color']));
        }
        /* Switch Jackfruit*/
        if (isset($_REQUEST['switch_jackfruit_width'])) {
            $this->base_admin->settings->updateSettings("switch_jackfruit_width", sanitize_text_field($_REQUEST['switch_jackfruit_width']));
        }
        if (isset($_REQUEST['switch_jackfruit_height'])) {
            $this->base_admin->settings->updateSettings("switch_jackfruit_height", sanitize_text_field($_REQUEST['switch_jackfruit_height']));
        }
        if (isset($_REQUEST['switch_jackfruit_icon_plate_width'])) {
            $this->base_admin->settings->updateSettings("switch_jackfruit_icon_plate_width", sanitize_text_field($_REQUEST['switch_jackfruit_icon_plate_width']));
        }
        if (isset($_REQUEST['switch_jackfruit_icon_plate_margin'])) {
            $this->base_admin->settings->updateSettings("switch_jackfruit_icon_plate_margin", sanitize_text_field($_REQUEST['switch_jackfruit_icon_plate_margin']));
        }
        if (isset($_REQUEST['switch_jackfruit_icon_width'])) {
            $this->base_admin->settings->updateSettings("switch_jackfruit_icon_width", sanitize_text_field($_REQUEST['switch_jackfruit_icon_width']));
        }
        if (isset($_REQUEST['switch_jackfruit_light_mode_bg'])) {
            $this->base_admin->settings->updateSettings("switch_jackfruit_light_mode_bg", sanitize_text_field($_REQUEST['switch_jackfruit_light_mode_bg']));
        }
        if (isset($_REQUEST['switch_jackfruit_dark_mode_bg'])) {
            $this->base_admin->settings->updateSettings("switch_jackfruit_dark_mode_bg", sanitize_text_field($_REQUEST['switch_jackfruit_dark_mode_bg']));
        }
        if (isset($_REQUEST['switch_jackfruit_light_mode_icon_plate_bg'])) {
            $this->base_admin->settings->updateSettings("switch_jackfruit_light_mode_icon_plate_bg", sanitize_text_field($_REQUEST['switch_jackfruit_light_mode_icon_plate_bg']));
        }
        if (isset($_REQUEST['switch_jackfruit_dark_mode_icon_plate_bg'])) {
            $this->base_admin->settings->updateSettings("switch_jackfruit_dark_mode_icon_plate_bg", sanitize_text_field($_REQUEST['switch_jackfruit_dark_mode_icon_plate_bg']));
        }
        if (isset($_REQUEST['switch_jackfruit_light_mode_light_icon_color'])) {
            $this->base_admin->settings->updateSettings("switch_jackfruit_light_mode_light_icon_color", sanitize_text_field($_REQUEST['switch_jackfruit_light_mode_light_icon_color']));
        }
        if (isset($_REQUEST['switch_jackfruit_light_mode_dark_icon_color'])) {
            $this->base_admin->settings->updateSettings("switch_jackfruit_light_mode_dark_icon_color", sanitize_text_field($_REQUEST['switch_jackfruit_light_mode_dark_icon_color']));
        }
        if (isset($_REQUEST['switch_jackfruit_dark_mode_light_icon_color'])) {
            $this->base_admin->settings->updateSettings("switch_jackfruit_dark_mode_light_icon_color", sanitize_text_field($_REQUEST['switch_jackfruit_dark_mode_light_icon_color']));
        }
        if (isset($_REQUEST['switch_jackfruit_dark_mode_dark_icon_color'])) {
            $this->base_admin->settings->updateSettings("switch_jackfruit_dark_mode_dark_icon_color", sanitize_text_field($_REQUEST['switch_jackfruit_dark_mode_dark_icon_color']));
        }



        /* Preset */
        if(isset($_REQUEST['dark_mode_color_preset'])){
            $this->base_admin->settings->updateSettings("dark_mode_color_preset", sanitize_text_field($_REQUEST['dark_mode_color_preset']));
        }
        if(isset($_REQUEST['dark_mode_bg'])){
            $this->base_admin->settings->updateSettings("dark_mode_bg", sanitize_text_field($_REQUEST['dark_mode_bg']));
        }
        if(isset($_REQUEST['dark_mode_secondary_bg'])){
            $this->base_admin->settings->updateSettings("dark_mode_secondary_bg", sanitize_text_field($_REQUEST['dark_mode_secondary_bg']));
        }
        if(isset($_REQUEST['dark_mode_text_color'])){
            $this->base_admin->settings->updateSettings("dark_mode_text_color", sanitize_text_field($_REQUEST['dark_mode_text_color']));
        }
        if(isset($_REQUEST['dark_mode_link_color'])){
            $this->base_admin->settings->updateSettings("dark_mode_link_color", sanitize_text_field($_REQUEST['dark_mode_link_color']));
        }
        if(isset($_REQUEST['dark_mode_link_hover_color'])){
            $this->base_admin->settings->updateSettings("dark_mode_link_hover_color", sanitize_text_field($_REQUEST['dark_mode_link_hover_color']));
        }
        if(isset($_REQUEST['dark_mode_input_bg'])){
            $this->base_admin->settings->updateSettings("dark_mode_input_bg", sanitize_text_field($_REQUEST['dark_mode_input_bg']));
        }
        if(isset($_REQUEST['dark_mode_input_text_color'])){
            $this->base_admin->settings->updateSettings("dark_mode_input_text_color", sanitize_text_field($_REQUEST['dark_mode_input_text_color']));
        }
        if(isset($_REQUEST['dark_mode_input_placeholder_color'])){
            $this->base_admin->settings->updateSettings("dark_mode_input_placeholder_color", sanitize_text_field($_REQUEST['dark_mode_input_placeholder_color']));
        }
        if(isset($_REQUEST['dark_mode_border_color'])){
            $this->base_admin->settings->updateSettings("dark_mode_border_color", sanitize_text_field($_REQUEST['dark_mode_border_color']));
        }
        if(isset($_REQUEST['dark_mode_btn_bg'])){
            $this->base_admin->settings->updateSettings("dark_mode_btn_bg", sanitize_text_field($_REQUEST['dark_mode_btn_bg']));
        }
        if(isset($_REQUEST['dark_mode_btn_text_color'])){
            $this->base_admin->settings->updateSettings("dark_mode_btn_text_color", sanitize_text_field($_REQUEST['dark_mode_btn_text_color']));
        }
        if(isset($_REQUEST['enable_scrollbar_dark'])){
            $this->base_admin->settings->updateSettings("enable_scrollbar_dark", sanitize_text_field($_REQUEST['enable_scrollbar_dark']));
        }
        if(isset($_REQUEST['dark_mode_scrollbar_track_bg'])){
            $this->base_admin->settings->updateSettings("dark_mode_scrollbar_track_bg", sanitize_text_field($_REQUEST['dark_mode_scrollbar_track_bg']));
        }
        if(isset($_REQUEST['dark_mode_scrollbar_thumb_bg'])){
            $this->base_admin->settings->updateSettings("dark_mode_scrollbar_thumb_bg", sanitize_text_field($_REQUEST['dark_mode_scrollbar_thumb_bg']));
        }


        /* Media */
        if(isset($_REQUEST['enable_low_image_brightness'])){
            $this->base_admin->settings->updateSettings("enable_low_image_brightness", sanitize_text_field($_REQUEST['enable_low_image_brightness']));
        }
        if(isset($_REQUEST['image_brightness_to'])){
            $this->base_admin->settings->updateSettings("image_brightness_to", sanitize_text_field($_REQUEST['image_brightness_to']));
        }
        if(isset($_REQUEST['disallowed_low_brightness_images'])){
            $this->base_admin->settings->updateSettings("disallowed_low_brightness_images", sanitize_text_field($_REQUEST['disallowed_low_brightness_images']));
        }
        if(isset($_REQUEST['enable_image_grayscale'])){
            $this->base_admin->settings->updateSettings("enable_image_grayscale", sanitize_text_field($_REQUEST['enable_image_grayscale']));
        }
        if(isset($_REQUEST['image_grayscale_to'])){
            $this->base_admin->settings->updateSettings("image_grayscale_to", sanitize_text_field($_REQUEST['image_grayscale_to']));
        }
        if(isset($_REQUEST['disallowed_grayscale_images'])){
            $this->base_admin->settings->updateSettings("disallowed_grayscale_images", sanitize_text_field($_REQUEST['disallowed_grayscale_images']));
        }
        if(isset($_REQUEST['enable_bg_image_darken'])){
            $this->base_admin->settings->updateSettings("enable_bg_image_darken", sanitize_text_field($_REQUEST['enable_bg_image_darken']));
        }
        if(isset($_REQUEST['bg_image_darken_to'])){
            $this->base_admin->settings->updateSettings("bg_image_darken_to", sanitize_text_field($_REQUEST['bg_image_darken_to']));
        }
        if(isset($_REQUEST['enable_invert_inline_svg'])){
            $this->base_admin->settings->updateSettings("enable_invert_inline_svg", sanitize_text_field($_REQUEST['enable_invert_inline_svg']));
        }
        if(isset($_REQUEST['enable_invert_images'])){
            $this->base_admin->settings->updateSettings("enable_invert_images", sanitize_text_field($_REQUEST['enable_invert_images']));
        }
        if(isset($_REQUEST['invert_images_allowed_urls'])){
            $this->base_admin->settings->updateSettings("invert_images_allowed_urls", sanitize_text_field($_REQUEST['invert_images_allowed_urls']));
        }
        if(isset($_REQUEST['image_replacements'])){
            $this->base_admin->settings->updateSettings("image_replacements", sanitize_text_field($_REQUEST['image_replacements']));
        }


        /* Video */
        if(isset($_REQUEST['enable_low_video_brightness'])){
            $this->base_admin->settings->updateSettings("enable_low_video_brightness", sanitize_text_field($_REQUEST['enable_low_video_brightness']));
        }
        if(isset($_REQUEST['video_brightness_to'])){
            $this->base_admin->settings->updateSettings("video_brightness_to", sanitize_text_field($_REQUEST['video_brightness_to']));
        }
        if(isset($_REQUEST['enable_video_grayscale'])){
            $this->base_admin->settings->updateSettings("enable_video_grayscale", sanitize_text_field($_REQUEST['enable_video_grayscale']));
        }
        if(isset($_REQUEST['video_grayscale_to'])){
            $this->base_admin->settings->updateSettings("video_grayscale_to", sanitize_text_field($_REQUEST['video_grayscale_to']));
        }
        if(isset($_REQUEST['video_replacements'])){
            $this->base_admin->settings->updateSettings("video_replacements", sanitize_text_field($_REQUEST['video_replacements']));
        }


        /* Restriction */
        if(isset($_REQUEST['allowed_elements'])){
            $this->base_admin->settings->updateSettings("allowed_elements", sanitize_text_field($_REQUEST['allowed_elements']));
        }
        if(isset($_REQUEST['allowed_elements_force_to_correct'])){
            $this->base_admin->settings->updateSettings("allowed_elements_force_to_correct", sanitize_text_field($_REQUEST['allowed_elements_force_to_correct']));
        }
        if(isset($_REQUEST['disallowed_elements'])){
            $this->base_admin->settings->updateSettings("disallowed_elements", sanitize_text_field($_REQUEST['disallowed_elements']));
        }
        if(isset($_REQUEST['disallowed_elements_force_to_correct'])){
            $this->base_admin->settings->updateSettings("disallowed_elements_force_to_correct", sanitize_text_field($_REQUEST['disallowed_elements_force_to_correct']));
        }
        if(isset($_REQUEST['allowed_pages'])){
            $this->base_admin->settings->updateSettings("allowed_pages", sanitize_text_field($_REQUEST['allowed_pages']));
        }
        if(isset($_REQUEST['disallowed_pages'])){
            $this->base_admin->settings->updateSettings("disallowed_pages", sanitize_text_field($_REQUEST['disallowed_pages']));
        }
        if(isset($_REQUEST['allowed_posts'])){
            $this->base_admin->settings->updateSettings("allowed_posts", sanitize_text_field($_REQUEST['allowed_posts']));
        }
        if(isset($_REQUEST['disallowed_posts'])){
            $this->base_admin->settings->updateSettings("disallowed_posts", sanitize_text_field($_REQUEST['disallowed_posts']));
        }
        if(isset($_REQUEST['custom_css'])){
            $this->base_admin->settings->updateSettings("custom_css", wp_filter_post_kses($_REQUEST['custom_css']));
        }
        if(isset($_REQUEST['custom_css_apply_on_children'])){
            $this->base_admin->settings->updateSettings("custom_css_apply_on_children", sanitize_text_field($_REQUEST['custom_css_apply_on_children']));
        }
        if(isset($_REQUEST['normal_custom_css'])){
            $this->base_admin->settings->updateSettings("normal_custom_css", wp_filter_post_kses($_REQUEST['normal_custom_css']));
        }


        /* License */
        if(isset($_REQUEST['enable_support_team_modify_settings'])){
            $this->base_admin->settings->updateSettings("enable_support_team_modify_settings", sanitize_text_field($_REQUEST['enable_support_team_modify_settings']));
        }


        $result = array("status" => "true");

    }else{
        $result = array("status" => 'false');
    }
}else{
    $result = array("status" => 'false');
}

echo json_encode($result,  JSON_UNESCAPED_UNICODE);