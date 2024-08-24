<?php

/**
 * All registered filter's handlers should be in app\Hooks\Handlers,
 * addFilter is similar to add_filter and addCustomFlter is just a
 * wrapper over add_filter which will add a prefix to the hook name
 * using the plugin slug to make it unique in all wordpress plugins,
 * ex: $app->addCustomFilter('foo', ['FooHandler', 'handleFoo']) is
 * equivalent to add_filter('slug-foo', ['FooHandler', 'handleFoo']).
 */

/**
 * @var $app FluentBoards\Framework\Foundation\Application
 */

//$app->addFilter('fluent_pipeline/ajax_options_task_assignees', '\FluentRoadmap\App\Hooks\Handlers\TaskHandler@searchAssignees', 10, 3);

add_filter('fluent_boards/board_find', function ($board) {

    $frontPageUrl = \FluentRoadmap\App\Services\Helper::getRoadmapPageUrl($board);

    $board->front_url = $frontPageUrl;

    return $board;
}, 10, 1);