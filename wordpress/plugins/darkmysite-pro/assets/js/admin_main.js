/* =========== GLOBAL OPERATIONS ========== */
function darkmysite_admin_init(host){
    'use strict';
    darkmysite_init_select2();
    darkmysite_switch_preview_init();
}

function darkmysite_hide_all(){
    'use strict';
    jQuery("#darkmysite_control").hide();
    jQuery("#darkmysite_admin").hide();
    jQuery("#darkmysite_switch").hide();
    jQuery("#darkmysite_preset").hide();
    jQuery("#darkmysite_media").hide();
    jQuery("#darkmysite_video").hide();
    jQuery("#darkmysite_advanced").hide();
    jQuery("#darkmysite_license").hide();
}



function darkmysite_init_select2(){
    'use strict';
    var darkmysite_allowed_pages = jQuery('.darkmysite_allowed_pages select')
    var darkmysite_disallowed_pages = jQuery('.darkmysite_disallowed_pages select')
    var darkmysite_allowed_posts = jQuery('.darkmysite_allowed_posts select')
    var darkmysite_disallowed_posts = jQuery('.darkmysite_disallowed_posts select')

    if (darkmysite_allowed_pages.data('select2')) { darkmysite_allowed_pages.select2('destroy');}
    if (darkmysite_disallowed_pages.data('select2')) { darkmysite_disallowed_pages.select2('destroy');}
    if (darkmysite_allowed_posts.data('select2')) { darkmysite_allowed_posts.select2('destroy');}
    if (darkmysite_disallowed_posts.data('select2')) { darkmysite_disallowed_posts.select2('destroy');}

    darkmysite_allowed_pages.select2();
    darkmysite_disallowed_pages.select2();
    darkmysite_allowed_posts.select2({
        closeOnSelect: true,
        ajax: {
            url: ajaxurl,
            type: "GET",
            data: function (params) {
                return {
                    action: 'darkmysite_search_wp_posts',
                    q: params.term
                };
            },
            delay: 250, dataType: 'json',
            processResults: function (response) {
                return {results: response.posts};
            }
        }
    });
    darkmysite_disallowed_posts.select2({
        closeOnSelect: true,
        ajax: {
            url: ajaxurl,
            type: "GET",
            data: function (params) {
                return {
                    action: 'darkmysite_search_wp_posts',
                    q: params.term
                };
            },
            delay: 250, dataType: 'json',
            processResults: function (response) {
                return {results: response.posts};
            }
        }
    });
}


function darkmysite_sidebar_menu_click(view, menu_slug){
    'use strict';
    jQuery(".darkmysite_sidebar .darkmysite_menu").removeClass("active");
    jQuery(view).addClass("active");
    darkmysite_hide_all();
    switch (menu_slug){
        case "control":
            jQuery("#darkmysite_control").show();
            break;
        case "admin":
            jQuery("#darkmysite_admin").show();
            break;
        case "switch":
            jQuery("#darkmysite_switch").show();
            break;
        case "preset":
            jQuery("#darkmysite_preset").show();
            break;
        case "image":
            jQuery("#darkmysite_media").show();
            break;
        case "video":
            jQuery("#darkmysite_video").show();
            break;
        case "advanced":
            jQuery("#darkmysite_advanced").show();
            break;
        case "license":
            jQuery("#darkmysite_license").show();
            break;
    }
}

function darkmysite_switch_design_click(view, switch_id){
    'use strict';
    jQuery(".darkmysite_switch_items .darkmysite_switch_item").removeClass("active");
    jQuery(view).addClass("active");
    jQuery(view).parent().attr("data-switch_id", switch_id);

    jQuery(".darkmysite_switch_customize_apple").hide()
    jQuery(".darkmysite_switch_customize_banana").hide()
    jQuery(".darkmysite_switch_customize_cherry").hide()
    jQuery(".darkmysite_switch_customize_durian").hide()
    jQuery(".darkmysite_switch_customize_elderberry").hide()
    jQuery(".darkmysite_switch_customize_fazli").hide()
    jQuery(".darkmysite_switch_customize_guava").hide()
    jQuery(".darkmysite_switch_customize_honeydew").hide()
    jQuery(".darkmysite_switch_customize_incaberry").hide()
    jQuery(".darkmysite_switch_customize_jackfruit").hide()

    if(switch_id === "apple"){
        jQuery(".darkmysite_switch_customize_apple").show().find("input").each(function(i, input) {
            jQuery(input).val(jQuery(input).attr("data-default"))
        });
    }else if(switch_id === "banana"){
        jQuery(".darkmysite_switch_customize_banana").show().find("input").each(function(i, input) {
            jQuery(input).val(jQuery(input).attr("data-default"))
        });
    }else if(switch_id === "cherry"){
        jQuery(".darkmysite_switch_customize_cherry").show().find("input").each(function(i, input) {
            jQuery(input).val(jQuery(input).attr("data-default"))
        });
    }else if(switch_id === "durian"){
        jQuery(".darkmysite_switch_customize_durian").show().find("input").each(function(i, input) {
            jQuery(input).val(jQuery(input).attr("data-default"))
        });
    }else if(switch_id === "elderberry"){
        jQuery(".darkmysite_switch_customize_elderberry").show().find("input").each(function(i, input) {
            jQuery(input).val(jQuery(input).attr("data-default"))
        });
    }else if(switch_id === "fazli"){
        jQuery(".darkmysite_switch_customize_fazli").show().find("input").each(function(i, input) {
            jQuery(input).val(jQuery(input).attr("data-default"))
        });
    }else if(switch_id === "guava"){
        jQuery(".darkmysite_switch_customize_guava").show().find("input").each(function(i, input) {
            jQuery(input).val(jQuery(input).attr("data-default"))
        });
    }else if(switch_id === "honeydew"){
        jQuery(".darkmysite_switch_customize_honeydew").show().find("input").each(function(i, input) {
            jQuery(input).val(jQuery(input).attr("data-default"))
        });
    }else if(switch_id === "incaberry"){
        jQuery(".darkmysite_switch_customize_incaberry").show().find("input").each(function(i, input) {
            jQuery(input).val(jQuery(input).attr("data-default"))
        });
    }else if(switch_id === "jackfruit"){
        jQuery(".darkmysite_switch_customize_jackfruit").show().find("input").each(function(i, input) {
            jQuery(input).val(jQuery(input).attr("data-default"))
        });
    }
    darkmysite_switch_preview_init();
}

