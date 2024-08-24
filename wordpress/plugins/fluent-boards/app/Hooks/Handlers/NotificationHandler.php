<?php

namespace FluentBoards\App\Hooks\Handlers;

use FluentBoards\App\Models\Task;
use FluentBoards\App\Models\User;
use FluentBoards\App\Models\Board;
use FluentBoards\App\Models\Notification;
use FluentBoards\App\Services\Constant;

class NotificationHandler
{
    public function addCommentNotification($comment)
    {
        if($comment->task_id){
            $task = Task::findOrFail($comment->task_id);
            $userIdsWhoGetNotification = $this->findUsersWhoWillGetNotification($task);
            if(count($userIdsWhoGetNotification) > 0){
                $plainDescription = strip_tags($comment->description);
                $action = $comment->parent_id ? 'task_reply_added' : 'task_comment_added';
                $message = $plainDescription;
                $settings = [ 'comment_id' => $comment->parent_id ?? $comment->id ];
                $notification = $this->createNotification($task, Constant::OBJECT_TYPE_BOARD_NOTIFICATION, $action, $message, $settings);
                $notification->users()->attach($userIdsWhoGetNotification);
            }
        }
    }

    public function mentionInCommentNotification($id, $comment_by, $mentionIds)
    {
        $commenter_name = User::where('id', $comment_by)->first()->display_name;
        $task_title = Task::findOrFail($id)->title;
        $board_id = Task::findOrFail($id)->board_id;
        $message = $commenter_name . ' mentioned you in a comment.';

        foreach ($mentionIds as $id) {
            if ($id != $comment_by) {
                Notification::create([
                    'user_id'       => (int) $id,
                    'board_id'      => (int) $board_id,
                    'activity_by'   => (int) $comment_by,
                    'activity_type' => 'mention',
                    'description'   => $message,
                ]);
            }
        }
    }

    public function addSubtaskNotification($parentTask, $subTask)
    {
        $userIdsWhoGetNotification = $this->findUsersWhoWillGetNotification($parentTask);
        if(count($userIdsWhoGetNotification) > 0){
            $board_id = $subTask->board_id;
            $action = 'subtask_added';
            $message = $subTask->title ;
            $notification = $this->createNotification($parentTask, Constant::OBJECT_TYPE_BOARD_NOTIFICATION, $action, $message);
            $notification->users()->attach($userIdsWhoGetNotification);
        }
    }

    public function changeDateNotification($task, $oldDate)
    {
        $userIdsWhoGetNotification = $this->findUsersWhoWillGetNotification($task);
        if(count($userIdsWhoGetNotification) > 0){
            $action = 'task_date_changed';
            $message = date_i18n('F j, Y, g:i a', strtotime($task->due_at));
            $notification = $this->createNotification($task, Constant::OBJECT_TYPE_BOARD_NOTIFICATION, $action, $message);
            $notification->users()->attach($userIdsWhoGetNotification);
        }
    }

    public function changeStageNotification($task, $oldStageId)
    {
        $userIdsWhoGetNotification = $this->findUsersWhoWillGetNotification($task);
        if(count($userIdsWhoGetNotification) > 0){
            $action = 'task_moved_to_new_stage';
            $message = $task->stage->title;
            $notification = $this->createNotification($task, Constant::OBJECT_TYPE_BOARD_NOTIFICATION, $action, $message);
            $notification->users()->attach($userIdsWhoGetNotification);
        }
    }

    public function changePriorityNotification($task, $oldPriority)
    {
        $userIdsWhoGetNotification = $this->findUsersWhoWillGetNotification($task);
        if(count($userIdsWhoGetNotification) > 0){
            $action = 'task_priority_changed';
            $message = $task->priority;
            $notification = $this->createNotification($task, Constant::OBJECT_TYPE_BOARD_NOTIFICATION, $action, $message);
            $notification->users()->attach($userIdsWhoGetNotification);
        }
    }

    public function taskArchiveNotification($task)
    {
        $userIdsWhoGetNotification = $this->findUsersWhoWillGetNotification($task);
        if(count($userIdsWhoGetNotification) > 0){
            if ($task->archived_at) {
                $action = 'board_task_archived';
                $message = $task->title;
            } else {
                $action = 'board_task_restored';
                $message = $task->title;
            }
            $notification = $this->createNotification($task, Constant::OBJECT_TYPE_BOARD_NOTIFICATION, $action, $message);
            $notification->users()->attach($userIdsWhoGetNotification);
        }
    }

    public function changeTitleOrDescriptionNotification($task, $col, $oldTask)
    {
        $userIdsWhoGetNotification = $this->findUsersWhoWillGetNotification($task);
        if(count($userIdsWhoGetNotification) > 0){
            if($col == 'title'){
                $action = 'task_title_updated';
                $message = $task->title;
            }else{
                $action = 'task_description_updated';
                $message = $task->description;
            }
            $notification = $this->createNotification($task, Constant::OBJECT_TYPE_BOARD_NOTIFICATION, $action, $message);
            $notification->users()->attach($userIdsWhoGetNotification);
        }
    }

    public function changeBoardNotification($task, $oldBoardId)
    {
        $userIdsWhoGetNotification = $this->findUsersWhoWillGetNotification($task);
        if(count($userIdsWhoGetNotification) > 0){
            $new_board_id = Task::findOrFail($task->board_id)->board_id;
            $new_board_title = Board::findOrFail($new_board_id)->title;
            $message = 'moved "' . $task->title . '" task to "' . $new_board_title . '" board.';
            $notification = $this->createNotification($oldBoardId, Constant::OBJECT_TYPE_BOARD_NOTIFICATION, $message);
            $notification->users()->attach($userIdsWhoGetNotification);
        }
    }

    public function assigneeAddedNotification($task, $newAssigneeId, $operation)
    {
        if($newAssigneeId != get_current_user_id()){
            $action = 'task_assignee_changed';
            $message = 'has '.$operation.' you as an assignee.';
            $notification = $this->createNotification($task, Constant::OBJECT_TYPE_BOARD_NOTIFICATION, $action, $message);
            $notification->users()->attach($newAssigneeId);
        }
    }

    public function createNotification($task, $objectType, $action, $description, $settings = null)
    {
        $data = [
            'object_id' => $task->board_id,
            'object_type' => $objectType,
            'task_id' => $task->id,
            'action' => $action,
            'description' => $description,
            'settings' => $settings
        ];
        return Notification::create($data);
    }

    public function findUsersWhoWillGetNotification($task)
    {
        $currentUserId = get_current_user_id();
        $watchersOfTask = $task->watchers;
        $watchersExceptCurrentUser = [];
        foreach($watchersOfTask as $watcher){
            if($watcher->ID != $currentUserId){
                $watchersExceptCurrentUser[] = $watcher->ID;
            }
        }
        return $watchersExceptCurrentUser;
    }
}
