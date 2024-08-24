<?php ob_start(); ?>

<?php if(!is_admin()){ ?>
    <style type="text/css" class="darkmysite_inline_css">
        :root {
            --darkmysite_dark_mode_bg: <?php echo esc_attr($this->data_settings["dark_mode_bg"]); ?>;
            --darkmysite_dark_mode_secondary_bg: <?php echo esc_attr($this->data_settings["dark_mode_secondary_bg"]); ?>;
            --darkmysite_dark_mode_text_color: <?php echo esc_attr($this->data_settings["dark_mode_text_color"]); ?>;
            --darkmysite_dark_mode_link_color: <?php echo esc_attr($this->data_settings["dark_mode_link_color"]); ?>;
            --darkmysite_dark_mode_link_hover_color: <?php echo esc_attr($this->data_settings["dark_mode_link_hover_color"]); ?>;
            --darkmysite_dark_mode_input_bg: <?php echo esc_attr($this->data_settings["dark_mode_input_bg"]); ?>;
            --darkmysite_dark_mode_input_text_color: <?php echo esc_attr($this->data_settings["dark_mode_input_text_color"]); ?>;
            --darkmysite_dark_mode_input_placeholder_color: <?php echo esc_attr($this->data_settings["dark_mode_input_placeholder_color"]); ?>;
            --darkmysite_dark_mode_border_color: <?php echo esc_attr($this->data_settings["dark_mode_border_color"]); ?>;
            --darkmysite_dark_mode_btn_bg: <?php echo esc_attr($this->data_settings["dark_mode_btn_bg"]); ?>;
            --darkmysite_dark_mode_btn_text_color: <?php echo esc_attr($this->data_settings["dark_mode_btn_text_color"]); ?>;
        }
    </style>
<?php }else{ ?>
    <style type="text/css" class="darkmysite_inline_css">
        :root {
            --darkmysite_dark_mode_bg: #181a1b;
            --darkmysite_dark_mode_secondary_bg: #202324;
            --darkmysite_dark_mode_text_color: #c8c4bd;
            --darkmysite_dark_mode_link_color: #6aafe2;
            --darkmysite_dark_mode_link_hover_color: #4f94c3;
            --darkmysite_dark_mode_input_bg: #2D2D2D;
            --darkmysite_dark_mode_input_text_color: #BEBEBE;
            --darkmysite_dark_mode_input_placeholder_color: #989898;
            --darkmysite_dark_mode_border_color: #4A4A4A;
            --darkmysite_dark_mode_btn_bg: #2D2D2D;
            --darkmysite_dark_mode_btn_text_color: #BEBEBE;
        }
    </style>
<?php } ?>


