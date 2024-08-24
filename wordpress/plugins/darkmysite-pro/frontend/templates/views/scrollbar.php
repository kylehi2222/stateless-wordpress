<style type="text/css" class="darkmysite_inline_css">
    .darkmysite_dark_mode_enabled::-webkit-scrollbar {
        background: <?php echo esc_attr($this->data_settings["dark_mode_scrollbar_track_bg"]); ?> !important;
    }
    .darkmysite_dark_mode_enabled::-webkit-scrollbar-track {
        background: <?php echo esc_attr($this->data_settings["dark_mode_scrollbar_track_bg"]); ?> !important;
    }
    .darkmysite_dark_mode_enabled::-webkit-scrollbar-thumb {
        background-color: <?php echo esc_attr($this->data_settings["dark_mode_scrollbar_thumb_bg"]); ?> !important;
    }
    .darkmysite_dark_mode_enabled::-webkit-scrollbar-corner {
        background-color: <?php echo esc_attr($this->data_settings["dark_mode_scrollbar_thumb_bg"]); ?> !important;
    }
    .darkmysite_dark_mode_enabled::-webkit-scrollbar-button {
        background-color: transparent !important;
        background-repeat: no-repeat !important;
        background-size: contain !important;
        background-position: center !important;
    }
    .darkmysite_dark_mode_enabled::-webkit-scrollbar-button:start {
        background-image: url(<?php echo esc_url(DARKMYSITE_PRO_IMG_DIR . "others/scroll_arrow_up.svg"); ?>) !important;
    }
    .darkmysite_dark_mode_enabled::-webkit-scrollbar-button:end {
        background-image: url(<?php echo esc_url(DARKMYSITE_PRO_IMG_DIR . "others/scroll_arrow_down.svg"); ?>) !important;
    }
    .darkmysite_dark_mode_enabled::-webkit-scrollbar-button:start:horizontal {
        background-image: url(<?php echo esc_url(DARKMYSITE_PRO_IMG_DIR . "others/scroll_arrow_left.svg"); ?>) !important;
    }
    .darkmysite_dark_mode_enabled::-webkit-scrollbar-button:end:horizontal {
        background-image: url(<?php echo esc_url(DARKMYSITE_PRO_IMG_DIR . "others/scroll_arrow_right.svg"); ?>) !important;
    }
</style>