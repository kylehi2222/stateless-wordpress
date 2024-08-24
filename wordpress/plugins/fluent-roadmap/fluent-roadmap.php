<?php
/*
Plugin Name: Fluent Roadmap
Description: FluentRoadmap is an add-on for FluentBoards that provides a clear view of user requests, project progress, and priorities.
Plugin URI:   https://fluenboards.com
Version:      1.21
Author:       Fluent Boards
Author URI:   https://fluenboards.com
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  fluent-roadmap
Domain Path:  /languages
*/

if (defined('FLUENT_ROADMAP_PLUGIN_PATH')) {
    return;
}

if (defined('FLUENT_ROADMAP')) {
    return;
}

define('FLUENT_ROADMAP', 'fluent-roadmap');
define('FLUENT_ROADMAP_DIR_FILE', __FILE__);
define('FLUENT_ROADMAP_PLUGIN_URL', plugin_dir_url(__FILE__));
define('FLUENT_ROADMAP_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('FLUENT_ROADMAP_PLUGIN_VERSION', '1.21');

require_once(FLUENT_ROADMAP_PLUGIN_PATH.'fluent_roadmap_boot.php');

add_action('fluent_boards_loaded', function ($app) {
    (new \FluentRoadmap\App\Application($app));
    do_action('fluent_roadmap_loaded', $app);
});

register_activation_hook(__FILE__,
    array('\FluentRoadmap\App\Database\DBMigrator', 'run'));

// Handle Network new Site Activation
add_action('wp_insert_site', function ($new_site) {
    if (is_plugin_active_for_network('fluent-roadmap/fluent-roadmap.php')) {
        switch_to_blog($new_site->blog_id);
        \FluentRoadmap\App\Database\DBMigrator::run(false);
        restore_current_blog();
    }
});

add_action('init', function () {
    load_plugin_textdomain('fluent-roadmap', false,
        dirname(plugin_basename(__FILE__)).'/languages');
});

function restrict_media_library_access($tabs)
{
    // Check if the user is not logged in
    if ( ! is_user_logged_in()) {
        // Remove the media library tab from the tabs array
        unset($tabs['library']);
    }

    return $tabs;
}

add_filter('media_upload_tabs', 'restrict_media_library_access');

add_action('plugins_loaded', function () {

    $apiUrl = 'https://fluentboards.com/wp-admin/admin-ajax.php?action=fluent_roadmap_update&time=' . time();
    new \FluentRoadmap\App\Services\PluginManager\Updater($apiUrl, FLUENT_ROADMAP_DIR_FILE, array(
        'version'   => FLUENT_ROADMAP_PLUGIN_VERSION,
        'license'   => '12345',
        'item_name' => 'Fluent Roadmap',
        'item_id'   => 'fluent-roadmap',
        'author'    => 'wpmanageninja'
    ),
        array(
            'license_status' => 'valid',
            'admin_page_url' => admin_url('admin.php?page=fluent-boards#/'),
            'purchase_url'   => 'https://fluentboards.com',
            'plugin_title'   => 'Fluent Roadmap'
        )
    );

    add_filter('plugin_row_meta', function ($links, $file) {

        if ('fluent-roadmap/fluent-roadmap.php' !== $file) {
            return $links;
        }

        $checkUpdateUrl = esc_url(admin_url('plugins.php?fluent-roadmap-check-update=' . time()));

        $row_meta = array(
            'check_update' => '<a  style="color: #583fad;font-weight: 600;" href="' . $checkUpdateUrl . '" aria-label="' . esc_attr__('Check Update', 'fluent-roadmap') . '">' . esc_html__('Check Update', 'fluent-roadmap') . '</a>',
        );

        return array_merge($links, $row_meta);

    }, 10, 2);
});
