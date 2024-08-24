<?php

namespace FluentBoardsPro\App\Services;

class ProHelper
{
    public static function getFrontEndSlug()
    {
        if (defined('FLUENT_BOARDS_SLUG') && FLUENT_BOARDS_SLUG) {
            return FLUENT_BOARDS_SLUG;
        }

        static $slug = null;

        if ($slug !== null) {
            return $slug;
        }

        $settings = self::getModuleSettings();

        $renderType = empty($settings['frontend']['render_type']) ? 'standalone' : $settings['frontend']['render_type'];

        if ($renderType != 'standalone') {
            $slug = '';
            return $slug;
        }

        $slug = $settings['frontend']['slug'];

        return $slug;
    }

    public static function getFrontAppUrl()
    {
        if (defined('FLUENT_BOARDS_SLUG') && FLUENT_BOARDS_SLUG) {
            return site_url(FLUENT_BOARDS_SLUG) . '#/';
        }

        $settings = self::getModuleSettings();

        if ($settings['frontend']['enabled'] !== 'yes') {
            return '';
        }

        // check if by page
        $renderType = empty($settings['frontend']['render_type']) ? 'standalone' : $settings['frontend']['render_type'];

        if ($renderType === 'shortcode') {
            if (empty($settings['frontend']['page_id'])) {
                return '';
            }

            return get_the_permalink($settings['frontend']['page_id']) . '#/';
        }

        if (empty($settings['frontend']['slug'])) {
            return '';
        }

        return site_url($settings['frontend']['slug']) . '#/';
    }

    public static function getModuleSettings()
    {

        static $option = null;

        if ($option !== null) {
            return $option;
        }

        $option = get_option('fluent_boards_modules');

        if (!$option || !is_array($option)) {
            $option = [
                'timeTracking' => [
                    'enabled'         => 'no',
                    'all_boards'      => 'yes',
                    'selected_boards' => []
                ],
                'frontend'     => [
                    'enabled'     => 'no',
                    'slug'        => 'projects',
                    'render_type' => 'standalone',
                    'page_id'     => ''
                ]
            ];

            update_option('fluent_boards_modules', $option, 'yes');
        }

        return $option;
    }

    public static function formatMinutes($minutes)
    {
        $hours = floor($minutes / 60);
        $minutes = $minutes % 60;

        return sprintf('%02d:%02d', $hours, $minutes);
    }

    public static function getValidatedDateRange($dateRange)
    {
        if (!$dateRange || !is_array($dateRange)) {
            $dateRange = [];
        } else {
            $dateRange = array_filter($dateRange);
        }

        if (!$dateRange || count($dateRange) !== 2) {
            $start = current_time('timestamp');

            $dateRange = [
                date('Y-m-d H:i:s', strtotime('-1 week', $start)),
                date('Y-m-d 23:59:59', $start)
            ];
        } else {
            $dateRange = [
                date('Y-m-d 00:00:00', strtotime($dateRange[0])),
                date('Y-m-d 23:59:59', strtotime($dateRange[1]))
            ];

            // check if start - end is greater than 31 days
            if (strtotime($dateRange[1]) - strtotime($dateRange[0]) > 31 * 24 * 60 * 60) {
                $dateRange[1] = date('Y-m-d 23:59:59', strtotime('+31 days', strtotime($dateRange[0])));
            }
        }

        return $dateRange;
    }

    public static function isPluginInstalled($plugin)
    {
        return file_exists(WP_PLUGIN_DIR . '/' . $plugin);
    }


}
