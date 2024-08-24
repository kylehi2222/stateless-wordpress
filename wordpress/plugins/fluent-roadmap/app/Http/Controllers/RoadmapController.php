<?php

namespace FluentRoadmap\App\Http\Controllers;

use FluentBoards\App\Models\BoardTerm;
use FluentBoards\App\Models\Comment;
use FluentBoards\Framework\Http\Request\Request;
use FluentRoadmap\App\Models\Board;
use FluentRoadmap\App\Models\Idea;
use FluentRoadmap\App\Services\Helper;

class RoadmapController extends Controller
{

    public function getStageIdeas(Request $request, $boardId, $stage_id)
    {
        $board = Board::findOrFail($boardId);

        if ($stage_id == 'all-ideas') {
            $publicStages = $board->getPublicStates();

            $stageSlugs = [];

            foreach ($publicStages as $stage) {
                $stageIds[] = $stage['id'];
            }

            $ideas = Idea::whereIn('stage_id', $stageIds)
                ->orderBy('position', 'ASC')
                ->with('stage')
                ->paginate();
        } else {
            $stage = BoardTerm::find($stage_id);

            $ideas = Idea::where('stage_id', $stage->id)
                ->orderBy('position', 'ASC')
                ->paginate();
        }

        foreach ($ideas as $idea) {
            $idea->author = $idea->getAuthorData();
            $idea->isVoted = !!$idea->getCurrentUserVote();
            $idea->vote_count = $idea->getMeta('upvote', 0);
            $idea->comments_count = $idea->getMeta('comments_count', 0);
            unset($idea->settings);
        }

        return [
            'ideas' => $ideas
        ];
    }

    public function createIdea(Request $request, $boardId)
    {
        $board = Board::findOrFail($boardId);

        $targetStage = $board->getNewIdeaStage();

        if (!$targetStage) {
            return $this->sendError([
                'message' => __('You can not create idea in this board', 'fluent-roadmap')
            ]);
        }

        $data = $request->get('idea', []);

        $this->validate($data, [
            'title'       => 'required|string|max:192',
            'description' => 'string|min:10',
        ]);

        $userId = get_current_user_id();

        if (!$userId) {
            $this->validate($data['author'], [
                'name'  => 'required|string|max:192',
                'email' => 'required|email|max:192',
            ]);
        }

        if ($userId) {
            $description = wp_kses_post($data['description']);
        } else {
            $description = sanitize_textarea_field($data['description']);
        }

        $ideaData = [
            'title'       => sanitize_text_field($data['title']),
            'description' => $description,
            'stage_id'    => $targetStage->id,
            'type'        => 'roadmap',
            'board_id'    => $board->id,
        ];

        if (!$userId) {
            $ideaData['settings']['author'] = [
                'name'  => sanitize_text_field($data['author']['name']),
                'email' => sanitize_email($data['author']['email']),
            ];
        } else {
            $ideaData['created_by'] = $userId;
        }

        do_action('fluent_roadmap/before_idea_submit', $ideaData, $board, $targetStage);

        $idea = Idea::create($ideaData);

        $idea->updateMeta('upvote', 0);
        $idea->updateMeta('comments_count', 0);

        do_action('fluent_roadmap/idea_created', $idea, $board, $targetStage);

        return [
            'idea'              => $idea,
            'message'           => __('Idea has been created', 'fluent-roadmap'),
            'confirmation_text' => 'Thank you for your idea. Once it\'s reviewed, approved for planning by our team, we will notify you via email.',
        ];
    }

    public function voteIdea(Request $request, $ideaId)
    {
        $idea = Idea::findOrFail($ideaId);
        $isVoted = $idea->toggleVote('upvote');

        return [
            'isVoted'   => !!$isVoted,
            'new_count' => $idea->getMeta('upvote', 0)
        ];
    }

    public function getIdea(Request $request, $board_id, $task_id)
    {
        $board = Board::findOrFail($board_id);

        $idea = Idea::where('id', $task_id)
            ->where('board_id', $board->id)
            ->with(['public_comments', 'stage'])
            ->firstOrFail();

        $idea->public_comments->each(function ($comment) use ($idea) {
            $comment->makeHidden(['author_email', 'author_ip']);
            if (!$comment->created_by) {
                $comment->badget = '';
                return;
            }
            if ($comment->created_by == $idea->created_by) {
                $comment->badget = 'author';
            } else if (user_can($comment->created_by, 'edit_comment')) {
                $comment->badget = 'admin';
            }
        });

        $idea->author = $idea->getAuthorData();

        $idea->isVoted = !! $idea->getCurrentUserVote();
        $idea->vote_count = $idea->getMeta('upvote', 0);
        $idea->comments_count = $idea->getMeta('comments_count', 0);
        unset($idea->settings);

        return [
            'idea' => $idea
        ];
    }

    public function addComment(Request $request, $boardId, $ideaSlug)
    {
        $board = Board::findOrFail($boardId);

        $idea = Idea::where('board_id', $board->id)
            ->with(['public_comments'])
            ->where('slug', $ideaSlug)
            ->firstOrFail();

        $data = $request->get('comment', []);

        $this->validate($data, [
            'message' => 'required|string|min:10',
        ]);

        $userId = get_current_user_id();

        if (!$userId) {
            $this->validate($data['author'], [
                'author_name'  => 'required|string|max:192',
                'author_email' => 'required|email|max:192',
            ]);
        } else {
            $user = get_user_by('ID', $userId);
            $name = trim($user->first_name . ' ' . $user->last_name);
            if (!$name) {
                $name = $user->display_name;
            }
            $data['author'] = [
                'name'       => $name,
                'email'      => $user->user_email,
                'created_by' => $user->ID
            ];
        }

        $ipAddress = Helper::getClientIP();

        $commentData = [
            'board_id'    => $board->id,
            'task_id'     => $idea->id,
            'type'        => 'comment',
            'privacy'     => 'public',
            'status'      => 'published',
            'description' => wpautop(sanitize_textarea_field($data['message'])),
            'author_ip'   => $ipAddress
        ];

        $commentData = wp_parse_args($commentData, $data['author']);

        $comment = Comment::create($commentData);

        $idea->increaseComment();

        return [
            'message' => __('Your comment has been added', 'fluent-roadmap'),
            'comment' => $comment
        ];
    }

