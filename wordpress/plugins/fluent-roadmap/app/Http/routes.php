<?php

/**
 * @var $router \FluentBoards\Framework\Http\Router
 */

$router->namespace('FluentRoadmap\App\Http\Controllers')->group(function ($router) {
    require_once __DIR__ . '/api.php';
});


# This is how we will implement the logic for the roadmap

// Roadmap
//$router->post('new-idea', 'FluentRoadmap\App\Http\Controllers\RoadmapController@storeIdea');
//$router->get('roadmap-ideas/{board_id}', 'FluentRoadmap\App\Http\Controllers\RoadmapController@getAllIdeas')->int('board_id');
//$router->post('vote-roadmap-idea', 'FluentRoadmap\App\Http\Controllers\RoadmapController@voteRoadmapIdea');
//$router->post('comment-roadmap-idea', 'FluentRoadmap\App\Http\Controllers\RoadmapController@commentRoadmapIdea');
//
//
//// $router->post('roadmap-comment/{board_id}/{task_id}', 'FluentRoadmap\App\Http\Controllers\RoadmapController@roadmapComment');
//$router->get('roadmap-comments/{task_id}', 'FluentRoadmap\App\Http\Controllers\RoadmapController@getRoadmapComment')->int('task_id');
//$router->put('roadmap/comment-update/{comment_id}', 'FluentRoadmap\App\Http\Controllers\RoadmapController@commentUpdate')->int('comment_id');
//$router->get('roadmap/comment/replies/{comment_id}', 'FluentRoadmap\App\Http\Controllers\RoadmapController@getReplies')->int('comment_id');
//$router->get('roadmap/comments/{task_id}', 'FluentRoadmap\App\Http\Controllers\RoadmapController@getAllComments')->int('task_id');
//$router->post('roadmap/{task_id}/replies', 'FluentRoadmap\App\Http\Controllers\RoadmapController@storeReplies')->int('task_id');
//$router->post('roadmap/reaction-vote', 'FluentRoadmap\App\Http\Controllers\RoadmapController@setReactionVote');
//$router->get('roadmap/{comment_id}/reaction-count', 'FluentRoadmap\App\Http\Controllers\RoadmapController@getCommentReaction')->int('comment_id');
//$router->delete('roadmap/comments/{comment_id}', 'FluentRoadmap\App\Http\Controllers\RoadmapController@commentDelete')->int('comment_id');
//
//$router->get('roadmap-board/{board_id}', 'FluentRoadmap\App\Http\Controllers\RoadmapController@getRoadmapStages')->int('board_id');
//$router->get('roadmap-idea/{task_id}', 'FluentRoadmap\App\Http\Controllers\RoadmapController@getIdea')->int('task_id');
