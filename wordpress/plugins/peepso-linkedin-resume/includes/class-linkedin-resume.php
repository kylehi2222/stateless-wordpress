<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class LinkedIn_Resume
{
    public static function init()
    {
        add_filter('peepso_profile_navigation_tabs', [self::class, 'add_resume_tab'], 10, 1);
        add_action('peepso_profile_navigation', [self::class, 'load_resume_tab_content'], 10, 1);
    }

    public static function add_resume_tab($tabs)
    {
        $tabs['resume'] = [
            'title' => __('Resume', 'peepso-linkedin-resume'),
            'icon'  => 'fa fa-briefcase',
            'url'   => 'resume'
        ];
        return $tabs;
    }

    public static function load_resume_tab_content($tab)
    {
        if ($tab === 'resume') {
            echo '<h2>Resume Content Here</h2>';
        }
    }
}
?>
