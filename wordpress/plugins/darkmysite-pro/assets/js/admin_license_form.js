function darkmysite_license_activate(host) {
    'use strict';

    jQuery(".darkmysite_license_form_main_area_body_container .darkmysite_license_form_error_msg").hide();
    jQuery(".darkmysite_license_form_main_area_body_container button").text("Checking...");

    var post_data = {
        'action': 'darkmysite_license_validate',
        'email': jQuery("#darkmysite_license_form_email_address").val(),
        'license_code': jQuery("#darkmysite_license_form_license_code").val()
    };


    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        data: post_data,
        success: function (data) {
            var obj = JSON.parse(data);
            if(obj.status === "true"){
                location.reload();
            }else{
                jQuery(".darkmysite_license_form_main_area_body_container .darkmysite_license_form_error_msg").show();
                jQuery(".darkmysite_license_form_main_area_body_container button").text("Activate");
            }
        }
    })
}