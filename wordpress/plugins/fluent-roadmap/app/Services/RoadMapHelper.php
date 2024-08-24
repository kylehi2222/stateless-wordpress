<?php

namespace FluentRoadmap\App\Services;


class RoadMapHelper
{
    public static function getRoadMapSettings()
    {
        $settings = fluent_boards_get_option('roadmap_settings', []);

        $defaults = [
            'new_idea_require_auth'         => 'yes',
            'new_idea_comment_require_auth' => 'yes',
            'new_idea_vote_require_auth'    => 'yes',
            'auth_html' => "<p>Please login to vote, comment and add new ideas.</p> <a href='{login_url}'>Login</a>"
        ];

        return wp_parse_args($settings, $defaults);
    }
}
