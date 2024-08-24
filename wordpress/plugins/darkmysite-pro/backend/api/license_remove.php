<?php

$result = array();

/* Check if user has admin capabilities */
if(current_user_can('manage_options')){

    $licenseEmail = $this->base_admin->settings->updateLicenseSettings("email");
    $licenseCode = $this->base_admin->settings->updateLicenseSettings("license_code");

    if($this->base_admin->settings->removeLicense($licenseEmail, $licenseCode)){
        $this->base_admin->settings->updateLicenseSettings("email", "");
        $this->base_admin->settings->updateLicenseSettings("license_code", "");
        $this->base_admin->settings->updateLicenseSettings("last_checked_time", 0);
        $result = array("status" => "true");
    }else {
        $result = array("status" => 'false');
    }

}else{
    $result = array("status" => 'false');
}

echo json_encode($result,  JSON_UNESCAPED_UNICODE);