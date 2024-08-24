<?php

$result = array();

/* Check if user has admin capabilities */
if(current_user_can('manage_options')){

    $q = isset($_REQUEST['q']) ? sanitize_text_field($_REQUEST['q']) : "";

    $result = array("status" => 'true', "posts" => $this->base_admin->utils->searchWpPosts($q));

}else{
    $result = array("status" => 'false');
}

echo json_encode($result,  JSON_UNESCAPED_UNICODE);