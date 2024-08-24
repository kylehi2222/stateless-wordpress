<div id="darkmysite_switch" style="display: none;">

    <div class="darkmysite_body_header">
        <div class="darkmysite_body_header_details">
            <div class="darkmysite_body_header_details_logo darkmysite_ignore">
                <img src="<?php echo esc_url(DARKMYSITE_PRO_IMG_DIR . "sidebar/sidebar_menu_switch.svg") ?>">
            </div>
            <div class="darkmysite_body_header_details_headline">
                <h2>SWITCH STYLE</h2>
                <p>Select and Customize Floating Switch</p>
            </div>
        </div>
        <button class="darkmysite_body_header_save_btn darkmysite_ignore" onclick="darkmysite_save()">SAVE CHANGES</button>
    </div>



    <div class="darkmysite_body_header_separator darkmysite_ignore"></div>




    <div class="darkmysite_section_header">
        <h3>Switch Design</h3>
        <p>Choose Default Dark Mode Floating Switch</p>
    </div>
    <div class="darkmysite_section_block">
        <div class="darkmysite_switch_items darkmysite_dark_mode_switch_design darkmysite_ignore" data-switch_id="<?php echo esc_attr($settings["dark_mode_switch_design"]) ?>">
            <div class="darkmysite_switch_item <?php echo esc_attr($settings["dark_mode_switch_design"] == "apple" ? "active" : "") ?>" onclick="darkmysite_switch_design_click(this, `apple`)">
                <img width="55px" src="<?php echo esc_url(DARKMYSITE_PRO_IMG_DIR . "switch/switch_apple.png") ?>">
            </div>
            <div class="darkmysite_switch_item <?php echo esc_attr($settings["dark_mode_switch_design"] == "banana" ? "active" : "") ?>" onclick="darkmysite_switch_design_click(this, `banana`)">
                <img width="55px" src="<?php echo esc_url(DARKMYSITE_PRO_IMG_DIR . "switch/switch_banana.png") ?>">
            </div>
            <div class="darkmysite_switch_item <?php echo esc_attr($settings["dark_mode_switch_design"] == "cherry" ? "active" : "") ?>" onclick="darkmysite_switch_design_click(this, `cherry`)">
                <img width="55px" src="<?php echo esc_url(DARKMYSITE_PRO_IMG_DIR . "switch/switch_cherry.png") ?>">
            </div>
            <div class="darkmysite_switch_item <?php echo esc_attr($settings["dark_mode_switch_design"] == "durian" ? "active" : "") ?>" onclick="darkmysite_switch_design_click(this, `durian`)">
                <img width="55px" src="<?php echo esc_url(DARKMYSITE_PRO_IMG_DIR . "switch/switch_durian.png") ?>">
            </div>
            <div class="darkmysite_switch_item <?php echo esc_attr($settings["dark_mode_switch_design"] == "elderberry" ? "active" : "") ?>" onclick="darkmysite_switch_design_click(this, `elderberry`)">
                <img width="85px" src="<?php echo esc_url(DARKMYSITE_PRO_IMG_DIR . "switch/switch_elderberry.png") ?>">
            </div>
            <div class="darkmysite_switch_item <?php echo esc_attr($settings["dark_mode_switch_design"] == "fazli" ? "active" : "") ?>" onclick="darkmysite_switch_design_click(this, `fazli`)">
                <img width="85px" src="<?php echo esc_url(DARKMYSITE_PRO_IMG_DIR . "switch/switch_fazli.png") ?>">
            </div>
            <div class="darkmysite_switch_item <?php echo esc_attr($settings["dark_mode_switch_design"] == "guava" ? "active" : "") ?>" onclick="darkmysite_switch_design_click(this, `guava`)">
                <img width="85px" src="<?php echo esc_url(DARKMYSITE_PRO_IMG_DIR . "switch/switch_guava.png") ?>">
            </div>
            <div class="darkmysite_switch_item <?php echo esc_attr($settings["dark_mode_switch_design"] == "honeydew" ? "active" : "") ?>" onclick="darkmysite_switch_design_click(this, `honeydew`)">
                <img width="85px" src="<?php echo esc_url(DARKMYSITE_PRO_IMG_DIR . "switch/switch_honeydew.png") ?>">
            </div>
            <div class="darkmysite_switch_item <?php echo esc_attr($settings["dark_mode_switch_design"] == "incaberry" ? "active" : "") ?>" onclick="darkmysite_switch_design_click(this, `incaberry`)">
                <img width="85px" src="<?php echo esc_url(DARKMYSITE_PRO_IMG_DIR . "switch/switch_incaberry.png") ?>">
            </div>
            <div class="darkmysite_switch_item <?php echo esc_attr($settings["dark_mode_switch_design"] == "jackfruit" ? "active" : "") ?>" onclick="darkmysite_switch_design_click(this, `jackfruit`)">
                <img width="70px" src="<?php echo esc_url(DARKMYSITE_PRO_IMG_DIR . "switch/switch_jackfruit.png") ?>">
            </div>
        </div>
    </div>






    <div class="darkmysite_section_header">
        <h3>Position Customization</h3>
        <p>Customize the Floating Switch Position for perfect placement</p>
    </div>
    <div class="darkmysite_section_block">
        <div class="darkmysite_input_select_setting darkmysite_dark_mode_switch_position">
            <div class="darkmysite_input_select_setting_details">
                <h4>Switch Position</h4>
                <p>Choose the screen position where the Floating Switch should be displayed.</p>
            </div>
            <select onchange="darkmysite_switch_position_change(this)">
                <option <?php echo esc_attr($settings["dark_mode_switch_position"] == "top_right" ? "selected" : "") ?> value="top_right">Top Right</option>
                <option <?php echo esc_attr($settings["dark_mode_switch_position"] == "top_left" ? "selected" : "") ?> value="top_left">Top Left</option>
                <option <?php echo esc_attr($settings["dark_mode_switch_position"] == "bottom_right" ? "selected" : "") ?> value="bottom_right">Bottom Right</option>
                <option <?php echo esc_attr($settings["dark_mode_switch_position"] == "bottom_left" ? "selected" : "") ?> value="bottom_left">Bottom Left</option>
            </select>
        </div>
        <div class="darkmysite_section_block_separator" style="<?php echo esc_attr(strpos($settings["dark_mode_switch_position"], "top") !== false ? "" : "display: none;") ?>"></div>
        <div class="darkmysite_input_select_setting darkmysite_dark_mode_switch_margin_top" style="<?php echo esc_attr(strpos($settings["dark_mode_switch_position"], "top") !== false ? "" : "display: none;") ?>">
            <div class="darkmysite_input_select_setting_details">
                <h4>Margin from Top</h4>
                <p>Customize default margin from the top of the Floating Switch.</p>
            </div>
            <input type="number" value="<?php echo esc_attr($settings["dark_mode_switch_margin_top"]) ?>">
        </div>
        <div class="darkmysite_section_block_separator" style="<?php echo esc_attr(strpos($settings["dark_mode_switch_position"], "bottom") !== false ? "" : "display: none;") ?>"></div>
        <div class="darkmysite_input_select_setting darkmysite_dark_mode_switch_margin_bottom" style="<?php echo esc_attr(strpos($settings["dark_mode_switch_position"], "bottom") !== false ? "" : "display: none;") ?>">
            <div class="darkmysite_input_select_setting_details">
                <h4>Margin from Bottom</h4>
                <p>Customize default margin from the bottom of the Floating Switch.</p>
            </div>
            <input type="number" value="<?php echo esc_attr($settings["dark_mode_switch_margin_bottom"]) ?>">
        </div>
        <div class="darkmysite_section_block_separator" style="<?php echo esc_attr(strpos($settings["dark_mode_switch_position"], "left") !== false ? "" : "display: none;") ?>"></div>
        <div class="darkmysite_input_select_setting darkmysite_dark_mode_switch_margin_left" style="<?php echo esc_attr(strpos($settings["dark_mode_switch_position"], "left") !== false ? "" : "display: none;") ?>">
            <div class="darkmysite_input_select_setting_details">
                <h4>Margin from Left</h4>
                <p>Customize default margin from the left of the Floating Switch.</p>
            </div>
            <input type="number" value="<?php echo esc_attr($settings["dark_mode_switch_margin_left"]) ?>">
        </div>
        <div class="darkmysite_section_block_separator" style="<?php echo esc_attr(strpos($settings["dark_mode_switch_position"], "right") !== false ? "" : "display: none;") ?>"></div>
        <div class="darkmysite_input_select_setting darkmysite_dark_mode_switch_margin_right" style="<?php echo esc_attr(strpos($settings["dark_mode_switch_position"], "right") !== false ? "" : "display: none;") ?>">
            <div class="darkmysite_input_select_setting_details">
                <h4>Margin from Right</h4>
                <p>Customize default margin from the right of the Floating Switch.</p>
            </div>
            <input type="number" value="<?php echo esc_attr($settings["dark_mode_switch_margin_right"]) ?>">
        </div>
        <div class="darkmysite_section_block_separator"></div>
        <div class="darkmysite_input_select_setting darkmysite_enable_switch_position_different_in_mobile">
            <div class="darkmysite_input_select_setting_details">
                <h4>Different Switch Position in Mobile</h4>
                <p>Should the Floating Switch be displayed on different position in mobile?</p>
            </div>
            <select onchange="darkmysite_enable_disable_mobile_switch_position(this)">
                <option <?php echo esc_attr($settings["enable_switch_position_different_in_mobile"] == "1" ? "selected" : "") ?> value="1">Yes</option>
                <option <?php echo esc_attr($settings["enable_switch_position_different_in_mobile"] == "0" ? "selected" : "") ?> value="0">No</option>
            </select>
        </div>
        <div class="darkmysite_section_block_separator" style="<?php echo esc_attr($settings["enable_switch_position_different_in_mobile"] == "1" ? "" : "display: none;") ?>"></div>
        <div class="darkmysite_input_select_setting darkmysite_dark_mode_switch_position_in_mobile" style="<?php echo esc_attr($settings["enable_switch_position_different_in_mobile"] == "1" ? "" : "display: none;") ?>">
            <div class="darkmysite_input_select_setting_details">
                <h4>Switch Position in Mobile</h4>
                <p>Choose the screen position where the Floating Switch should be displayed in mobile.</p>
            </div>
            <select onchange="darkmysite_mobile_switch_position_change(this)">
                <option <?php echo esc_attr($settings["dark_mode_switch_position_in_mobile"] == "top_right" ? "selected" : "") ?> value="top_right">Top Right</option>
                <option <?php echo esc_attr($settings["dark_mode_switch_position_in_mobile"] == "top_left" ? "selected" : "") ?> value="top_left">Top Left</option>
                <option <?php echo esc_attr($settings["dark_mode_switch_position_in_mobile"] == "bottom_right" ? "selected" : "") ?> value="bottom_right">Bottom Right</option>
                <option <?php echo esc_attr($settings["dark_mode_switch_position_in_mobile"] == "bottom_left" ? "selected" : "") ?> value="bottom_left">Bottom Left</option>
            </select>
        </div>
        <div class="darkmysite_section_block_separator" style="<?php echo esc_attr($settings["enable_switch_position_different_in_mobile"] == "1" && strpos($settings["dark_mode_switch_position_in_mobile"], "top") !== false ? "" : "display: none;") ?>"></div>
        <div class="darkmysite_input_select_setting darkmysite_dark_mode_switch_margin_top_in_mobile" style="<?php echo esc_attr($settings["enable_switch_position_different_in_mobile"] == "1" && strpos($settings["dark_mode_switch_position_in_mobile"], "top") !== false ? "" : "display: none;") ?>">
            <div class="darkmysite_input_select_setting_details">
                <h4>Margin from Top in Mobile</h4>
                <p>Customize default margin from the top of the Floating Switch in Mobile.</p>
            </div>
            <input type="number" value="<?php echo esc_attr($settings["dark_mode_switch_margin_top_in_mobile"]) ?>">
        </div>
        <div class="darkmysite_section_block_separator" style="<?php echo esc_attr($settings["enable_switch_position_different_in_mobile"] == "1" && strpos($settings["dark_mode_switch_position_in_mobile"], "bottom") !== false ? "" : "display: none;") ?>"></div>
        <div class="darkmysite_input_select_setting darkmysite_dark_mode_switch_margin_bottom_in_mobile" style="<?php echo esc_attr($settings["enable_switch_position_different_in_mobile"] == "1" && strpos($settings["dark_mode_switch_position_in_mobile"], "bottom") !== false ? "" : "display: none;") ?>">
            <div class="darkmysite_input_select_setting_details">
                <h4>Margin from Bottom in Mobile</h4>
                <p>Customize default margin from the bottom of the Floating Switch in Mobile.</p>
            </div>
            <input type="number" value="<?php echo esc_attr($settings["dark_mode_switch_margin_bottom_in_mobile"]) ?>">
        </div>
        <div class="darkmysite_section_block_separator" style="<?php echo esc_attr($settings["enable_switch_position_different_in_mobile"] == "1" && strpos($settings["dark_mode_switch_position_in_mobile"], "left") !== false ? "" : "display: none;") ?>"></div>
        <div class="darkmysite_input_select_setting darkmysite_dark_mode_switch_margin_left_in_mobile" style="<?php echo esc_attr($settings["enable_switch_position_different_in_mobile"] == "1" && strpos($settings["dark_mode_switch_position_in_mobile"], "left") !== false ? "" : "display: none;") ?>">
            <div class="darkmysite_input_select_setting_details">
                <h4>Margin from Left in Mobile</h4>
                <p>Customize default margin from the left of the Floating Switch in Mobile.</p>
            </div>
            <input type="number" value="<?php echo esc_attr($settings["dark_mode_switch_margin_left_in_mobile"]) ?>">
        </div>
        <div class="darkmysite_section_block_separator" style="<?php echo esc_attr($settings["enable_switch_position_different_in_mobile"] == "1" && strpos($settings["dark_mode_switch_position_in_mobile"], "right") !== false ? "" : "display: none;") ?>"></div>
        <div class="darkmysite_input_select_setting darkmysite_dark_mode_switch_margin_right_in_mobile" style="<?php echo esc_attr($settings["enable_switch_position_different_in_mobile"] == "1" && strpos($settings["dark_mode_switch_position_in_mobile"], "right") !== false ? "" : "display: none;") ?>">
            <div class="darkmysite_input_select_setting_details">
                <h4>Margin from Right in Mobile</h4>
                <p>Customize default margin from the right of the Floating Switch in Mobile.</p>
            </div>
            <input type="number" value="<?php echo esc_attr($settings["dark_mode_switch_margin_right_in_mobile"]) ?>">
        </div>
        <div class="darkmysite_section_block_separator"></div>
        <div class="darkmysite_input_select_setting darkmysite_enable_absolute_position">
            <div class="darkmysite_input_select_setting_details">
                <h4>Absolute Switch Position</h4>
                <p>Enable to make the floating switch scroll from it's position with page scrolling.</p>
            </div>
            <select>
                <option <?php echo esc_attr($settings["enable_absolute_position"] == "1" ? "selected" : "") ?> value="1">Enable</option>
                <option <?php echo esc_attr($settings["enable_absolute_position"] == "0" ? "selected" : "") ?> value="0">Disable</option>
            </select>
        </div>
        <div class="darkmysite_section_block_separator"></div>
        <div class="darkmysite_input_select_setting darkmysite_enable_switch_dragging">
            <div class="darkmysite_input_select_setting_details">
                <h4>Draggable Position Change</h4>
                <p>Allow/Disallow users to change the floating switch position by dragging to where they want.</p>
            </div>
            <select>
                <option <?php echo esc_attr($settings["enable_switch_dragging"] == "1" ? "selected" : "") ?> value="1">Enable</option>
                <option <?php echo esc_attr($settings["enable_switch_dragging"] == "0" ? "selected" : "") ?> value="0">Disable</option>
            </select>
        </div>
    </div>












    <div class="darkmysite_section_header">
        <h3>Advanced Customization</h3>
        <p>Customize the Selected Switch Design in Your Way</p>
    </div>
    <div class="darkmysite_settings_with_switch_preview">

        <div class="darkmysite_section_block">

            <?php include DARKMYSITE_PRO_PATH . "backend/templates/views/switch_customize/apple.php"; ?>
            <?php include DARKMYSITE_PRO_PATH . "backend/templates/views/switch_customize/banana.php"; ?>
            <?php include DARKMYSITE_PRO_PATH . "backend/templates/views/switch_customize/cherry.php"; ?>
            <?php include DARKMYSITE_PRO_PATH . "backend/templates/views/switch_customize/durian.php"; ?>
            <?php include DARKMYSITE_PRO_PATH . "backend/templates/views/switch_customize/elderberry.php"; ?>
            <?php include DARKMYSITE_PRO_PATH . "backend/templates/views/switch_customize/fazli.php"; ?>
            <?php include DARKMYSITE_PRO_PATH . "backend/templates/views/switch_customize/guava.php"; ?>
            <?php include DARKMYSITE_PRO_PATH . "backend/templates/views/switch_customize/honeydew.php"; ?>
            <?php include DARKMYSITE_PRO_PATH . "backend/templates/views/switch_customize/incaberry.php"; ?>
            <?php include DARKMYSITE_PRO_PATH . "backend/templates/views/switch_customize/jackfruit.php"; ?>

        </div>

        <div class="darkmysite_switch_preview_container">
            <div class="darkmysite_switch_preview">
                <div class="darkmysite_switch darkmysite_switch_apple <?php echo esc_attr($settings["dark_mode_switch_design"] == "apple" ? "selected" : "") ?>" onclick="darkmysite_switch_preview_triggered(this)">
                    <span class="darkmysite_switch_icon"></span>
                </div>
                <div class="darkmysite_switch darkmysite_switch_banana <?php echo esc_attr($settings["dark_mode_switch_design"] == "banana" ? "selected" : "") ?>" onclick="darkmysite_switch_preview_triggered(this)">
                    <span class="darkmysite_switch_icon"></span>
                </div>
                <div class="darkmysite_switch darkmysite_switch_cherry <?php echo esc_attr($settings["dark_mode_switch_design"] == "cherry" ? "selected" : "") ?>" onclick="darkmysite_switch_preview_triggered(this)">
                    <span class="darkmysite_switch_icon"></span>
                </div>
                <div class="darkmysite_switch darkmysite_switch_durian <?php echo esc_attr($settings["dark_mode_switch_design"] == "durian" ? "selected" : "") ?>" onclick="darkmysite_switch_preview_triggered(this)">
                    <span class="darkmysite_switch_icon"></span>
                </div>
                <div class="darkmysite_switch darkmysite_switch_elderberry <?php echo esc_attr($settings["dark_mode_switch_design"] == "elderberry" ? "selected" : "") ?>" onclick="darkmysite_switch_preview_triggered(this)">
                    <div class="darkmysite_switch_plate">
                        <span class="darkmysite_switch_icon"></span>
                    </div>
                </div>
                <div class="darkmysite_switch darkmysite_switch_fazli <?php echo esc_attr($settings["dark_mode_switch_design"] == "fazli" ? "selected" : "") ?>" onclick="darkmysite_switch_preview_triggered(this)">
                    <div class="darkmysite_switch_plate">
                        <span class="darkmysite_switch_icon"></span>
                    </div>
                </div>
                <div class="darkmysite_switch darkmysite_switch_guava <?php echo esc_attr($settings["dark_mode_switch_design"] == "guava" ? "selected" : "") ?>" onclick="darkmysite_switch_preview_triggered(this)">
                    <span class="darkmysite_switch_icon"></span>
                </div>
                <div class="darkmysite_switch darkmysite_switch_honeydew <?php echo esc_attr($settings["dark_mode_switch_design"] == "honeydew" ? "selected" : "") ?>" onclick="darkmysite_switch_preview_triggered(this)">
                    <div class="darkmysite_switch_plate">
                        <span class="darkmysite_switch_icon"></span>
                    </div>
                </div>
                <div class="darkmysite_switch darkmysite_switch_incaberry <?php echo esc_attr($settings["dark_mode_switch_design"] == "incaberry" ? "selected" : "") ?>" onclick="darkmysite_switch_preview_triggered(this)">
                    <div class="darkmysite_switch_plate">
                        <span class="darkmysite_switch_icon"></span>
                    </div>
                </div>
                <div class="darkmysite_switch darkmysite_switch_jackfruit <?php echo esc_attr($settings["dark_mode_switch_design"] == "jackfruit" ? "selected" : "") ?>" onclick="darkmysite_switch_preview_triggered(this)">
                    <div class="darkmysite_switch_plate">
                        <span class="darkmysite_switch_icon"></span>
                    </div>
                    <div class="darkmysite_switch_secondary_plate">
                        <span class="darkmysite_switch_icon"></span>
                    </div>
                </div>
            </div>
            <button class="darkmysite_copy_customized_shortcode" onclick="darkmysite_cody_customized_shortcode()">Copy Customized Shortcode</button>
        </div>
    </div>





    <div class="darkmysite_section_header">
        <h3>Switch Extras</h3>
        <p>Extra features to customize the dark mde floating switch</p>
    </div>
    <div class="darkmysite_section_block">
        <div class="darkmysite_input_select_setting darkmysite_enable_floating_switch_tooltip">
            <div class="darkmysite_input_select_setting_details">
                <h4>Enable Tooltip on Floating Switch</h4>
                <p>Want to show a hint on dark mode floating switch as tooltip?</p>
            </div>
            <select onchange="darkmysite_enable_disable_floating_switch_tooltip(this)">
                <option <?php echo esc_attr($settings["enable_floating_switch_tooltip"] == "1" ? "selected" : "") ?> value="1">Yes</option>
                <option <?php echo esc_attr($settings["enable_floating_switch_tooltip"] == "0" ? "selected" : "") ?> value="0">No</option>
            </select>
        </div>
        <div class="darkmysite_section_block_separator" style="<?php echo esc_attr($settings["enable_floating_switch_tooltip"] == "1" ? "" : "display: none;") ?>"></div>
        <div class="darkmysite_input_select_setting darkmysite_floating_switch_tooltip_position" style="<?php echo esc_attr($settings["enable_floating_switch_tooltip"] == "1" ? "" : "display: none;") ?>">
            <div class="darkmysite_input_select_setting_details">
                <h4>Tooltip Position</h4>
                <p>Choose the position where the tooltip should be displaed relative to the floating switch.</p>
            </div>
            <select>
                <option <?php echo esc_attr($settings["floating_switch_tooltip_position"] == "top" ? "selected" : "") ?> value="top">Top</option>
                <option <?php echo esc_attr($settings["floating_switch_tooltip_position"] == "bottom" ? "selected" : "") ?> value="bottom">Bottom</option>
                <option <?php echo esc_attr($settings["floating_switch_tooltip_position"] == "left" ? "selected" : "") ?> value="left">Left</option>
                <option <?php echo esc_attr($settings["floating_switch_tooltip_position"] == "right" ? "selected" : "") ?> value="right">Right</option>
            </select>
        </div>
        <div class="darkmysite_section_block_separator" style="<?php echo esc_attr($settings["enable_floating_switch_tooltip"] == "1" ? "" : "display: none;") ?>"></div>
        <div class="darkmysite_input_select_setting darkmysite_floating_switch_tooltip_text" style="<?php echo esc_attr($settings["enable_floating_switch_tooltip"] == "1" ? "" : "display: none;") ?>">
            <div class="darkmysite_input_select_setting_details">
                <h4>Tooltip Text</h4>
                <p>Customize text to be displayed on the tooltip.</p>
            </div>
            <input type="text" value="<?php echo esc_attr($settings["floating_switch_tooltip_text"]) ?>">
        </div>
        <div class="darkmysite_section_block_separator" style="<?php echo esc_attr($settings["enable_floating_switch_tooltip"] == "1" ? "" : "display: none;") ?>"></div>
        <div class="darkmysite_input_select_setting darkmysite_floating_switch_tooltip_bg_color" style="<?php echo esc_attr($settings["enable_floating_switch_tooltip"] == "1" ? "" : "display: none;") ?>">
            <div class="darkmysite_input_select_setting_details">
                <h4>Tooltip Background Color</h4>
                <p>Customize the background color of the floating switch tooltip.</p>
            </div>
            <input type="color" value="<?php echo esc_attr($settings["floating_switch_tooltip_bg_color"]) ?>">
        </div>
        <div class="darkmysite_section_block_separator" style="<?php echo esc_attr($settings["enable_floating_switch_tooltip"] == "1" ? "" : "display: none;") ?>"></div>
        <div class="darkmysite_input_select_setting darkmysite_floating_switch_tooltip_text_color" style="<?php echo esc_attr($settings["enable_floating_switch_tooltip"] == "1" ? "" : "display: none;") ?>">
            <div class="darkmysite_input_select_setting_details">
                <h4>Tooltip Text Color</h4>
                <p>Customize default margin from the left of the Floating Switch in Mobile.</p>
            </div>
            <input type="color" value="<?php echo esc_attr($settings["floating_switch_tooltip_text_color"]) ?>">
        </div>
        <div class="darkmysite_section_block_separator"></div>
        <div class="darkmysite_input_select_setting darkmysite_alternative_dark_mode_switch">
            <div class="darkmysite_input_select_setting_details">
                <h4>Alternative Dark Mode Switch</h4>
                <p>Enter comma-separated CSS class or ID selectors to treat them as dark mode switch.</p>
            </div>
            <input type="text" placeholder="Example: .cz-1, #abc" value="<?php echo esc_attr($settings["alternative_dark_mode_switch"]) ?>">
        </div>
    </div>




</div>