function darkmysite_switch_preview_init() {
    'use strict';
    var switch_id = jQuery(".darkmysite_dark_mode_switch_design").attr("data-switch_id")
    jQuery(".darkmysite_switch_preview").find(".darkmysite_switch").removeClass("selected");
    jQuery(".darkmysite_switch_preview").find(".darkmysite_switch_"+switch_id).addClass("selected");

    jQuery(".darkmysite_switch_customize_"+switch_id).find("input").each(function(i, input) {
        darkmysite_switch_preview_update_design(input);
        jQuery(input).unbind( "change" ).on('change', function() {
            darkmysite_switch_preview_update_design(input);
        });
    });
}

function darkmysite_switch_preview_update_design(input) {
    'use strict';
    var classes = jQuery(input).parent().attr("class").split(' ');
    var design_class_name = classes[classes.length - 1]
    if(jQuery(input).attr("type") === "color"){
        jQuery(".darkmysite_switch_preview").find(".darkmysite_switch.selected").css("--"+design_class_name, jQuery(input).val());
    }else if(jQuery(input).attr("type") === "number"){
        jQuery(".darkmysite_switch_preview").find(".darkmysite_switch.selected").css("--"+design_class_name, jQuery(input).val()+"px");
    }
}

function darkmysite_switch_preview_triggered(view) {
    'use strict';
    if(jQuery(view).parent().hasClass("darkmysite_dark_mode_enabled")){
        jQuery(view).parent().removeClass("darkmysite_dark_mode_enabled")
    }else{
        jQuery(view).parent().addClass("darkmysite_dark_mode_enabled")
    }
}

function darkmysite_cody_customized_shortcode() {
    'use strict';
    var all_switches = ["apple","banana","cherry","durian","elderberry","fazli","guava","honeydew","incaberry","jackfruit"]
    var switch_id = jQuery(".darkmysite_dark_mode_switch_design").attr("data-switch_id")
    var shortcode = "[darkmysite switch=\""+(all_switches.indexOf(switch_id)+1)+"\""
    jQuery(".darkmysite_switch_customize_"+switch_id).find("input").each(function(i, input) {
        var classes = jQuery(input).parent().attr("class").split(' ');
        var design_class_name = classes[classes.length - 1].replace("darkmysite_switch_"+switch_id+"_", "")
        if(jQuery(input).attr("type") === "color"){
            shortcode += " "+design_class_name+"=\""+jQuery(input).val()+"\""
        }else if(jQuery(input).attr("type") === "number"){
            shortcode += " "+design_class_name+"=\""+jQuery(input).val()+"px\""
        }
    });
    shortcode += "]"
    var temp = jQuery("<input>");
    jQuery("body").append(temp);
    temp.val(shortcode).select();
    document.execCommand("copy");
    temp.remove();
    alert("Shortcode Copied to Clipboard")
}

function darkmysite_color_preset_click(view, preset_id){
    'use strict';
    jQuery(".darkmysite_preset_items .darkmysite_preset_item .darkmysite_preset_item_active").remove();
    jQuery(view).append("<span class=\"darkmysite_preset_item_active\"></span>")
    jQuery(view).parent().attr("data-preset_id", preset_id);

    if(preset_id === "black"){
        jQuery(".darkmysite_dark_mode_bg input").val("#0F0F0F")
        jQuery(".darkmysite_dark_mode_secondary_bg input").val("#171717")
        jQuery(".darkmysite_dark_mode_text_color input").val("#BEBEBE")
        jQuery(".darkmysite_dark_mode_link_color input").val("#FFFFFF")
        jQuery(".darkmysite_dark_mode_link_hover_color input").val("#CCCCCC")
        jQuery(".darkmysite_dark_mode_input_bg input").val("#2D2D2D")
        jQuery(".darkmysite_dark_mode_input_text_color input").val("#BEBEBE")
        jQuery(".darkmysite_dark_mode_input_placeholder_color input").val("#989898")
        jQuery(".darkmysite_dark_mode_border_color input").val("#4A4A4A")
        jQuery(".darkmysite_dark_mode_btn_bg input").val("#2D2D2D")
        jQuery(".darkmysite_dark_mode_btn_text_color input").val("#BEBEBE")
    }else if(preset_id === "blue"){
        jQuery(".darkmysite_dark_mode_bg input").val("#142434")
        jQuery(".darkmysite_dark_mode_secondary_bg input").val("#182D43")
        jQuery(".darkmysite_dark_mode_text_color input").val("#B0CBE7")
        jQuery(".darkmysite_dark_mode_link_color input").val("#337EC9")
        jQuery(".darkmysite_dark_mode_link_hover_color input").val("#0075EB")
        jQuery(".darkmysite_dark_mode_input_bg input").val("#1B4B7B")
        jQuery(".darkmysite_dark_mode_input_text_color input").val("#B0CBE7")
        jQuery(".darkmysite_dark_mode_input_placeholder_color input").val("#2c73b7")
        jQuery(".darkmysite_dark_mode_border_color input").val("#4B6F93")
        jQuery(".darkmysite_dark_mode_btn_bg input").val("#1B4B7B")
        jQuery(".darkmysite_dark_mode_btn_text_color input").val("#B0CBE7")
    }else if(preset_id === "green"){
        jQuery(".darkmysite_dark_mode_bg input").val("#0D1D07")
        jQuery(".darkmysite_dark_mode_secondary_bg input").val("#112609")
        jQuery(".darkmysite_dark_mode_text_color input").val("#ABC2A2")
        jQuery(".darkmysite_dark_mode_link_color input").val("#509F34")
        jQuery(".darkmysite_dark_mode_link_hover_color input").val("#45CF14")
        jQuery(".darkmysite_dark_mode_input_bg input").val("#162d0d")
        jQuery(".darkmysite_dark_mode_input_text_color input").val("#ABC2A2")
        jQuery(".darkmysite_dark_mode_input_placeholder_color input").val("#3d7a28")
        jQuery(".darkmysite_dark_mode_border_color input").val("#2f5a1e")
        jQuery(".darkmysite_dark_mode_btn_bg input").val("#162d0d")
        jQuery(".darkmysite_dark_mode_btn_text_color input").val("#ABC2A2")
    }else if(preset_id === "orange"){
        jQuery(".darkmysite_dark_mode_bg input").val("#171004")
        jQuery(".darkmysite_dark_mode_secondary_bg input").val("#211706")
        jQuery(".darkmysite_dark_mode_text_color input").val("#D3BFA1")
        jQuery(".darkmysite_dark_mode_link_color input").val("#E09525")
        jQuery(".darkmysite_dark_mode_link_hover_color input").val("#FFB23E")
        jQuery(".darkmysite_dark_mode_input_bg input").val("#372911")
        jQuery(".darkmysite_dark_mode_input_text_color input").val("#D3BFA1")
        jQuery(".darkmysite_dark_mode_input_placeholder_color input").val("#b37b21")
        jQuery(".darkmysite_dark_mode_border_color input").val("#6D4911")
        jQuery(".darkmysite_dark_mode_btn_bg input").val("#372911")
        jQuery(".darkmysite_dark_mode_btn_text_color input").val("#D3BFA1")
    }else if(preset_id === "pink"){
        jQuery(".darkmysite_dark_mode_bg input").val("#19081B")
        jQuery(".darkmysite_dark_mode_secondary_bg input").val("#210A24")
        jQuery(".darkmysite_dark_mode_text_color input").val("#C09DC2")
        jQuery(".darkmysite_dark_mode_link_color input").val("#C832D4")
        jQuery(".darkmysite_dark_mode_link_hover_color input").val("#DE58E9")
        jQuery(".darkmysite_dark_mode_input_bg input").val("#440649")
        jQuery(".darkmysite_dark_mode_input_text_color input").val("#C09DC2")
        jQuery(".darkmysite_dark_mode_input_placeholder_color input").val("#8e6d92")
        jQuery(".darkmysite_dark_mode_border_color input").val("#630F68")
        jQuery(".darkmysite_dark_mode_btn_bg input").val("#440649")
        jQuery(".darkmysite_dark_mode_btn_text_color input").val("#C09DC2")
    }
}


