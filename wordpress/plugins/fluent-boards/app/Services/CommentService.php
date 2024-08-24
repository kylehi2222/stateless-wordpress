<?php

namespace FluentBoards\App\Services;

use FluentBoards\App\Models\Comment;
use FluentBoards\App\Models\Task;
use FluentBoards\App\Models\TaskActivity;

class CommentService
{
    public function getComments($id, $per_page, $filter)
    {
        $task = Task::findOrFail($id);

        $commentsQuery = $task->comments()->whereNull('parent_id')
            ->with(['user']);

        if ($filter == 'oldest') {
            $commentsQuery = $commentsQuery->oldest();
        } else { // latest or newest
            $commentsQuery = $commentsQuery->latest();
        }
        $comments = $commentsQuery->paginate($per_page);

        foreach ($comments as $comment) {
            $comment->replies = $this->getReplies($comment);
            $comment->replies_count = count($comment->replies);
        }

        return $comments;
    }

    public function getTotal($id)
    {
        $task = Task::findOrFail($id);
        $totalComment = Comment::where('task_id', $task->id)
            ->type('comment')
            ->count();
        $totalReply = Comment::where('task_id', $task->id)
            ->type('reply')
            ->count();

        return $totalComment + $totalReply;
    }

    public function getReplies($comment)
    {
        $replies = Comment::where('parent_id', $comment->id)->with(['user'])->get();
        return $replies;
    }

    public function create($commentData, $id)
    {
        $comment = Comment::create($commentData);
        do_action('fluent_boards/task_comment_added', $comment);
        return $comment;
    }

    public function update($commentData, $comment_id)
    {
        $comment = Comment::findOrFail($comment_id);

        if ($comment->created_by != get_current_user_id()) {
            return false;
        }

        $oldComment = $comment->description;
        $comment->description = $commentData['description'];
        $comment->save();
        do_action('fluent_boards/task_comment_updated', $comment->task_id, $oldComment, $comment->description);

        return $comment;
    }

    public function delete($comment_id)
    {
        $comment = Comment::findOrFail($comment_id);
        $taskId = $comment->task_id;

        if ($comment->created_by != get_current_user_id()) {
            return false;
        }

        $commentDescription = strip_tags($comment->description);

        $deleted = $comment->delete();

        if ($deleted) {
            $this->relatedReplyDelete($comment_id);

            $task = Task::findOrFail($taskId);
            $task->comments_count = $task->comments_count - 1;
            $task->save();
        }

        do_action('fluent_boards/task_comment_deleted', $taskId, $commentDescription);
    }

    public function relatedReplyDelete($comment_id)
    {
        $replies = Comment::where('parent_id', $comment_id)
            ->type('reply')
            ->get();
        foreach ($replies as $reply) {
            $reply->delete();
        }
    }

    public function updateReply($replyData, $id)
    {
        $reply = Comment::findOrFail($id);

        if ($reply->created_by != get_current_user_id()) {
            return false;
        }

        $oldReply = $reply->description;
        $reply->description = $replyData['description'];
        $reply->save();
//        do_action('fluent_boards/task_comment_updated', $comment->task_id, $oldComment, $comment->description);

        return $reply;
    }

    public function deleteReply($id)
    {
        $reply = Comment::findOrFail($id);
//        $taskId = $reply->task_id;

        if ($reply->created_by != get_current_user_id()) {
            return false;
        }

        $reply->delete();

//        do_action('fluent_boards/task_comment_deleted', $taskId);
    }
}
