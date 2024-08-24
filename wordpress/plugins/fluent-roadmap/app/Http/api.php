<?php

/**
 * @var $router \FluentBoards\Framework\Http\Router
 */

$router->prefix('roadmaps')->withPolicy('PublicPolicy')->group(function ($router) {
    $router->get('{board_id}/stages/{stage_id}/ideas', 'RoadmapController@getStageIdeas')
        ->int('board_id')
        ->alphaNumDash('stage_id');

    $router->post('{board_id}/ideas', 'RoadmapController@createIdea')
        ->int('board_id');

    $router->get('{board_id}/ideas/{task_id}', 'RoadmapController@getIdea')
        ->int('board_id')
        ->alphaNumDash('task_id');

    $router->post('{board_id}/ideas/{idea_slug}/comments', 'RoadmapController@addComment')
        ->int('board_id')
        ->alphaNumDash('idea_slug');

    $router->delete('idea/comments/{comment_id}', 'RoadmapController@deleteComment')
        ->int('comment_id');

    $router->post('vote-idea/{idea_id}', 'RoadmapController@voteIdea')->int('idea_id');
});

$router->prefix('admin/roadmap')->withPolicy(\FluentRoadmap\App\Http\Policies\RoadmapAdminPolicy::class)->group(function ($router) {
    $router->get('settings', [\FluentRoadmap\App\Http\Controllers\RoadmapAdminController::class, 'getSettings']);
    $router->post('settings', [\FluentRoadmap\App\Http\Controllers\RoadmapAdminController::class, 'updateSettings']);
    $router->get('page-settings', [\FluentRoadmap\App\Http\Controllers\RoadmapAdminController::class, 'getPageSettings']);
    $router->post('page-settings', [\FluentRoadmap\App\Http\Controllers\RoadmapAdminController::class, 'updatePageSettings']);
});
