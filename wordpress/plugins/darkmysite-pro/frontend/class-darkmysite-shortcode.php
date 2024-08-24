<?php

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

if (!class_exists('DarkMySiteShortcodeParser')) {
    class DarkMySiteShortcodeParser
    {

        public $base_client;

        public function __construct($base_client)
        {
            $this->base_client = $base_client;

            add_shortcode( 'darkmysite', array($this, 'darkmysite_shortcode_parser') );
        }


        public function darkmysite_shortcode_parser( $atts , $content = null) {
            if(!isset($atts['switch'])){
                $atts = array();
                $atts['switch'] = '1';
            }
            return $this->darkmysite_client_view_maker($atts);
        }


        public function darkmysite_client_view_maker( $atts ) {
            $unique_id = rand()."_shortcode";
            $switch_styles = $this->base_client->utils->generateSwitchStylesForShortcode($atts);
            ob_start(); ?>

            <?php if(sizeof($switch_styles) > 0) { ?>
                <style type="text/css">
                    #darkmysite_switch_<?php echo esc_attr($unique_id);?> {
                    <?php foreach($switch_styles as $key => $value ){ ?>
                    <?php echo esc_attr($key); ?>: <?php echo esc_attr($value); ?>;
                    <?php } ?>
                    }
                </style>
            <?php } ?>

            <?php if($atts['switch'] == "1") { ?>
                <div id="darkmysite_switch_<?php echo esc_attr($unique_id);?>" class="darkmysite_switch darkmysite_switch_apple" style="position: relative;" onclick="darkmysite_switch_trigger()">
                    <span class="darkmysite_switch_icon"></span>
                </div>
            <?php } ?>
            <?php if($atts['switch'] == "2") { ?>
                <div id="darkmysite_switch_<?php echo esc_attr($unique_id);?>" class="darkmysite_switch darkmysite_switch_banana" style="position: relative;" onclick="darkmysite_switch_trigger()">
                    <span class="darkmysite_switch_icon"></span>
                </div>
            <?php } ?>
            <?php if($atts['switch'] == "3") { ?>
                <div id="darkmysite_switch_<?php echo esc_attr($unique_id);?>" class="darkmysite_switch darkmysite_switch_cherry" style="position: relative;" onclick="darkmysite_switch_trigger()">
                    <span class="darkmysite_switch_icon"></span>
                </div>
            <?php } ?>
            <?php if($atts['switch'] == "4") { ?>
                <div id="darkmysite_switch_<?php echo esc_attr($unique_id);?>" class="darkmysite_switch darkmysite_switch_durian" style="position: relative;" onclick="darkmysite_switch_trigger()">
                    <span class="darkmysite_switch_icon"></span>
                </div>
            <?php } ?>
            <?php if($atts['switch'] == "5") { ?>
                <div id="darkmysite_switch_<?php echo esc_attr($unique_id);?>" class="darkmysite_switch darkmysite_switch_elderberry" style="position: relative;" onclick="darkmysite_switch_trigger()">
                    <div class="darkmysite_switch_plate">
                        <span class="darkmysite_switch_icon"></span>
                    </div>
                </div>
            <?php } ?>
            <?php if($atts['switch'] == "6") { ?>
                <div id="darkmysite_switch_<?php echo esc_attr($unique_id);?>" class="darkmysite_switch darkmysite_switch_fazli" style="position: relative;" onclick="darkmysite_switch_trigger()">
                    <div class="darkmysite_switch_plate">
                        <span class="darkmysite_switch_icon"></span>
                    </div>
                </div>
            <?php } ?>
            <?php if($atts['switch'] == "7") { ?>
                <div id="darkmysite_switch_<?php echo esc_attr($unique_id);?>" class="darkmysite_switch darkmysite_switch_guava" style="position: relative;" onclick="darkmysite_switch_trigger()">
                    <span class="darkmysite_switch_icon"></span>
                </div>
            <?php } ?>
            <?php if($atts['switch'] == "8") { ?>
                <div id="darkmysite_switch_<?php echo esc_attr($unique_id);?>" class="darkmysite_switch darkmysite_switch_honeydew" style="position: relative;" onclick="darkmysite_switch_trigger()">
                    <div class="darkmysite_switch_plate">
                        <span class="darkmysite_switch_icon"></span>
                    </div>
                </div>
            <?php } ?>
            <?php if($atts['switch'] == "9") { ?>
                <div id="darkmysite_switch_<?php echo esc_attr($unique_id);?>" class="darkmysite_switch darkmysite_switch_incaberry" style="position: relative;" onclick="darkmysite_switch_trigger()">
                    <div class="darkmysite_switch_plate">
                        <span class="darkmysite_switch_icon"></span>
                    </div>
                </div>
            <?php } ?>
            <?php if($atts['switch'] == "10") { ?>
                <div id="darkmysite_switch_<?php echo esc_attr($unique_id);?>" class="darkmysite_switch darkmysite_switch_jackfruit" style="position: relative;" onclick="darkmysite_switch_trigger()">
                    <div class="darkmysite_switch_plate">
                        <span class="darkmysite_switch_icon"></span>
                    </div>
                    <div class="darkmysite_switch_secondary_plate">
                        <span class="darkmysite_switch_icon"></span>
                    </div>
                </div>
            <?php } ?>

        <?php return ob_get_clean();
        }

    }
}
