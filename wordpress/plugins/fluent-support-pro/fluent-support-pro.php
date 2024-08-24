<?php defined('ABSPATH') or die;
/**
 * Plugin Name:  Fluent Support Pro
 * Plugin URI:   https://fluentsupport.com
 * Description:  Customer Support and Ticketing System for WordPress
 * Version:      1.7.90
 * Author:       WPManageNinja LLC
 * Author URI:   https://fluentsupport.com
 * License:      GPL2
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:  fluent-support-pro
 * Domain Path:  /languages
 */

if (defined('FLUENT_SUPPORT_PRO_DIR_FILE')) {
    return;
}

define('FLUENT_SUPPORT_PRO_DIR_FILE', __FILE__);

require_once("fluent-support-pro-boot.php");

add_action('plugins_loaded', function () {
    add_action('init', function () {
        load_plugin_textdomain('fluent-support-pro', false, 'fluent-support-pro/languages/');
    });

    if (!wp_next_scheduled('fluent_support_pro_every_two_hour_tasks')) {
        wp_schedule_event(time(), 'every_two_hour', 'fluent_support_pro_every_two_hour_tasks');
    }
    if (!wp_next_scheduled('fluent_support_pro_quarter_to_hour_tasks')) {
        wp_schedule_event(time(), 'every_quarter_to_hour', 'fluent_support_pro_quarter_to_hour_tasks');
    }

});

add_action('fluent_support_loaded', function ($app) {
    (new \FluentSupportPro\App\Application($app));
    do_action('fluent_support_pro_loaded', $app);
});

register_activation_hook(
    __FILE__, array('FluentSupportPro\Database\DBMigrator', 'run')
);

// Handle Network new Site Activation
add_action('wp_insert_site', function ($new_site) {
    if (is_plugin_active_for_network('fluent-support-pro/fluent-support-pro.php')) {
        switch_to_blog($new_site->blog_id);
        \FluentSupportPro\Database\DBMigrator::run(false);
        restore_current_blog();
    }
});

add_action('cron_schedules', function ($schedules) {
    $schedules['every_two_hour'] = array(
        'interval' => 7200,
        'display'  => __('Every Two Hours', 'fluent-support-pro')
    );
    $schedules['every_quarter_to_hour'] = [
        'interval' => 2700, //45 minutes = 2700 seconds
        'display'  => __('Every Quarter Till Hour', 'fluent-support-pro')
    ];
    return $schedules;
});
