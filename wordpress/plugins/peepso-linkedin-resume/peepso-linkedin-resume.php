<?php
/**
 * Plugin Name: PeepSo LinkedIn Resume
 * Description: Adds a LinkedIn-style resume tab to PeepSo profiles for Professional users.
 * Author: Your Name
 * Author URI: https://yourwebsite.com
 * Version: 1.0.0
 * Copyright: (c) 2024 Your Name
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: peepsolinkedinresume
 * Domain Path: /languages
 */

class PeepSoLinkedInResume
{
    private static $_instance = NULL;
    const PLUGIN_NAME = 'LinkedIn Resume';
    const PLUGIN_VERSION = '1.0.0';
    const PLUGIN_RELEASE = ''; // ALPHA1, BETA10, RC1, '' for STABLE
    const PEEPSO_MIN_VERSION = '6.4.4.0'; // Minimum PeepSo version required
    const PEEPSO_MAX_VERSION = '6.4.6.0'; // Maximum PeepSo version tested

    private static function ready() {
        return class_exists('PeepSo') && 
               version_compare(PeepSo::PLUGIN_VERSION, self::PEEPSO_MIN_VERSION, '>=') &&
               version_compare(PeepSo::PLUGIN_VERSION, self::PEEPSO_MAX_VERSION, '<=');
    }

    private function __construct()
    {
        if (is_admin()) {
            add_action('admin_init', array(&$this, 'peepso_check'));
        }

        add_filter('peepso_all_plugins', function($plugins) {
            $plugins[plugin_basename(__FILE__)] = get_class($this);
            return $plugins;
        });

        if (self::ready()) {
            add_action('peepso_init', array(&$this, 'init'));
        }
    }

    public static function get_instance()
    {
        if (NULL === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function init()
    {
        PeepSo::add_autoload_directory(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR);
        PeepSoTemplate::add_template_directory(plugin_dir_path(__FILE__) . 'templates');

        // Add the tab to the profile navigation for all viewers if the profile user has the "Professional" role
        add_filter('peepso_navigation_profile', function($links) {
            $view_user_id = PeepSoUrlSegments::get_view_id(PeepSoProfileShortcode::get_instance()->get_view_user_id());
            $user = get_userdata($view_user_id);
            if (array_intersect(['professional', 'vip'], $user->roles)) {
                $links['about-me'] = array(
                    'href' => 'about-me',
                    'label' => __('About', 'peepsolinkedinresume'),
                    'icon' => 'gcis gci-suitcase',
                );
            }
            return $links;
        });

        // Display the content of the "About Me" tab if the profile user has the "Professional" role
        add_action('peepso_profile_segment_about-me', function () {
            $view_user_id = PeepSoUrlSegments::get_view_id(PeepSoProfileShortcode::get_instance()->get_view_user_id());
            $user = get_userdata($view_user_id);
            if (array_intersect(['professional', 'vip'], $user->roles)) {
                $template_path = PeepSoTemplate::locate_template('profile/profile-resume');
                if ($template_path) {
                    include($template_path);
                } else {
                    echo '<div class="resume-content"><h2>Template file not found at path: ' . $template_path . '</h2></div>';
                }
            }
        });
    }

    public function peepso_check()
    {
        if (!class_exists('PeepSo') || !self::ready()) {
            add_action('admin_notices', function() {
                ?>
                <div class="error">
                    <p><?php _e('PeepSo LinkedIn Resume requires PeepSo to be installed and active.', 'peepsolinkedinresume'); ?></p>
                </div>
                <?php
            });

            unset($_GET['activate']);
            deactivate_plugins(plugin_basename(__FILE__));
            return FALSE;
        }

        return TRUE;
    }
}

PeepSoLinkedInResume::get_instance();

?>
