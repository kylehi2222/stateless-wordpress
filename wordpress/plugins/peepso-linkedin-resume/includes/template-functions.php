<?php

function show_resume_content($user, $profile) {
    $resume_data = get_resume_data($user->ID);
    $education_data = get_education_data($user->ID);
    $skills_data = get_skills_data($user->ID);
    $privacy_settings = get_privacy_settings($user->ID);
    ?>
    <div class="resume-content">
        <h2><?php _e('Work Experience', 'peepso-linkedin-resume'); ?></h2>
        <?php foreach ($resume_data as $entry): ?>
            <div class="resume-entry">
                <h3><?php echo esc_html($entry->position); ?></h3>
                <p><?php echo esc_html($entry->company); ?> (<?php echo esc_html($entry->start_date); ?> - <?php echo esc_html($entry->end_date); ?>)</p>
                <p><?php echo esc_html($entry->description); ?></p>
            </div>
        <?php endforeach; ?>

        <h2><?php _e('Education', 'peepso-linkedin-resume'); ?></h2>
        <?php foreach ($education_data as $entry): ?>
            <div class="education-entry">
                <h3><?php echo esc_html($entry->degree); ?></h3>
                <p><?php echo esc_html($entry->institution); ?> (<?php echo esc_html($entry->start_date); ?> - <?php echo esc_html($entry->end_date); ?>)</p>
                <p><?php echo esc_html($entry->description); ?></p>
            </div>
        <?php endforeach; ?>

        <h2><?php _e('Skills', 'peepso-linkedin-resume'); ?></h2>
        <ul>
            <?php foreach ($skills_data as $entry): ?>
                <li><?php echo esc_html($entry->skill); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php
}

function get_resume_data($user_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'peepso_resumes';
    return $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE user_id = %d", $user_id));
}

function get_education_data($user_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'peepso_education';
    return $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE user_id = %d", $user_id));
}

function get_skills_data($user_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'peepso_skills';
    return $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE user_id = %d", $user_id));
}
?>
