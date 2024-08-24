<?php

function get_privacy_settings($user_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'peepso_privacy';
    return $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE user_id = %d", $user_id));
}

function set_privacy_settings($user_id, $section, $level) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'peepso_privacy';
    $wpdb->replace($table_name, array(
        'user_id' => $user_id,
        'section' => $section,
        'privacy_level' => $level
    ));
}
?>
