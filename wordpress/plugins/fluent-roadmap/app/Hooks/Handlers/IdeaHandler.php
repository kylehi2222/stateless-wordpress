<?php

namespace FluentRoadmap\App\Hooks\Handlers;

use FluentBoards\App\Models\Stage;
use FluentRoadmap\App\Models\Idea;
use FluentRoadmap\App\Models\User;

class IdeaHandler
{
    public function ideaMoved($idea, $old_stage_id)
    {
        if (!$idea || $idea->type != 'roadmap') {
            return;
        }

        $roadmapBoard = $idea->board;

        if (!$roadmapBoard || $roadmapBoard->type != 'roadmap') {
            return;
        }

        $stage = $idea->stage;

        $stageSettings = $stage->settings;

        if(isset($stageSettings['is_public']) && !$stageSettings['is_public']) {
            // this stage is private/admin only, so no email should be sent
            return;
        }

        if (!isset($roadmapBoard->meta['enable_stage_change_email']) || !$roadmapBoard->meta['enable_stage_change_email'])  {
            // email notification is disabled for this board
            return;
        }

        as_enqueue_async_action('fluent_roadmap/send_email_idea_submitter', [$idea->id, $old_stage_id ], 'fluent-roadmap');
        as_enqueue_async_action('fluent_roadmap/send_email_idea_commenters', [$idea->id, $old_stage_id ], 'fluent-roadmap');

    }

}