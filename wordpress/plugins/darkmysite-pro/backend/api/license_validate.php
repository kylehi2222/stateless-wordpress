<?php

$result = array();

/* Check if user has admin capabilities */
if(current_user_can('manage_options')){

    if(isset($_REQUEST['email']) && isset($_REQUEST['license_code'])){


        $email = sanitize_text_field($_REQUEST['email']);
        $license_code = sanitize_text_field($_REQUEST['license_code']);

        if($this->base_admin->settings->isLicenseValid($email, $license_code)){
            $this->base_admin->settings->updateLicenseSettings("email", $email);
            $this->base_admin->settings->updateLicenseSettings("license_code", $license_code);
            $this->base_admin->settings->updateLicenseSettings("last_checked_time", time());
            $result = array("status" => "true");
        }else {
            $this->base_admin->settings->updateLicenseSettings("email", "");
            $this->base_admin->settings->updateLicenseSettings("license_code", "");
            $this->base_admin->settings->updateLicenseSettings("last_checked_time", 0);
            $result = array("status" => 'false');
        }

    }else{
        $result = array("status" => 'false');
    }
}else{
    $result = array("status" => 'false');
}

echo json_encode($result,  JSON_UNESCAPED_UNICODE);