<?php if($this->data_settings["enable_scrollbar_dark"] == "1") { ?>
<?php include DARKMYSITE_PRO_PATH . "frontend/templates/views/scrollbar.php"; ?>
<?php } ?>


    <script type="text/javascript" class="darkmysite_inline_js">
        var darkmysite_switch_unique_id = "<?php echo esc_attr($this->unique_id); ?>";

        var darkmysite_is_this_admin_panel = "<?php echo esc_attr(is_admin() ? "1" : "0"); ?>";
        var darkmysite_enable_default_dark_mode = "<?php echo esc_attr($this->data_settings["enable_default_dark_mode"]); ?>";
        var darkmysite_enable_os_aware = "<?php echo esc_attr($this->data_settings["enable_os_aware"]); ?>";
        var darkmysite_enable_keyboard_shortcut = "<?php echo esc_attr($this->data_settings["enable_keyboard_shortcut"]); ?>";
        var darkmysite_enable_time_based_dark = "<?php echo esc_attr($this->data_settings["enable_time_based_dark"]); ?>";
        var darkmysite_time_based_dark_start = "<?php echo esc_attr($this->data_settings["time_based_dark_start"]); ?>";
        var darkmysite_time_based_dark_stop = "<?php echo esc_attr($this->data_settings["time_based_dark_stop"]); ?>";

        var darkmysite_enable_switch_dragging = "<?php echo esc_attr($this->data_settings["enable_switch_dragging"]); ?>";
        var darkmysite_alternative_dark_mode_switch = "<?php echo esc_attr($this->data_settings["alternative_dark_mode_switch"]); ?>";

        var darkmysite_enable_low_image_brightness = "<?php echo esc_attr($this->data_settings["enable_low_image_brightness"]); ?>";
        var darkmysite_image_brightness_to = "<?php echo esc_attr($this->data_settings["image_brightness_to"]); ?>";
        var darkmysite_disallowed_low_brightness_images = "<?php echo esc_attr($this->data_settings["disallowed_low_brightness_images"]); ?>";
        var darkmysite_enable_image_grayscale = "<?php echo esc_attr($this->data_settings["enable_image_grayscale"]); ?>";
        var darkmysite_image_grayscale_to = "<?php echo esc_attr($this->data_settings["image_grayscale_to"]); ?>";
        var darkmysite_disallowed_grayscale_images = "<?php echo esc_attr($this->data_settings["disallowed_grayscale_images"]); ?>";
        var darkmysite_enable_bg_image_darken = "<?php echo esc_attr($this->data_settings["enable_bg_image_darken"]); ?>";
        var darkmysite_bg_image_darken_to = "<?php echo esc_attr($this->data_settings["bg_image_darken_to"]); ?>";
        var darkmysite_enable_invert_inline_svg = "<?php echo esc_attr($this->data_settings["enable_invert_inline_svg"]); ?>";
        var darkmysite_enable_invert_images = "<?php echo esc_attr($this->data_settings["enable_invert_images"]); ?>";
        var darkmysite_invert_images_allowed_urls = "<?php echo esc_attr($this->data_settings["invert_images_allowed_urls"]); ?>";
        var darkmysite_image_replacements = "<?php echo esc_attr($this->data_settings["image_replacements"]); ?>";

        var darkmysite_enable_low_video_brightness = "<?php echo esc_attr($this->data_settings["enable_low_video_brightness"]); ?>";
        var darkmysite_video_brightness_to = "<?php echo esc_attr($this->data_settings["video_brightness_to"]); ?>";
        var darkmysite_enable_video_grayscale = "<?php echo esc_attr($this->data_settings["enable_video_grayscale"]); ?>";
        var darkmysite_video_grayscale_to = "<?php echo esc_attr($this->data_settings["video_grayscale_to"]); ?>";
        var darkmysite_video_replacements = "<?php echo esc_attr($this->data_settings["video_replacements"]); ?>";

        var darkmysite_allowed_elements = "<?php echo esc_attr($this->utils->generateAllowedElementsStr($this->data_settings)); ?>";
        var darkmysite_allowed_elements_raw = "<?php echo esc_attr($this->data_settings["allowed_elements"]); ?>";
        var darkmysite_allowed_elements_force_to_correct = "<?php echo esc_attr($this->data_settings["allowed_elements_force_to_correct"]); ?>";
        var darkmysite_disallowed_elements = "<?php echo esc_attr($this->utils->generateDisallowedElementsStr($this->data_settings, $this->external_support)); ?>";
        var darkmysite_disallowed_elements_raw = "<?php echo esc_attr($this->data_settings["disallowed_elements"]); ?>";
        var darkmysite_disallowed_elements_force_to_correct = "<?php echo esc_attr($this->data_settings["disallowed_elements_force_to_correct"]); ?>";
    </script>

<?php
$output = ob_get_clean();

$tags_allowed_in_output = array(
    'style' => array('type' => array(), 'class' => array()),
    'script' => array('type' => array(), 'class' => array())
);
echo wp_kses($this->utils->minify_string($output), $tags_allowed_in_output);
?>