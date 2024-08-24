<?php

namespace FluentRoadmap\App\Database\Migrations;

class IdeaReactionsMigrator
{
    static $tableName = 'frm_idea_reactions';

    public static function migrate()
    {
        global $wpdb;

        $charsetCollate = $wpdb->get_charset_collate();

        $table = $wpdb->prefix . static::$tableName;

        if ($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {
            $sql = "CREATE TABLE $table (
                `id` BIGINT(20) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
                `user_id` BIGINT UNSIGNED NULL,
                `object_type` VARCHAR(50) NOT NULL DEFAULT 'idea',
                `object_id` BIGINT UNSIGNED NULL COMMENT 'object_id will be task_idea id/comment_id',
                `type` VARCHAR(50) NULL DEFAULT 'upvote' COMMENT 'upvote/downvote',
                `author_ip` VARCHAR(50) NULL,
                `created_at` TIMESTAMP NULL,
                `updated_at` TIMESTAMP NULL,
                KEY `object_id` (`object_id`),
                KEY `object_type` (`object_type`),
                KEY `author_ip` (`author_ip`)
            ) $charsetCollate;";
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            dbDelta($sql);
        }
    }
}