    public function deleteComment(Request $request, $commentId)
    {
        try {
            Comment::where('id', $commentId)->delete();
            return [
                'message' => __('Comment has been deleted successfully', 'fluent-roadmap')
            ];
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 404);
        }
    }

    /**
     * Create a new idea from public page to the roadmap board
     *
     * @param \FluentBoards\Framework\Http\Request\Request $request
     * @return \WP_REST_Response
     */

    public function storeIdea(Request $request)
    {
        $roadmapData = $this->roadmapSanitizeAndValidate($request->all(), [
            'title'            => 'required|string|max:150',
            'description'      => 'string|min:2',
            'user_email'       => 'nullable|string',
            'roadmap_board_id' => 'required',
        ]);

        try {
            $idea = $this->roadmapService->createIdea($roadmapData);

            return $this->sendSuccess([
                'message' => __("Roadmap has been created", 'fluent-roadmap'),
                'idea'    => $idea,
            ], 200);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 400);
        }
    }

    public function commentRoadmapIdea(Request $request)
    {
        $roadmapData = $this->roadmapCommentSanitizeAndValidate($request->all(), [
            'task_id' => 'required|int',
        ]);

        try {
            $idea = $this->commentService->commentRoadmap($request->all());

            return $this->sendSuccess([
                'message' => __("Comment has been created", 'fluent-roadmap'),
                'idea'    => $idea,
            ], 200);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 400);
        }
    }

    private function roadmapSanitizeAndValidate($data, array $rules)
    {
        $data = Helper::sanitizeRoadmap($data);
        return $this->validate($data, $rules);
    }

    private function roadmapCommentSanitizeAndValidate($data, array $rules)
    {
        $data = Helper::sanitizeComment($data);
        return $this->validate($data, $rules);
    }

    /**
     * Get all comments replies depends on comment id
     *
     * @param $comment_id int
     * @return \WP_REST_Response
     */

    public function getReplies($comment_id)
    {
        try {
            $replies = $this->commentService->fetchReplies($comment_id);

            return $this->sendSuccess([
                'replies' => $replies,
            ], 200);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 404);
        }
    }

    /**
     * Comment modify
     *
     * @param \FluentBoards\Framework\Http\Request\Request $request
     * @param $comment_id int
     * @return \WP_REST_Response
     */

    public function commentUpdate(Request $request, $comment_id)
    {
        try {
            $comment = $this->commentService->updateComment($request->message, $comment_id);

            if (!$comment) {
                return $this->sendError('Unauthorized Action', 401);
            }

            return $this->sendSuccess([
                'description' => $comment->description,
                'message'     => __('Comment has been updated', 'fluent-roadmap'),
            ], 200);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 404);
        }
    }

    /**
     * store comment replies
     *
     * @param \FluentBoards\Framework\Http\Request\Request $request
     * @param $task_id int
     * @return \WP_REST_Response
     */

    public function storeReplies(Request $request, $task_id)
    {
        $requestData = [
            'message'    => $request->message,
            'user_id'    => $request->user_id,
            'user_name'  => $request->user_name,
            'user_email' => $request->user_email,
            'task_id'    => $task_id,
            'type'       => 'comment',
            'parent_id'  => $request->parent_id
        ];

        $commentData = $this->commentSanitizeAndValidate($requestData, [
            'message'   => 'required|string',
            'task_id'   => 'required|integer',
            'parent_id' => 'required|string'
        ]);

        try {
            $comment = $this->commentService->createReply($commentData, $task_id);
            return $this->sendSuccess([
                'comment' => $comment,
                'message' => __('Reply has been added', 'fluent-roadmap'),
            ], 201);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 404);
        }
    }

    /**
     * set comment reaction vote
     *
     * @param \FluentBoards\Framework\Http\Request\Request $request
     * @param $task_id int
     * @return \WP_REST_Response
     */

    public function setReactionVote(Request $request)
    {
        try {
            $reactions = $this->roadmapService->storeReaction($request, $this->user);

            return $this->sendSuccess([
                'reactions' => $reactions,
                'message'   => __('Reaction has been added', 'fluent-roadmap'),
            ], 201);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 404);
        }
    }

    /**
     * get comment reaction statistics
     *
     * @param $comment_id int
     * @return \WP_REST_Response
     */

    public function getCommentReaction($comment_id)
    {
        try {
            $reactions = $this->roadmapService->countReactionCount($comment_id);

            return $this->sendSuccess([
                'reactions' => $reactions,
                'message'   => __('Reaction count', 'fluent-roadmap'),
            ], 201);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 404);
        }
    }

    public function commentDelete($comment_id)
    {
        try {
            $reactions = $this->commentService->commentTrash($comment_id);

            return $this->sendSuccess([
                'reactions' => $reactions,
                'message'   => __('Comment has been deleted', 'fluent-roadmap'),
            ], 201);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 404);
        }
    }

    private function commentSanitizeAndValidate($data, array $rules = [])
    {
        $data = Helper::sanitizeComment($data);
        return $this->validate($data, $rules);
    }
}
