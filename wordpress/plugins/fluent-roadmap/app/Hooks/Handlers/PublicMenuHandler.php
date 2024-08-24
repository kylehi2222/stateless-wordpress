<?php

namespace FluentRoadmap\App\Hooks\Handlers;

use FluentBoards\App\App;
use FluentBoards\App\Models\BoardMeta;
use FluentBoards\App\Models\Meta;
use FluentRoadmap\App\Models\Board;
use FluentRoadmap\App\Services\Helper;
use FluentRoadmap\App\Services\RoadMapHelper;

class PublicMenuHandler
{
    public function register()
    {
        add_shortcode('fluent-roadmap', [$this, 'loadShortCode']);

        if (defined('FLUENT_ROADMAP_SLUG') && defined('FLUENT_ROADMAP_PAGE_ID')) {
            add_action('init', function () {
                $page_id = FLUENT_ROADMAP_PAGE_ID; //Your serve page id
                add_rewrite_rule('^roadmap.*', 'index.php?page_id=' . $page_id, 'top');
            });
            add_filter('request', [$this, 'registerRoadmapRewriteRule']);
        }
    }

    public function registerRoadmapRewriteRule($request)
    {
        $uri = $_SERVER['REQUEST_URI'];

        if (!preg_match('/^\/' . FLUENT_ROADMAP_SLUG . '(\/.*)?$/', $uri)) {
            return $request;
        }

        $page = get_page_by_path(FLUENT_ROADMAP_SLUG);

        if (!$page) {
            return $request;
        }

        $request['pagename'] = FLUENT_ROADMAP_SLUG;


        $pattern = '/\/roadmap\/idea\/(.*?)\//';
        preg_match($pattern, $uri, $matches);
        if (isset($matches[1])) {
            $ideaSlug = $matches[1];
            $idea = \FluentRoadmap\App\Models\Idea::where('slug', $ideaSlug)->first();
            if ($idea) {
                add_filter('pre_get_document_title', function ($title) use ($idea) {
                    return $idea->title . ' - ' . get_bloginfo('name');
                }, 10000);

                // Set the meta description
                add_filter('seopress_titles_desc', function ($title) use ($idea) {
                    $description = $idea->description;
                    // strip html and then truncate for meta description
                    $description = wp_strip_all_tags($description);
                    // replace new lines with spaces
                    $description = str_replace("\n", ' ', $description);
                    // truncate to 160 chars with php
                    return substr($description, 0, 160);
                });

            }
        }

        return $request;
    }

    public function loadShortCode($atts)
    {
        $atts = shortcode_atts(array(
            'id' => '', // Default ID value if not provided
        ), $atts, 'fluent-roadmap');

        if (empty($atts['id'])) {
            return 'Please provide a valid roadmap id';
        }

        $roadmapId = $atts['id'];

        $board = Board::find($roadmapId);

        $pageId = $board->meta['roadmap_page_id'] ?? null;

        if (!$board) {
            return 'Please provide a valid roadmap id';
        }

        wp_enqueue_script('fluent_roadmap_script', FLUENT_ROADMAP_PLUGIN_URL . 'assets/frontend/roadmap.js', array('jquery'), FLUENT_ROADMAP_PLUGIN_VERSION, true);

        $app = App::getInstance();
        $currentUser = get_user_by('ID', get_current_user_id());

//        if (function_exists('wp_enqueue_media')) {
//            wp_enqueue_editor();
//            wp_enqueue_media();
//        }

        $globalVars = [
            'slug'             => 'fluent-roadmap',
            'nonce'            => wp_create_nonce('fluent-roadmap'),
            'rest'             => $this->getRestInfo($app),
            'trans'            => [
                'Please enter a keyword' => __('Please enter a keyword', 'fluent-roadmap'),
            ],
            'me'               => [
                'id'        => $currentUser ? $currentUser->ID : 0,
                'full_name' => $currentUser ? trim($currentUser->first_name . ' ' . $currentUser->last_name) : '',
                'email'     => $currentUser ? $currentUser->user_email : '',
                'avatar'    => $currentUser ? fluent_boards_user_avatar($currentUser->user_email) : FLUENT_ROADMAP_PLUGIN_URL . 'assets/images/avatar.png',
            ],
            'server_time'      => current_time('mysql'),
            'is_auth_user'     => is_user_logged_in(),
            'auth_settings'    => RoadMapHelper::getRoadMapSettings(),
            'registration_url' => wp_registration_url(),
        ];

        if (!$currentUser) {
            $authHtml = $globalVars['auth_settings']['auth_html'];

              $currentUrl = get_the_permalink();

              $loginUrl = wp_login_url($currentUrl);

             $authHtml = str_replace('{current_url}', $currentUrl, $authHtml);
             $authHtml = str_replace('{login_url}', $loginUrl, $authHtml);

            $globalVars['auth_html'] = $authHtml;
        }

        unset($globalVars['auth_settings']['auth_html']);

        wp_localize_script('fluent_roadmap_script', 'fluentAddonVars', $globalVars);

        $publicStages = $board->getPublicStates();

        $publicStages[] = [
            'id'    => 'all-ideas',
            'slug'  => 'all-ideas',
            'label' => 'All',
        ];

        // $all_plugins = get_plugins();
        wp_localize_script('fluent_roadmap_script', 'fluentBoardVars_' . $board->id, [
            'board'    => [
                'id'          => $board->id,
                'title'       => $board->title,
                'description' => $board->description,
                'stages'      => $publicStages,
            ],
            'app_path' => defined('FLUENT_ROADMAP_SLUG') ? '/' . FLUENT_ROADMAP_SLUG . '/' : ''
        ]);

        $content = "<div data-board_id='" . $board->id . "' class='fluent_board_app_wrapper' id='frontend-fluent-roadmap-" . $board->id . "'></div>";
        return $content;
    }

    protected function getRestInfo($app)
    {
        $ns = $app->config->get('app.rest_namespace');
        $ver = $app->config->get('app.rest_version');

        return [
            'base_url'  => esc_url_raw(rest_url()),
            'url'       => rest_url($ns . '/' . $ver),
            'nonce'     => wp_create_nonce('wp_rest'),
            'namespace' => $ns,
            'version'   => $ver
        ];
    }

    public function custom_media_tabs($tabs)
    {
        // Remove the media library tab
        unset($tabs['library']);
        return $tabs;
    }
}
