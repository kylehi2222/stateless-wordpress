<?php

if (!defined('WPINC')) {
    die;
}

function bgc_chart_shortcode($atts) {  
    ob_start();  
    global $post;

    $attributes = shortcode_atts(array(
        'user_id' => get_current_user_id(),
    ), $atts);
    
    $user_id = $attributes['user_id'];

    // Check if the post type is 'chart' or 'celebrities'
    if ($post && ($post->post_type === 'chart' || $post->post_type === 'celebrities')) {  
        $data = get_post_meta($post->ID, '_bgc_data', true);  
    } else {  
        $data = get_user_meta($user_id, '_bgc_data', true);  
    }

		include_once BGC_PLUGIN_DIR . 'templates/chart.php';

		$template = ob_get_contents();

		ob_end_clean();

		return $template;
	}

add_shortcode('bgc_chart', 'bgc_chart_shortcode');

function bgc_property_shortcode($atts, $content = null, $tag) {
    global $post;
    $atts = shortcode_atts(array(
        'user_id' => get_current_user_id(),
        // Assume the default ID is for the current post when not on a user page
        'post_id' => isset($post) ? $post->ID : null,
    ), $atts);

    $property_key = '_' . strtolower($tag);
    $property_value = '';

    // Determine if it's a page or a post/custom post type
    if (is_page()) {
        // For pages, retrieve data from user meta
        $user_id = $atts['user_id'];
        $property_value = get_user_meta($user_id, $property_key, true);
    } elseif (is_single() || is_singular() || is_post_type_archive()) {
        // For posts and custom post types, retrieve data from post meta
        $post_id = $atts['post_id'];
        $property_value = get_post_meta($post_id, $property_key, true);
    }

    return $property_value;
}

// Define the properties for which you want to create shortcodes
$bgc_properties = [
    'bgc_incarnationcross',
    'bgc_profile',
    'bgc_definition',
    'bgc_signature',
    'bgc_notselftheme',
    'bgc_innerauthority',
    'bgc_strategy',
    'bgc_type',
    'bgc_designdateutc',
    'bgc_birthdateutc',
    'bgc_birthdate',
    'bgc_birthdatelocal',
    'bgc_location',
    'bgc_environment',
    'bgc_channels',
    'bgc_sense',
    'bgc_digestion',
    'bgc_year',
    'bgc_month',
    'bgc_day',
    'bgc_timezone',
    'bgc_hour',
    'bgc_minutes',
    'bgc_motivation',
    'bgc_perspective',
    'bgc_designsense',
    'bgc_decisionmakingstrategy',
    'bgc_businesscompetence',
];

// Automatically generate shortcodes for each property
foreach ($bgc_properties as $property) {
    add_shortcode($property, 'bgc_property_shortcode');
}

function bgc_check_type_and_redirect_shortcode($atts) {
    $atts = shortcode_atts(array(
        'user_id' => get_current_user_id(),
        'redirect_url' => 'https://app.humandesign.ai/onboarding/',
    ), $atts);

    $user_id = $atts['user_id'];
    $property_key = '_bgc_type';
    $property_value = get_user_meta($user_id, $property_key, true);

    if (empty($property_value)) {
        return '<script>window.location.href="' . esc_url($atts['redirect_url']) . '";</script>';
    }

    return '';
}
add_shortcode('bgc_check_type_and_redirect', 'bgc_check_type_and_redirect_shortcode');

function bgc_wp_enqueue_scripts() {
    // Enqueue styles and scripts unconditionally
    wp_enqueue_style('bgc-hd', BGC_PLUGIN_URL . 'assets/css/hd.css');
    wp_enqueue_script('bgc-hd', BGC_PLUGIN_URL . 'assets/js/hd.js');

    // Inject custom JavaScript to fix the fill attributes
    wp_add_inline_script('bgc-hd', "
        jQuery(document).ready(function($) {
            $('[data-chart]').each(function(index, element) {
                var data = $(element).data('chart');
                for (var definedCenter in data['DefinedCenters']) {
                    var centerId = data['DefinedCenters'][definedCenter].replace(/\\s+/g, '-').toLowerCase();
                    $('#' + centerId).attr('fill', '#d39556');
                }
            });
        });
    ");
}
add_action('wp_enqueue_scripts', 'bgc_wp_enqueue_scripts');

?>