function darkmysite_enable_scrollbar_change_change(view){
    'use strict';
    var enable_scrollbar_dark = jQuery(view).val();
    if(enable_scrollbar_dark === "1"){
        jQuery(".darkmysite_dark_mode_scrollbar_track_bg").show().prev().show()
        jQuery(".darkmysite_dark_mode_scrollbar_thumb_bg").show().prev().show()
    }else if(enable_scrollbar_dark === "0"){
        jQuery(".darkmysite_dark_mode_scrollbar_track_bg").hide().prev().hide()
        jQuery(".darkmysite_dark_mode_scrollbar_thumb_bg").hide().prev().hide()
    }
}


function darkmysite_switch_position_change(view){
    'use strict';
    var position = jQuery(view).val();
    if(position === "top_right"){
        jQuery(".darkmysite_dark_mode_switch_margin_top").show().prev().show()
        jQuery(".darkmysite_dark_mode_switch_margin_right").show().prev().show()
        jQuery(".darkmysite_dark_mode_switch_margin_bottom").hide().prev().hide()
        jQuery(".darkmysite_dark_mode_switch_margin_left").hide().prev().hide()
    }else if(position === "top_left"){
        jQuery(".darkmysite_dark_mode_switch_margin_top").show().prev().show()
        jQuery(".darkmysite_dark_mode_switch_margin_left").show().prev().show()
        jQuery(".darkmysite_dark_mode_switch_margin_bottom").hide().prev().hide()
        jQuery(".darkmysite_dark_mode_switch_margin_right").hide().prev().hide()
    }else if(position === "bottom_right"){
        jQuery(".darkmysite_dark_mode_switch_margin_bottom").show().prev().show()
        jQuery(".darkmysite_dark_mode_switch_margin_right").show().prev().show()
        jQuery(".darkmysite_dark_mode_switch_margin_top").hide().prev().hide()
        jQuery(".darkmysite_dark_mode_switch_margin_left").hide().prev().hide()
    }else if(position === "bottom_left"){
        jQuery(".darkmysite_dark_mode_switch_margin_bottom").show().prev().show()
        jQuery(".darkmysite_dark_mode_switch_margin_left").show().prev().show()
        jQuery(".darkmysite_dark_mode_switch_margin_top").hide().prev().hide()
        jQuery(".darkmysite_dark_mode_switch_margin_right").hide().prev().hide()
    }
    jQuery(".darkmysite_dark_mode_switch_margin_top input").val("40")
    jQuery(".darkmysite_dark_mode_switch_margin_right input").val("40")
    jQuery(".darkmysite_dark_mode_switch_margin_bottom input").val("40")
    jQuery(".darkmysite_dark_mode_switch_margin_left input").val("40")
}


function darkmysite_enable_disable_mobile_switch_position(view){
    'use strict';
    var choice = jQuery(view).val();
    if(choice === "1"){
        jQuery(".darkmysite_dark_mode_switch_position_in_mobile").show().prev().show()
        jQuery(".darkmysite_dark_mode_switch_margin_top_in_mobile").show().prev().show()
        jQuery(".darkmysite_dark_mode_switch_margin_left_in_mobile").show().prev().show()
        jQuery(".darkmysite_dark_mode_switch_margin_bottom_in_mobile").show().prev().show()
        jQuery(".darkmysite_dark_mode_switch_margin_right_in_mobile").show().prev().show()
        darkmysite_mobile_switch_position_change(jQuery(".darkmysite_dark_mode_switch_position_in_mobile select"))
    }else if(choice === "0"){
        jQuery(".darkmysite_dark_mode_switch_position_in_mobile").hide().prev().hide()
        jQuery(".darkmysite_dark_mode_switch_margin_top_in_mobile").hide().prev().hide()
        jQuery(".darkmysite_dark_mode_switch_margin_left_in_mobile").hide().prev().hide()
        jQuery(".darkmysite_dark_mode_switch_margin_bottom_in_mobile").hide().prev().hide()
        jQuery(".darkmysite_dark_mode_switch_margin_right_in_mobile").hide().prev().hide()
    }
}

function darkmysite_mobile_switch_position_change(view){
    'use strict';
    var position = jQuery(view).val();
    if(position === "top_right"){
        jQuery(".darkmysite_dark_mode_switch_margin_top_in_mobile").show().prev().show()
        jQuery(".darkmysite_dark_mode_switch_margin_right_in_mobile").show().prev().show()
        jQuery(".darkmysite_dark_mode_switch_margin_bottom_in_mobile").hide().prev().hide()
        jQuery(".darkmysite_dark_mode_switch_margin_left_in_mobile").hide().prev().hide()
    }else if(position === "top_left"){
        jQuery(".darkmysite_dark_mode_switch_margin_top_in_mobile").show().prev().show()
        jQuery(".darkmysite_dark_mode_switch_margin_left_in_mobile").show().prev().show()
        jQuery(".darkmysite_dark_mode_switch_margin_bottom_in_mobile").hide().prev().hide()
        jQuery(".darkmysite_dark_mode_switch_margin_right_in_mobile").hide().prev().hide()
    }else if(position === "bottom_right"){
        jQuery(".darkmysite_dark_mode_switch_margin_bottom_in_mobile").show().prev().show()
        jQuery(".darkmysite_dark_mode_switch_margin_right_in_mobile").show().prev().show()
        jQuery(".darkmysite_dark_mode_switch_margin_top_in_mobile").hide().prev().hide()
        jQuery(".darkmysite_dark_mode_switch_margin_left_in_mobile").hide().prev().hide()
    }else if(position === "bottom_left"){
        jQuery(".darkmysite_dark_mode_switch_margin_bottom_in_mobile").show().prev().show()
        jQuery(".darkmysite_dark_mode_switch_margin_left_in_mobile").show().prev().show()
        jQuery(".darkmysite_dark_mode_switch_margin_top_in_mobile").hide().prev().hide()
        jQuery(".darkmysite_dark_mode_switch_margin_right_in_mobile").hide().prev().hide()
    }
    jQuery(".darkmysite_dark_mode_switch_margin_top_in_mobile input").val("40")
    jQuery(".darkmysite_dark_mode_switch_margin_right_in_mobile input").val("40")
    jQuery(".darkmysite_dark_mode_switch_margin_bottom_in_mobile input").val("40")
    jQuery(".darkmysite_dark_mode_switch_margin_left_in_mobile input").val("40")
}

