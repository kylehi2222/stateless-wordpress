<?php

namespace FluentBoards\Database\Migrations;

class TaskMetaMigrator
{
    /**
     * Task Meta Table.
     *
     * @param  bool $isForced
     * @return void
     */
    public static function migrate($isForced = true)
    {
        global $wpdb;

        $charsetCollate = $wpdb->get_charset_collate();
        $table = $wpdb->prefix . 'fbs_task_metas';

        if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table)) != $table || $isForced) {
            $sql = "CREATE TABLE $table (
                `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
                `task_id` INT UNSIGNED NOT NULL,
                `key` VARCHAR(100) NOT NULL,
                `value` LONGTEXT NULL,
                `created_at` TIMESTAMP NULL,
                `updated_at` TIMESTAMP NULL,
                KEY `task_id` (`task_id`)
            ) $charsetCollate;";
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            dbDelta($sql);
        }
    }
}
