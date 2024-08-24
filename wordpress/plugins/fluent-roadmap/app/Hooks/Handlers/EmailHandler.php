<?php

namespace FluentRoadmap\App\Hooks\Handlers;

use FluentBoards\App\Models\Stage;
use FluentBoards\App\Services\Libs\Mailer;
use FluentBoards\Framework\Support\Collection;
use FluentRoadmap\App\Models\Idea;
use FluentRoadmap\App\Models\User;
use FluentRoadmap\App\Services\Helper;

class EmailHandler
{

    public function mailToIdeaSubmitter($ideaId, $oldStageId)
    {
        $idea = Idea::find($ideaId);
        $oldStage = Stage::find($oldStageId);

        $ideaSubmitterId = $idea->created_by;

        if($ideaSubmitterId == 0) {
            $settings = $idea->settings;
            $ideaSubmitter = new User();
            $ideaSubmitter->name = $settings['author']['name'];
            $ideaSubmitter->user_email = $settings['author']['email'];
        }  else {
            $ideaSubmitter = User::find($ideaSubmitterId);
        }

        // must have a valid email
        if(!is_email($ideaSubmitter->user_email)) {
            return;
        }

        $roadmapPageUrl = Helper::getRoadmapPageUrl($idea->board);

        $ideaLink = $roadmapPageUrl . '#/idea/' . $idea->id;
        $ideaLinkTag = '<a target="_blank" href="' . $ideaLink . '">' . $idea->title . '</a>';

        $data = [
            'body'        => 'The idea you submitted, ' . $ideaLinkTag . ' has been moved to <strong>' . $idea->stage->title. '</strong> from <strong>' . $oldStage->title. '</strong>.',
            'pre_header'  => 'your idea moved to a new stage',
            'show_footer' => true,
            'site_url'    => site_url(),
            'site_title'  => get_bloginfo('name'),
            'site_logo'   => fluent_boards_site_logo(),
        ];

        $emailSubject = __('Update on Your Idea: Idea Status Changed', 'fluent-roadmap');
        $emailBody    = Helper::loadView('emails.idea_submitter', $data);

        $to  = $ideaSubmitter->name . ' <' . $ideaSubmitter->user_email . '>';

        $mailer = new Mailer($to, $emailSubject, $emailBody);
        return $mailer->send();
    }

    public function mailToIdeaCommenters($ideaId, $oldStageId)
    {
        $idea = Idea::find($ideaId);
        $oldStage = Stage::find($oldStageId);

        $lastCommentersMailSentId = fluent_boards_get_option('_last_commenters_mail_sent_id', 0);
        $commenters  = $idea->comments()->where('privacy', 'public')
                                        ->where('id', '>', $lastCommentersMailSentId)
                                        ->select('description', 'author_name', 'author_email', 'id')
                                        ->orderBy('id')
                                        ->get();


        $commenterNameEmails =  [];

        foreach ($commenters as $comment) {
            $commenterNameEmails[] = [
                'name' => $comment->author_name,
                'email' => $comment->author_email,
                'id' => $comment->id,
                'description' => $comment->description,
            ];
        }

        if(!$commenterNameEmails) {
            fluent_boards_update_option('_last_commenters_mail_sent_id', 0);
            return;
        }

        $roadmapPageUrl = Helper::getRoadmapPageUrl($idea->board);

        $stageLink = $roadmapPageUrl . '#/' . $idea->stage_id;
        $stageLinkTag = '<a target="_blank" href="' . $stageLink . '">' . $idea->stage->title . '</a>';

        $ideaLink = $roadmapPageUrl . '#/idea/' . $idea->id;
        $ideaLinkTag = '<a target="_blank" href="' . $ideaLink . '">' . $idea->title . '</a>';

        $data = [
            'body'        => 'The idea you commented on, ' . $ideaLinkTag . ' has been moved to <strong>' . $idea->stage->title. '</strong> from <strong>' . $oldStage->title. '</strong>.',
            'pre_header'  => 'Idea moved to a new stage',
            'show_footer' => true,
            'site_url'    => site_url(),
            'site_title'  => get_bloginfo('name'),
            'site_logo'   => fluent_boards_site_logo(),
        ];

        $emailSubject = __('Update on Your Comment: Idea Status Changed', 'fluent-roadmap');

        $emailBody = Helper::loadView('emails.idea_commenters', $data);
        $commenters  = $commenterNameEmails;


        if (count($commenters) == 1) {

            $mailer = new Mailer('', $emailSubject, $emailBody);
            $commenter = reset($commenters);

            if (!$commenter || !$commenter['email']) {
                return;
            }

            $mailer->to($commenter['name'] . ' <' . $commenter['email'] . '>');

            $mailer->send();

            fluent_boards_update_option('_last_commenters_mail_sent_id',  0);
            return true;
        }

        // send by BCC by 30 chunks
        $commenters = Collection::make($commenters)->toArray();
        $chunks = array_chunk($commenters, 30);

        $hasMore = false;
        $startTime = time();
        foreach ($chunks as $chunk) {

            $mailer = new Mailer('', $emailSubject, $emailBody);

            $commenters = Collection::make($chunk)->toArray();
            foreach ($commenters as $commenter) {
                if (!$commenter || !$commenter['email']) {
                    continue;
                }
                $mailer->addBCC($commenter['name'] . ' <' . $commenter['email'] . '>');
            }

            $mailer->send();

            fluent_boards_update_option('_last_commenters_mail_sent_id', $commenters[count($commenters) - 1]['id']);

            if (time() - $startTime > 20) {
                $hasMore = true;
                as_schedule_single_action(time() + 10, 'fluent_boards/send_email_idea_commenters', [$ideaId, $oldStageId], 'fluent-roadmap');
                break;
            }
        }

        if (!$hasMore) {
            fluent_boards_update_option('_last_commenters_mail_sent_id', 0);
        }

        return true;

    }

}