function darkmysite_enable_disable_floating_switch_tooltip(view){
    'use strict';
    var choice = jQuery(view).val();
    if(choice === "1"){
        jQuery(".darkmysite_floating_switch_tooltip_position").show().prev().show()
        jQuery(".darkmysite_floating_switch_tooltip_text").show().prev().show()
        jQuery(".darkmysite_floating_switch_tooltip_bg_color").show().prev().show()
        jQuery(".darkmysite_floating_switch_tooltip_text_color").show().prev().show()
    }else if(choice === "0"){
        jQuery(".darkmysite_floating_switch_tooltip_position").hide().prev().hide()
        jQuery(".darkmysite_floating_switch_tooltip_text").hide().prev().hide()
        jQuery(".darkmysite_floating_switch_tooltip_bg_color").hide().prev().hide()
        jQuery(".darkmysite_floating_switch_tooltip_text_color").hide().prev().hide()
    }
}


function darkmysite_checkbox_input_select_change(view) {
    'use strict';
    if(jQuery(view).parent().find("input[type='checkbox']:checked").length > 0){
        jQuery(view).parent().parent().find("select").show()
        jQuery(view).parent().parent().find("input").show()

        if(jQuery(view).parent().parent().hasClass("darkmysite_checkbox_input_select_setting_part_1")){
            if(jQuery(view).parent().parent().parent().find(".darkmysite_checkbox_input_select_setting_part_2").length > 0){
                jQuery(view).parent().parent().parent().find(".darkmysite_checkbox_input_select_setting_part_2").show()
            }
        }

    }else{
        jQuery(view).parent().parent().find("select").hide()
        jQuery(view).parent().parent().find("input").hide()

        if(jQuery(view).parent().parent().hasClass("darkmysite_checkbox_input_select_setting_part_1")){
            if(jQuery(view).parent().parent().parent().find(".darkmysite_checkbox_input_select_setting_part_2").length > 0){
                jQuery(view).parent().parent().parent().find(".darkmysite_checkbox_input_select_setting_part_2").hide()
            }
        }
    }
}

function darkmysite_switch_in_menu_checkbox_change(view) {
    'use strict';
    if(jQuery(view).parent().find("input[type='checkbox']:checked").length > 0){
        jQuery(view).parent().parent().find("select").show()
        jQuery(view).parent().parent().find("textarea").show()
        jQuery(view).parent().parent().find("span.darkmysite_menu_shortcode_helper").show()
    }else{
        jQuery(view).parent().parent().find("select").hide()
        jQuery(view).parent().parent().find("textarea").hide()
        jQuery(view).parent().parent().find("span.darkmysite_menu_shortcode_helper").hide()
    }
}

function darkmysite_image_inversion_generate_json() {
    'use strict';
    var urls = []
    var textarea_val = jQuery(".darkmysite_invert_images_allowed_urls textarea").val();
    var urls_arr = textarea_val.split('\n')
    jQuery.each(urls_arr, function(key, value) {
        if(value.toString().trim().length > 0){
            urls.push(value.toString().trim())
        }
    });
    return urls;
}



function darkmysite_image_replacement_add_item(){
    'use strict';
    var html = "<div class=\"darkmysite_image_replace_setting_item\">\n" +
        "                <div class=\"darkmysite_image_replace_setting_item_part_0\">\n" +
        "                    <input type=\"text\" value=\"\" placeholder=\"Image URL\">\n" +
        "                </div>\n" +
        "                <div class=\"darkmysite_image_replace_setting_item_part_1\">\n" +
        "                    <button class=\"choose_image darkmysite_ignore\" onclick=\"darkmysite_image_replacement_choose_image(this)\"></button>\n" +
        "                </div>\n" +
        "                <div class=\"darkmysite_image_replace_setting_item_part_2\">\n" +
        "                    <input type=\"text\" value=\"\" placeholder=\"Image URL\">\n" +
        "                </div>\n" +
        "                <div class=\"darkmysite_image_replace_setting_item_part_3\">\n" +
        "                    <button class=\"choose_image darkmysite_ignore\" onclick=\"darkmysite_image_replacement_choose_image(this)\"></button>\n" +
        "                </div>\n" +
        "                <div class=\"darkmysite_image_replace_setting_item_part_4\">\n" +
        "                    <button class=\"remove_item darkmysite_ignore\" onclick=\"darkmysite_image_replacement_remove_item(this)\"></button>\n" +
        "                </div>\n" +
        "            </div>"
    jQuery(".darkmysite_image_replace_setting").append(html)
}
function darkmysite_image_replacement_remove_item(view){
    'use strict';
    jQuery(view).parent().parent().remove()
}
function darkmysite_image_replacement_choose_image(view){
    'use strict';
    var image_frame;
    if(image_frame){
        image_frame.open();
    }
    image_frame = wp.media({
        title: 'Select Image',
        multiple : false,
        library : {
            type : 'image',
        }
    });
    image_frame.on('close',function() {
        var selection =  image_frame.state().get('selection');
        jQuery(view).parent().prev().find("input").val(selection.models[0].attributes.url)
    });
    image_frame.open();
}
function darkmysite_image_replacement_generate_json() {
    'use strict';
    var replacements = []
    jQuery(".darkmysite_image_replace_setting .darkmysite_image_replace_setting_item").each(function (i, object) {
        var single_item = {}
        single_item["normal_image"] = jQuery(object).find(".darkmysite_image_replace_setting_item_part_0 input").val().trim()
        single_item["dark_image"] = jQuery(object).find(".darkmysite_image_replace_setting_item_part_2 input").val().trim()

        if((single_item["normal_image"]+"").length > 0 && (single_item["dark_image"]+"").length > 0){
            replacements.push(single_item)
        }
    });
    return replacements;
}






