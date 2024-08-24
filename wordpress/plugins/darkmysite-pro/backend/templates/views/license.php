<div id="darkmysite_license" style="display: none;">

    <div class="darkmysite_body_header">
        <div class="darkmysite_body_header_details">
            <div class="darkmysite_body_header_details_logo darkmysite_ignore">
                <img src="<?php echo esc_url(DARKMYSITE_PRO_IMG_DIR . "sidebar/sidebar_menu_license.svg") ?>">
            </div>
            <div class="darkmysite_body_header_details_headline">
                <h2>LICENSE</h2>
                <p>DarkMySite License Information</p>
            </div>
        </div>
        <button class="darkmysite_body_header_save_btn darkmysite_ignore" onclick="darkmysite_save()">SAVE CHANGES</button>
    </div>



    <div class="darkmysite_body_header_separator darkmysite_ignore"></div>



    <?php
    $licenseEmail = $this->settings->updateLicenseSettings("email");
    $licenseCode = $this->settings->updateLicenseSettings("license_code");
    if($licenseCode != Null){
        $licenseCodeArr = explode("-",$licenseCode);
        $licenseCodeArr[1] = "XXXX";
        $licenseCodeArr[2] = "XXXX";
        $licenseCodeArr[3] = "XXXX";
        $licenseCodeHidden = implode("-",$licenseCodeArr);
    }
    ?>

    <div class="darkmysite_section_header">
        <h3>License Information</h3>
        <p>Details of activated DarkMySite License</p>
    </div>
    <div class="darkmysite_section_block">
        <div class="darkmysite_license_setting">
            <h4>License Activation Email</h4>
            <p><?php echo esc_attr($licenseEmail); ?></p>
        </div>
        <div class="darkmysite_section_block_separator"></div>
        <div class="darkmysite_license_setting">
            <h4>License Code</h4>
            <p><?php echo esc_attr($licenseCodeHidden); ?></p>
        </div>
        <div class="darkmysite_section_block_separator" style="height: 0; background: transparent;"></div>
        <div class="darkmysite_license_setting">
            <button onclick="darkmysite_license_remove(this)">Remove License</button>
        </div>
    </div>





    <div class="darkmysite_section_header">
        <h3>Support Settings</h3>
        <p>Settings related to premium support from the DarkMySite Team</p>
    </div>
    <div class="darkmysite_section_block">
        <div class="darkmysite_checkbox_setting darkmysite_enable_support_team_modify_settings">
            <label class="darkmysite_checkbox_item darkmysite_ignore"><input type="checkbox" <?php echo esc_attr($settings["enable_support_team_modify_settings"] == "1" ? "checked" : "") ?>><span class="darkmysite_checkbox_checkmark"></span></label>
            <div class="darkmysite_checkbox_setting_details">
                <h4>Allow Settings Modification</h4>
                <p>Check to allow DarkMySite Support Team modify the plugin settings to fix issues remotely and securely.</p>
            </div>
        </div>
    </div>





</div>