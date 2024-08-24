<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class LinkedInResumeInstall
{
    public function plugin_activation()
    {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $table_resumes = $wpdb->prefix . 'peepso_resumes';
        $sql_resumes = "CREATE TABLE $table_resumes (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            position varchar(255) NOT NULL,
            company varchar(255) NOT NULL,
            start_date date NOT NULL,
            end_date date DEFAULT NULL,
            description text DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        $table_education = $wpdb->prefix . 'peepso_education';
        $sql_education = "CREATE TABLE $table_education (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            institution varchar(255) NOT NULL,
            degree varchar(255) NOT NULL,
            start_date date NOT NULL,
            end_date date DEFAULT NULL,
            description text DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        $table_skills = $wpdb->prefix . 'peepso_skills';
        $sql_skills = "CREATE TABLE $table_skills (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            skill varchar(255) NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta([$sql_resumes, $sql_education, $sql_skills]);

        return TRUE;
    }
}

?>