function darkmysite_video_replacement_add_item(){
    'use strict';
    var html = "<div class=\"darkmysite_video_replace_setting_item\">\n" +
        "                <div class=\"darkmysite_video_replace_setting_item_part_0\">\n" +
        "                    <input type=\"text\" value=\"\" placeholder=\"Video URL\">\n" +
        "                </div>\n" +
        "                <div class=\"darkmysite_video_replace_setting_item_part_1\">\n" +
        "                    <button class=\"choose_video darkmysite_ignore\" onclick=\"darkmysite_video_replacement_choose_video(this)\"></button>\n" +
        "                </div>\n" +
        "                <div class=\"darkmysite_video_replace_setting_item_part_2\">\n" +
        "                    <input type=\"text\" value=\"\" placeholder=\"Video URL\">\n" +
        "                </div>\n" +
        "                <div class=\"darkmysite_video_replace_setting_item_part_3\">\n" +
        "                    <button class=\"choose_video darkmysite_ignore\" onclick=\"darkmysite_video_replacement_choose_video(this)\"></button>\n" +
        "                </div>\n" +
        "                <div class=\"darkmysite_video_replace_setting_item_part_4\">\n" +
        "                    <button class=\"remove_item darkmysite_ignore\" onclick=\"darkmysite_video_replacement_remove_item(this)\"></button>\n" +
        "                </div>\n" +
        "            </div>"
    jQuery(".darkmysite_video_replace_setting").append(html)
}
function darkmysite_video_replacement_remove_item(view){
    'use strict';
    jQuery(view).parent().parent().remove()
}
function darkmysite_video_replacement_choose_video(view){
    'use strict';
    var video_frame;
    if(video_frame){
        video_frame.open();
    }
    video_frame = wp.media({
        title: 'Select Video',
        multiple : false,
        library : {
            type : 'video',
        }
    });
    video_frame.on('close',function() {
        var selection =  video_frame.state().get('selection');
        jQuery(view).parent().prev().find("input").val(selection.models[0].attributes.url)
    });
    video_frame.open();
}
function darkmysite_video_replacement_generate_json() {
    'use strict';
    var replacements = []
    jQuery(".darkmysite_video_replace_setting .darkmysite_video_replace_setting_item").each(function (i, object) {
        var single_item = {}
        single_item["normal_video"] = jQuery(object).find(".darkmysite_video_replace_setting_item_part_0 input").val().trim()
        single_item["dark_video"] = jQuery(object).find(".darkmysite_video_replace_setting_item_part_2 input").val().trim()

        if((single_item["normal_video"]+"").length > 0 && (single_item["dark_video"]+"").length > 0){
            replacements.push(single_item)
        }
    });
    return replacements;
}





function darkmysite_copy_shortcode(view){
    'use strict';
    var temp = jQuery("<input>");
    jQuery("body").append(temp);
    temp.val(jQuery(view).text()).select();
    document.execCommand("copy");
    temp.remove();
    alert("Shortcode Copied to Clipboard")
}


function darkmysite_license_remove(view) {
    'use strict';
    jQuery(view).text("Removing...");
    var post_data = {
        'action': 'darkmysite_license_remove'
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
                jQuery(view).text("Remove License");
            }
        }
    })
}


