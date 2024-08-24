<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Handle adding/updating work experience
add_action('wp_ajax_save_work_experience', 'save_work_experience');
function save_work_experience() {
    global $wpdb;

    $user_id = get_current_user_id();
    $position = sanitize_text_field($_POST['position']);
    $company = sanitize_text_field($_POST['company']);
    $start_date = sanitize_text_field($_POST['start_date']);
    $end_date = sanitize_text_field($_POST['end_date']);
    $description = sanitize_textarea_field($_POST['description']);

    if ($_POST['id']) {
        // Update existing entry
        $wpdb->update(
            $wpdb->prefix . 'peepso_resumes',
            array(
                'position' => $position,
                'company' => $company,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'description' => $description,
            ),
            array('id' => intval($_POST['id']), 'user_id' => $user_id),
            array('%s', '%s', '%s', '%s', '%s'),
            array('%d', '%d')
        );
    } else {
        // Insert new entry
        $wpdb->insert(
            $wpdb->prefix . 'peepso_resumes',
            array(
                'user_id' => $user_id,
                'position' => $position,
                'company' => $company,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'description' => $description,
            ),
            array('%d', '%s', '%s', '%s', '%s', '%s')
        );
    }

    wp_send_json_success();
}

// Similar functions for education and skills can be added here...
