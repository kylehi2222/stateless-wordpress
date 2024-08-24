<?php

namespace FluentBoards\App\Http\Controllers;

use FluentBoards\App\Models\Task;
use FluentBoards\App\Services\NotificationService;
use FluentBoards\App\Services\Constant;
use FluentBoards\App\Services\Helper;
use FluentBoards\Framework\Http\Request\Request;
use FluentBoards\App\Services\CommentService;

class CommentController extends Controller
{
    private $commentService;
    private $notificationService;

    public function __construct(CommentService $commentService, NotificationService $notificationService)
    {
        parent::__construct();
        $this->commentService = $commentService;
        $this->notificationService = $notificationService;
    }

    public function getComments(Request $request, $board_id, $task_id)
    {
        try {
            $filter = $request->getSafe('filter');
            $per_page =  10;

            $comments = $this->commentService->getComments($task_id, $per_page, $filter);
            $totalComments = $this->commentService->getTotal($task_id);

            return $this->sendSuccess([
                'comments' => $comments,
                'total' => $totalComments
            ], 200);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 404);
        }
    }

    /*
     * handles comment or reply creation
     * @param $board_id int
     * @param $task_id int
     * @return json
     */
    public function create(Request $request, $board_id, $task_id)
    {
        // TODO: Refactor the whole request and sanitize process here.. minimize the code in this functions.
        $requestData = [
            'parent_id'     => $request->parent_id,
            'description'   => $request->comment,
            'created_by'    => $request->comment_by,
            'task_id'       => $task_id,
            'type'          => $request->comment_type ? $request->comment_type : 'comment',
            'board_id'      => (int) $board_id,
        ];

        $commentData = $this->commentSanitizeAndValidate($requestData, [
            'description'   => 'required|string',
            'created_by'    => 'required|integer',
            'board_id'      => 'required|integer',
            'task_id'       => 'required|integer',
            'type'          => 'required|string'
        ]);

        try {
            $comment = $this->commentService->create($commentData, $task_id);
            $comment['user'] = $comment->user;
            //sending emails to assignees who enabled their email
            $usersToSendEmail = $this->notificationService->filterAssigneeToSendEmail($task_id, Constant::BOARD_EMAIL_COMMENT);

            $this->sendMailAfterComment($comment->id, $usersToSendEmail);

//            if($request->mentions)
//            {
//                do_action('fluent_boards/mention_comment_notification', $task_id, get_current_user_id(), $request->mentions);
//            }

            return $this->sendSuccess([
                'message' => __('Comment has been added', 'fluent-boards'),
                'comment' => $comment
            ], 201);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 404);
        }
    }

    public function update(Request $request, $comment_id)
    {
        $commentData = $this->commentSanitizeAndValidate($request->all(), [
            'description' => 'required|string',
        ]);
        try {
            $comment = $this->commentService->update($commentData, $comment_id);

            if ( !$comment ) {
                $errorMessage = __('Unauthorized Action', 'fluent-boards');
                return $this->sendError($errorMessage, 401);
            }

            return $this->sendSuccess([
                'description' => $comment->description,
                'message'     => __('Comment has been updated', 'fluent-boards'),
            ], 200);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 404);
        }
    }

    public function delete($comment_id)
    {
        try {
            $this->commentService->delete($comment_id);

            return $this->sendSuccess([
                'message' => __('Comment has been deleted', 'fluent-boards'),
            ], 200);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 404);
        }
    }

    public function updateReply(Request $request, $task_id)
    {
        $replyData = $this->commentSanitizeAndValidate($request->all(), [
            'description' => 'required|string',
        ]);
        try {
            $reply = $this->commentService->updateReply($replyData, $task_id);

            if (!$reply) {
                $errorMessage = __('Unauthorized Action', 'fluent-boards');
                return $this->sendError($errorMessage, 401);
            }

            return $this->sendSuccess([
                'description' => $reply->description,
                'message'     => __('Reply has been updated', 'fluent-boards'),
            ], 200);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 404);
        }
    }

    public function deleteReply($comment_id)
    {
        try {
            $this->commentService->deleteReply($comment_id);

            return $this->sendSuccess([
                'message' => __('Reply has been deleted', 'fluent-boards'),
            ], 200);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 404);
        }
    }

    public function sendMailAfterComment($commentId, $usersToSendEmail)
    {
        $current_user_id = get_current_user_id();

        /* this will run in background as soon as possible */
        /* sending Model or Model Instance won't work here */
        as_enqueue_async_action('fluent_boards/one_time_schedule_send_email_for_comment', [$commentId, $usersToSendEmail, $current_user_id], 'fluent-boards');
    }

    private function commentSanitizeAndValidate($data, array $rules = [])
    {
        $data = Helper::sanitizeComment($data);

        return $this->validate($data, $rules);
    }
}