function darkmysite_save() {
    'use strict';
    jQuery('.darkmysite_body_header_save_btn').text('SAVING..').prop('disabled', true);

    var post_data = {
        'action': 'darkmysite_update_settings',

        /* Control */
        'show_rating_block': jQuery(".darkmysite_rating_msg_block").length > 0 ? "1" : "0",
        'show_support_msg_block': jQuery(".darkmysite_support_msg_block").length > 0 ? "1" : "0",
        'enable_dark_mode_switch': jQuery(".darkmysite_enable_dark_mode_switch input[type='checkbox']:checked").length > 0 ? "1" : "0",
        'enable_default_dark_mode': jQuery(".darkmysite_enable_default_dark_mode input[type='checkbox']:checked").length > 0 ? "1" : "0",
        'enable_os_aware': jQuery(".darkmysite_enable_os_aware input[type='checkbox']:checked").length > 0 ? "1" : "0",
        'enable_keyboard_shortcut': jQuery(".darkmysite_enable_keyboard_shortcut input[type='checkbox']:checked").length > 0 ? "1" : "0",
        'enable_time_based_dark': jQuery(".darkmysite_enable_time_based_dark input[type='checkbox']:checked").length > 0 ? "1" : "0",
        'time_based_dark_start': jQuery(".darkmysite_enable_time_based_dark").find("input[type='time']").eq(0).val(),
        'time_based_dark_stop': jQuery(".darkmysite_enable_time_based_dark").find("input[type='time']").eq(1).val(),
        'hide_on_desktop': jQuery(".darkmysite_hide_on_desktop input[type='checkbox']:checked").length > 0 ? "1" : "0",
        'hide_on_mobile': jQuery(".darkmysite_hide_on_mobile input[type='checkbox']:checked").length > 0 ? "1" : "0",
        'hide_on_mobile_by': jQuery(".darkmysite_hide_on_mobile select").val(),
        'enable_switch_in_menu': jQuery(".darkmysite_enable_switch_in_menu input[type='checkbox']:checked").length > 0 ? "1" : "0",
        'switch_in_menu_location': jQuery(".darkmysite_enable_switch_in_menu select").val(),
        'switch_in_menu_shortcode': jQuery(".darkmysite_enable_switch_in_menu textarea").val(),

        /* Admin */
        'enable_admin_dark_mode': jQuery(".darkmysite_enable_admin_dark_mode input[type='checkbox']:checked").length > 0 ? "1" : "0",
        'display_in_admin_settings_menu': jQuery(".darkmysite_display_in_admin_settings_menu input[type='checkbox']:checked").length > 0 ? "1" : "0",
        'disallowed_admin_pages': jQuery(".darkmysite_disallowed_admin_pages textarea").val(),

        /* Switch */
        'dark_mode_switch_design': jQuery(".darkmysite_dark_mode_switch_design").attr("data-switch_id"),
        'dark_mode_switch_position': jQuery(".darkmysite_dark_mode_switch_position select").val(),
        'dark_mode_switch_margin_top': jQuery(".darkmysite_dark_mode_switch_margin_top input").val(),
        'dark_mode_switch_margin_bottom': jQuery(".darkmysite_dark_mode_switch_margin_bottom input").val(),
        'dark_mode_switch_margin_left': jQuery(".darkmysite_dark_mode_switch_margin_left input").val(),
        'dark_mode_switch_margin_right': jQuery(".darkmysite_dark_mode_switch_margin_right input").val(),
        'enable_switch_position_different_in_mobile': jQuery(".darkmysite_enable_switch_position_different_in_mobile select").val(),
        'dark_mode_switch_position_in_mobile': jQuery(".darkmysite_dark_mode_switch_position_in_mobile select").val(),
        'dark_mode_switch_margin_top_in_mobile': jQuery(".darkmysite_dark_mode_switch_margin_top_in_mobile input").val(),
        'dark_mode_switch_margin_bottom_in_mobile': jQuery(".darkmysite_dark_mode_switch_margin_bottom_in_mobile input").val(),
        'dark_mode_switch_margin_left_in_mobile': jQuery(".darkmysite_dark_mode_switch_margin_left_in_mobile input").val(),
        'dark_mode_switch_margin_right_in_mobile': jQuery(".darkmysite_dark_mode_switch_margin_right_in_mobile input").val(),
        'enable_absolute_position': jQuery(".darkmysite_enable_absolute_position select").val(),
        'enable_switch_dragging': jQuery(".darkmysite_enable_switch_dragging select").val(),

        /* Switch Extras */
        'enable_floating_switch_tooltip': jQuery(".darkmysite_enable_floating_switch_tooltip select").val(),
        'floating_switch_tooltip_position': jQuery(".darkmysite_floating_switch_tooltip_position select").val(),
        'floating_switch_tooltip_text': jQuery(".darkmysite_floating_switch_tooltip_text input").val(),
        'floating_switch_tooltip_bg_color': jQuery(".darkmysite_floating_switch_tooltip_bg_color input").val(),
        'floating_switch_tooltip_text_color': jQuery(".darkmysite_floating_switch_tooltip_text_color input").val(),
        'alternative_dark_mode_switch': jQuery(".darkmysite_alternative_dark_mode_switch input").val(),

        /* Switch Apple */
        'switch_apple_width_height': jQuery(".darkmysite_switch_apple_width_height input").val(),
        'switch_apple_border_radius': jQuery(".darkmysite_switch_apple_border_radius input").val(),
        'switch_apple_icon_width': jQuery(".darkmysite_switch_apple_icon_width input").val(),
        'switch_apple_light_mode_bg': jQuery(".darkmysite_switch_apple_light_mode_bg input").val(),
        'switch_apple_dark_mode_bg': jQuery(".darkmysite_switch_apple_dark_mode_bg input").val(),
        'switch_apple_light_mode_icon_color': jQuery(".darkmysite_switch_apple_light_mode_icon_color input").val(),
        'switch_apple_dark_mode_icon_color': jQuery(".darkmysite_switch_apple_dark_mode_icon_color input").val(),
        /* Switch Banana */
        'switch_banana_width_height': jQuery(".darkmysite_switch_banana_width_height input").val(),
        'switch_banana_border_radius': jQuery(".darkmysite_switch_banana_border_radius input").val(),
        'switch_banana_icon_width': jQuery(".darkmysite_switch_banana_icon_width input").val(),
        'switch_banana_light_mode_bg': jQuery(".darkmysite_switch_banana_light_mode_bg input").val(),
        'switch_banana_dark_mode_bg': jQuery(".darkmysite_switch_banana_dark_mode_bg input").val(),
        'switch_banana_light_mode_icon_color': jQuery(".darkmysite_switch_banana_light_mode_icon_color input").val(),
        'switch_banana_dark_mode_icon_color': jQuery(".darkmysite_switch_banana_dark_mode_icon_color input").val(),
        /* Switch Cherry */
        'switch_cherry_width_height': jQuery(".darkmysite_switch_cherry_width_height input").val(),
        'switch_cherry_border_radius': jQuery(".darkmysite_switch_cherry_border_radius input").val(),
        'switch_cherry_icon_width': jQuery(".darkmysite_switch_cherry_icon_width input").val(),
        'switch_cherry_light_mode_bg': jQuery(".darkmysite_switch_cherry_light_mode_bg input").val(),
        'switch_cherry_dark_mode_bg': jQuery(".darkmysite_switch_cherry_dark_mode_bg input").val(),
        'switch_cherry_light_mode_icon_color': jQuery(".darkmysite_switch_cherry_light_mode_icon_color input").val(),
        'switch_cherry_dark_mode_icon_color': jQuery(".darkmysite_switch_cherry_dark_mode_icon_color input").val(),
        /* Switch Durian */
        'switch_durian_width_height': jQuery(".darkmysite_switch_durian_width_height input").val(),
        'switch_durian_border_size': jQuery(".darkmysite_switch_durian_border_size input").val(),
        'switch_durian_border_radius': jQuery(".darkmysite_switch_durian_border_radius input").val(),
        'switch_durian_icon_width': jQuery(".darkmysite_switch_durian_icon_width input").val(),
        'switch_durian_light_mode_bg': jQuery(".darkmysite_switch_durian_light_mode_bg input").val(),
        'switch_durian_dark_mode_bg': jQuery(".darkmysite_switch_durian_dark_mode_bg input").val(),
        'switch_durian_light_mode_icon_and_border_color': jQuery(".darkmysite_switch_durian_light_mode_icon_and_border_color input").val(),
        'switch_durian_dark_mode_icon_and_border_color': jQuery(".darkmysite_switch_durian_dark_mode_icon_and_border_color input").val(),
        /* Switch Elderberry */
        'switch_elderberry_width': jQuery(".darkmysite_switch_elderberry_width input").val(),
        'switch_elderberry_height': jQuery(".darkmysite_switch_elderberry_height input").val(),
        'switch_elderberry_icon_plate_width': jQuery(".darkmysite_switch_elderberry_icon_plate_width input").val(),
        'switch_elderberry_icon_plate_border_size': jQuery(".darkmysite_switch_elderberry_icon_plate_border_size input").val(),
        'switch_elderberry_icon_width': jQuery(".darkmysite_switch_elderberry_icon_width input").val(),
        'switch_elderberry_light_mode_bg': jQuery(".darkmysite_switch_elderberry_light_mode_bg input").val(),
        'switch_elderberry_dark_mode_bg': jQuery(".darkmysite_switch_elderberry_dark_mode_bg input").val(),
        'switch_elderberry_light_mode_icon_plate_bg': jQuery(".darkmysite_switch_elderberry_light_mode_icon_plate_bg input").val(),
        'switch_elderberry_dark_mode_icon_plate_bg': jQuery(".darkmysite_switch_elderberry_dark_mode_icon_plate_bg input").val(),
        'switch_elderberry_light_mode_icon_color': jQuery(".darkmysite_switch_elderberry_light_mode_icon_color input").val(),
        'switch_elderberry_dark_mode_icon_color': jQuery(".darkmysite_switch_elderberry_dark_mode_icon_color input").val(),
        /* Switch Fazli */
        'switch_fazli_width': jQuery(".darkmysite_switch_fazli_width input").val(),
        'switch_fazli_height': jQuery(".darkmysite_switch_fazli_height input").val(),
        'switch_fazli_icon_plate_width': jQuery(".darkmysite_switch_fazli_icon_plate_width input").val(),
        'switch_fazli_icon_plate_border_size': jQuery(".darkmysite_switch_fazli_icon_plate_border_size input").val(),
        'switch_fazli_icon_width': jQuery(".darkmysite_switch_fazli_icon_width input").val(),
        'switch_fazli_light_mode_bg': jQuery(".darkmysite_switch_fazli_light_mode_bg input").val(),
        'switch_fazli_dark_mode_bg': jQuery(".darkmysite_switch_fazli_dark_mode_bg input").val(),
        'switch_fazli_light_mode_icon_plate_bg': jQuery(".darkmysite_switch_fazli_light_mode_icon_plate_bg input").val(),
        'switch_fazli_dark_mode_icon_plate_bg': jQuery(".darkmysite_switch_fazli_dark_mode_icon_plate_bg input").val(),
        'switch_fazli_light_mode_icon_color': jQuery(".darkmysite_switch_fazli_light_mode_icon_color input").val(),
        'switch_fazli_dark_mode_icon_color': jQuery(".darkmysite_switch_fazli_dark_mode_icon_color input").val(),
        /* Switch Guava */
        'switch_guava_width': jQuery(".darkmysite_switch_guava_width input").val(),
        'switch_guava_height': jQuery(".darkmysite_switch_guava_height input").val(),
        'switch_guava_icon_width': jQuery(".darkmysite_switch_guava_icon_width input").val(),
        'switch_guava_icon_margin': jQuery(".darkmysite_switch_guava_icon_margin input").val(),
        'switch_guava_light_mode_bg': jQuery(".darkmysite_switch_guava_light_mode_bg input").val(),
        'switch_guava_dark_mode_bg': jQuery(".darkmysite_switch_guava_dark_mode_bg input").val(),
        'switch_guava_light_mode_icon_color': jQuery(".darkmysite_switch_guava_light_mode_icon_color input").val(),
        'switch_guava_dark_mode_icon_color': jQuery(".darkmysite_switch_guava_dark_mode_icon_color input").val(),
        /* Switch Honeydew */
        'switch_honeydew_width': jQuery(".darkmysite_switch_honeydew_width input").val(),
        'switch_honeydew_height': jQuery(".darkmysite_switch_honeydew_height input").val(),
        'switch_honeydew_icon_plate_width': jQuery(".darkmysite_switch_honeydew_icon_plate_width input").val(),
        'switch_honeydew_icon_plate_margin': jQuery(".darkmysite_switch_honeydew_icon_plate_margin input").val(),
        'switch_honeydew_icon_width': jQuery(".darkmysite_switch_honeydew_icon_width input").val(),
        'switch_honeydew_light_mode_icon_plate_bg': jQuery(".darkmysite_switch_honeydew_light_mode_icon_plate_bg input").val(),
        'switch_honeydew_dark_mode_icon_plate_bg': jQuery(".darkmysite_switch_honeydew_dark_mode_icon_plate_bg input").val(),
        'switch_honeydew_light_mode_icon_color': jQuery(".darkmysite_switch_honeydew_light_mode_icon_color input").val(),
        'switch_honeydew_dark_mode_icon_color': jQuery(".darkmysite_switch_honeydew_dark_mode_icon_color input").val(),
        /* Switch Incaberry */
        'switch_incaberry_width': jQuery(".darkmysite_switch_incaberry_width input").val(),
        'switch_incaberry_height': jQuery(".darkmysite_switch_incaberry_height input").val(),
        'switch_incaberry_icon_plate_width': jQuery(".darkmysite_switch_incaberry_icon_plate_width input").val(),
        'switch_incaberry_icon_plate_margin': jQuery(".darkmysite_switch_incaberry_icon_plate_margin input").val(),
        'switch_incaberry_icon_width': jQuery(".darkmysite_switch_incaberry_icon_width input").val(),
        'switch_incaberry_light_mode_icon_plate_bg': jQuery(".darkmysite_switch_incaberry_light_mode_icon_plate_bg input").val(),
        'switch_incaberry_dark_mode_icon_plate_bg': jQuery(".darkmysite_switch_incaberry_dark_mode_icon_plate_bg input").val(),
        'switch_incaberry_light_mode_icon_color': jQuery(".darkmysite_switch_incaberry_light_mode_icon_color input").val(),
        'switch_incaberry_dark_mode_icon_color': jQuery(".darkmysite_switch_incaberry_dark_mode_icon_color input").val(),
        /* Switch Jackfruit */
        'switch_jackfruit_width': jQuery(".darkmysite_switch_jackfruit_width input").val(),
        'switch_jackfruit_height': jQuery(".darkmysite_switch_jackfruit_height input").val(),
        'switch_jackfruit_icon_plate_width': jQuery(".darkmysite_switch_jackfruit_icon_plate_width input").val(),
        'switch_jackfruit_icon_plate_margin': jQuery(".darkmysite_switch_jackfruit_icon_plate_margin input").val(),
        'switch_jackfruit_icon_width': jQuery(".darkmysite_switch_jackfruit_icon_width input").val(),
        'switch_jackfruit_light_mode_bg': jQuery(".darkmysite_switch_jackfruit_light_mode_bg input").val(),
        'switch_jackfruit_dark_mode_bg': jQuery(".darkmysite_switch_jackfruit_dark_mode_bg input").val(),
        'switch_jackfruit_light_mode_icon_plate_bg': jQuery(".darkmysite_switch_jackfruit_light_mode_icon_plate_bg input").val(),
        'switch_jackfruit_dark_mode_icon_plate_bg': jQuery(".darkmysite_switch_jackfruit_dark_mode_icon_plate_bg input").val(),
        'switch_jackfruit_light_mode_light_icon_color': jQuery(".darkmysite_switch_jackfruit_light_mode_light_icon_color input").val(),
        'switch_jackfruit_light_mode_dark_icon_color': jQuery(".darkmysite_switch_jackfruit_light_mode_dark_icon_color input").val(),
        'switch_jackfruit_dark_mode_light_icon_color': jQuery(".darkmysite_switch_jackfruit_dark_mode_light_icon_color input").val(),
        'switch_jackfruit_dark_mode_dark_icon_color': jQuery(".darkmysite_switch_jackfruit_dark_mode_dark_icon_color input").val(),


        /* Preset */
        'dark_mode_color_preset': jQuery(".darkmysite_dark_mode_color_preset").attr("data-preset_id"),
        'dark_mode_bg': jQuery(".darkmysite_dark_mode_bg input").val(),
        'dark_mode_secondary_bg': jQuery(".darkmysite_dark_mode_secondary_bg input").val(),
        'dark_mode_text_color': jQuery(".darkmysite_dark_mode_text_color input").val(),
        'dark_mode_link_color': jQuery(".darkmysite_dark_mode_link_color input").val(),
        'dark_mode_link_hover_color': jQuery(".darkmysite_dark_mode_link_hover_color input").val(),
        'dark_mode_input_bg': jQuery(".darkmysite_dark_mode_input_bg input").val(),
        'dark_mode_input_text_color': jQuery(".darkmysite_dark_mode_input_text_color input").val(),
        'dark_mode_input_placeholder_color': jQuery(".darkmysite_dark_mode_input_placeholder_color input").val(),
        'dark_mode_border_color': jQuery(".darkmysite_dark_mode_border_color input").val(),
        'dark_mode_btn_bg': jQuery(".darkmysite_dark_mode_btn_bg input").val(),
        'dark_mode_btn_text_color': jQuery(".darkmysite_dark_mode_btn_text_color input").val(),
        'enable_scrollbar_dark': jQuery(".darkmysite_enable_scrollbar_dark select").val(),
        'dark_mode_scrollbar_track_bg': jQuery(".darkmysite_dark_mode_scrollbar_track_bg input").val(),
        'dark_mode_scrollbar_thumb_bg': jQuery(".darkmysite_dark_mode_scrollbar_thumb_bg input").val(),

        /* Media */
        'enable_low_image_brightness': jQuery(".darkmysite_enable_low_image_brightness input[type='checkbox']:checked").length > 0 ? "1" : "0",
        'image_brightness_to': jQuery(".darkmysite_enable_low_image_brightness select").val(),
        'disallowed_low_brightness_images': jQuery(".darkmysite_enable_low_image_brightness textarea").val(),
        'enable_image_grayscale': jQuery(".darkmysite_enable_image_grayscale input[type='checkbox']:checked").length > 0 ? "1" : "0",
        'image_grayscale_to': jQuery(".darkmysite_enable_image_grayscale select").val(),
        'disallowed_grayscale_images': jQuery(".darkmysite_enable_image_grayscale textarea").val(),
        'enable_bg_image_darken': jQuery(".darkmysite_enable_bg_image_darken input[type='checkbox']:checked").length > 0 ? "1" : "0",
        'bg_image_darken_to': jQuery(".darkmysite_enable_bg_image_darken select").val(),
        'enable_invert_inline_svg': jQuery(".darkmysite_enable_invert_inline_svg input[type='checkbox']:checked").length > 0 ? "1" : "0",
        'enable_invert_images': jQuery(".darkmysite_enable_invert_images input[type='checkbox']:checked").length > 0 ? "1" : "0",
        'invert_images_allowed_urls': JSON.stringify(darkmysite_image_inversion_generate_json()),
        'image_replacements': JSON.stringify(darkmysite_image_replacement_generate_json()),

        /* Video */
        'enable_low_video_brightness': jQuery(".darkmysite_enable_low_video_brightness input[type='checkbox']:checked").length > 0 ? "1" : "0",
        'video_brightness_to': jQuery(".darkmysite_enable_low_video_brightness select").val(),
        'enable_video_grayscale': jQuery(".darkmysite_enable_video_grayscale input[type='checkbox']:checked").length > 0 ? "1" : "0",
        'video_grayscale_to': jQuery(".darkmysite_enable_video_grayscale select").val(),
        'video_replacements': JSON.stringify(darkmysite_video_replacement_generate_json()),

        /* Restriction */
        'allowed_elements': jQuery(".darkmysite_allowed_elements textarea").val(),
        'allowed_elements_force_to_correct': jQuery(".darkmysite_allowed_elements input[type='checkbox']:checked").length > 0 ? "1" : "0",
        'disallowed_elements': jQuery(".darkmysite_disallowed_elements textarea").val(),
        'disallowed_elements_force_to_correct': jQuery(".darkmysite_disallowed_elements input[type='checkbox']:checked").length > 0 ? "1" : "0",
        'allowed_pages': jQuery(".darkmysite_allowed_pages select").val() != null ? jQuery(".darkmysite_allowed_pages select").val().join(",") : "",
        'disallowed_pages': jQuery(".darkmysite_disallowed_pages select").val() != null ? jQuery(".darkmysite_disallowed_pages select").val().join(",") : "",
        'allowed_posts': jQuery(".darkmysite_allowed_posts select").val() != null ? jQuery(".darkmysite_allowed_posts select").val().join(",") : "",
        'disallowed_posts': jQuery(".darkmysite_disallowed_posts select").val() != null ? jQuery(".darkmysite_disallowed_posts select").val().join(",") : "",
        'custom_css': jQuery(".darkmysite_custom_css textarea").val(),
        'custom_css_apply_on_children': jQuery(".darkmysite_custom_css input[type='checkbox']:checked").length > 0 ? "1" : "0",
        'normal_custom_css': jQuery(".darkmysite_normal_custom_css textarea").val(),

        /* License */
        'enable_support_team_modify_settings': jQuery(".darkmysite_enable_support_team_modify_settings input[type='checkbox']:checked").length > 0 ? "1" : "0",

    };

    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        data: post_data,
        success: function (data) {
            var obj = JSON.parse(data);
            if (obj.status === "true") {
                jQuery('.darkmysite_body_header_save_btn').text('SAVE CHANGES').prop('disabled', false);
            }
        }
    });
}




function darkmysite_show_pro_popup(headline, details) {
    'use strict';
    if(headline === ""){
        headline = "Go Premium";
    }
    if(details === ""){
        details = "This feature is only available in the Pro Version";
    }
    jQuery(".darkmysite_pro_popup_container").css("display", "flex");
    jQuery(".darkmysite_pro_popup_container h3").text(headline);
    jQuery(".darkmysite_pro_popup_container p").text(details);
}
function darkmysite_close_pro_popup() {
    'use strict';
    jQuery(".darkmysite_pro_popup_container").css("display", "none");
}

function darkmysite_close_support_msg_block() {
    'use strict';
    jQuery(".darkmysite_support_msg_block").remove();
}

function darkmysite_close_rating_msg_block() {
    'use strict';
    jQuery(".darkmysite_rating_msg_block").remove();
}