<?php

/**
 * All registered action's handlers should be in app\Hooks\Handlers,
 * addAction is similar to add_action and addCustomAction is just a
 * wrapper over add_action which will add a prefix to the hook name
 * using the plugin slug to make it unique in all wordpress plugins,
 * ex: $app->addCustomAction('foo', ['FooHandler', 'handleFoo']) is
 * equivalent to add_action('slug-foo', ['FooHandler', 'handleFoo']).
 */

/**
 * @var $app FluentCrm\Framework\Foundation\Application
 */
(new \FluentRoadmap\App\Hooks\Handlers\AdminMenuHandler)->register();
(new \FluentRoadmap\App\Hooks\Handlers\PublicMenuHandler)->register();


/**
 * @var \FluentBoards\Framework\Foundation\Application $app
 */


//$app->addAction( 'fluent_pipeline/task_prop_changed', '\FluentRoadmap\App\Hooks\Handlers\ActivityHandler@logActivity', 10, 3 );
$app->addAction('fluent_boards/task_moved_to_new_stage', '\FluentRoadmap\App\Hooks\Handlers\IdeaHandler@ideaMoved', 10, 3);
$app->addAction('fluent_roadmap/send_email_idea_submitter', '\FluentRoadmap\App\Hooks\Handlers\EmailHandler@mailToIdeaSubmitter', 10, 2);
$app->addAction('fluent_roadmap/send_email_idea_commenters', '\FluentRoadmap\App\Hooks\Handlers\EmailHandler@mailToIdeaCommenters', 10, 2);