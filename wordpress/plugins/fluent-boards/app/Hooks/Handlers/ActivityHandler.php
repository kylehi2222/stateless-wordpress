<?php

namespace FluentBoards\App\Hooks\Handlers;

use FluentBoards\App\Models\Board;
use FluentBoards\App\Models\Stage;
use FluentBoards\App\Models\User;
use FluentBoards\App\Services\Constant;
use FluentCrm\App\Models\Subscriber;
use FluentBoards\App\Models\Comment;
use FluentBoards\App\Services\Helper;

class ActivityHandler
{
    public function createLogActivity($taskId, $action, $column, $oldValue = null, $newValue = null, $description = null, $settings = null )
    {
        $data = [
            'object_type' => Constant::ACTIVITY_TASK,
            'object_id' => $taskId,
            'action' => $action, // action type: changed, updated, added, removed
            'column' => $column,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'description' => $description,
            'settings' => $settings
        ];

        Helper::createActivity($data);
    }
    public function logMoveTaskToAnotherBoardActivity($task, $oldTask)
    {
        $old = $this->getBoardTitleById($oldTask->board_id);
        $new = $this->getBoardTitleById($task->board_id);
        $this->createLogActivity( $task->id,'changed', 'board', $old, $new);
    }

    public function logAssigneeChangeActivity($task, $newAssigneeId, $operation)
    {
        $user = User::findOrFail($newAssigneeId);
        if($user) {
            $this->createLogActivity($task->id, $operation, 'assignee', null, $user->display_name);
        }
    }

    public function logTaskCreationActivity($task)
    {
        $this->createLogActivity( $task->id,'created', 'task', null, $task->title, null );
    }

    public function logTaskContentUpdatedActivity($task, $col, $oldTask = null)
    {
        $action = 'updated';
        if($col == 'description'){
            $this->createLogActivity( $task->id, $action, $col, $oldTask->description, $task->description );
        }else{
            if($task->parent_id){
                $this->createLogActivity( $task->parent_id, $action, 'subtask', $oldTask->title, $task->title );
            }else{
                $this->createLogActivity( $task->id, $action, $col, $oldTask->title, $task->title );
            }
        }
    }

    public function taskLabelActivity($task, $label, $action)
    {
        $column = 'label';
        $settings = [
            'bg_color' => $label->bg_color,
            'color' => $label->color,
            'title' => $label->title,
        ];

        $this->createLogActivity($task->id, $action, $column, null, null, null, $settings);
    }

    public function logDueDateActivity($task, $oldDate, $dateType)
    {
        $column = '';
        $action = 'changed';
        if($dateType == 'Due Date'){
            if($oldDate){
                $new = gmdate("F j, Y, g:i a", strtotime($task->due_at));
                $old = gmdate("F j, Y, g:i a", strtotime($oldDate));
                if($new == $old)
                    return;
            }
            $column = 'Due Date';
            $newDueDate = $task->due_at;
            $new = null;
            if ($newDueDate) {
                $new = gmdate("F j, Y, g:i a", strtotime($newDueDate));
                if(str_contains($new, '12:00 am')){
                    $new = gmdate("F j, Y", strtotime($newDueDate));
                }
            }
            $old = null;
            if(!$oldDate){
                $action = 'added';
            }else{
                $old = gmdate("F j, Y, g:i a", strtotime($oldDate));
                if(str_contains($old, '12:00 am')){
                    $old = gmdate("F j, Y", strtotime($oldDate));
                }
            }
        }else {
            if($oldDate){
                $new = gmdate("F j, Y", strtotime($task->started_at));
                $old = gmdate("F j, Y", strtotime($oldDate));
                if($new == $old)
                    return;
            }
            $column = 'Start Date';
            $newStartDate = $task->started_at;
            $new = null;
            if ($newStartDate) {
                $new = gmdate("F j, Y", strtotime($newStartDate));
            }
            $old = null;
            if(!$oldDate){
                $action = 'added';
            }else{
                $old = gmdate("F j, Y", strtotime($oldDate));
            }
        }

        $this->createLogActivity($task->id, $action, $column, $old, $new);
    }

    public function logPriorityChangeActivity($task, $old)
    {
        $new = $task->priority;
        $this->createLogActivity($task->id, 'changed', 'priority', $old, $new);
    }

    public function logCommentCreateActivity($comment)
    {
        if($comment->parent_id) {
            // dd($comment);
            $parent = Comment::findOrFail($comment->parent_id);
            $parentDescription = $parent->description;
            $this->createLogActivity($comment->task_id, 'added', 'a reply'); 
        }else{
            $commentPlainText = strip_tags($comment->description);
            $this->createLogActivity($comment->task_id, 'added', 'comment', null, $commentPlainText, null);
        }
    }
    public function logCommentUpdateActivity($id, $oldComment, $newComment)
    {
        $this->createLogActivity($id, 'updated', 'comment', $oldComment, $newComment);
    }
    public function logCommentDeleteActivity($id, $commentDescription)
    {
        $this->createLogActivity($id, 'removed', 'comment', null, $commentDescription, null);
    }

    public function logSubtaskAddedActivity($parentTask, $subTask)
    {
        $this->createLogActivity($parentTask->id, 'added', 'subtask', null, $subTask->title);
    }

    public function logSubtaskDeletedActivity($id, $subTaskTitle)
    {
        $this->createLogActivity($id, 'removed', 'subtask', null, $subTaskTitle);
    }

    public function logTaskCompletedOrReopenActivity($task, $status)
    {
        if($status == 'completed'){
            if($task->parent_id){
                $this->createLogActivity($task->parent_id, 'completed', 'subtask', $task->title);
            }else{
                $this->createLogActivity($task->id, 'completed', 'task', $task->title);
            }
        }else{
            if($task->parent_id){
                $this->createLogActivity($task->parent_id, 'reopened', 'subtask', $task->title);
            }else{
                $this->createLogActivity($task->id, 'reopened', 'task', $task->title);
            }
        }
    }

    public function logTaskStageUpdatedActivity($task, $oldStageId)
    {
        $oldStage = Stage::findOrFail($oldStageId);
        $newStage = Stage::findOrFail($task->stage_id);
        if($oldStage && $newStage){
            $this->createLogActivity($task->id, 'changed', 'stage', $oldStage->title, $newStage->title);
        }
    }

    public function associateUserAddChangeRemoveActivity($oldAssociateId, $newAssociateId, $taskId)
    {
        $subsciber = new Subscriber();
        $oldAssociateUser = $subsciber->find($oldAssociateId);
        $newAssociateUser = $subsciber->find($newAssociateId);
        $action = 'added';
        $column = 'the associate email';
        $old = null; $new = null;

        if ($oldAssociateUser && $newAssociateUser) {
            $old = $oldAssociateUser->email;
            $new = $newAssociateUser->email;
            $action = 'changed';
        } elseif ($oldAssociateUser && !$newAssociateUser) {
            $old = $oldAssociateUser->email;
            $action = 'removed';
        } else {
            $new = $newAssociateUser->email;
        }
        $this->createLogActivity($taskId, $action, $column, $old, $new);
    }

    public function taskAssigneeJoin($id)
    {
        $this->createLogActivity($id, 'joined', 'task', null, null);
    }

    public function taskAssigneeLeave($id)
    {
        $this->createLogActivity($id, 'left', 'task', null, null);
    }

    public function labelManageForTaskActivity($id, $action)
    {
        $this->createLogActivity($id, $action, 'label', null, null);
    }

    private function getBoardTitleById($boardId)
    {
        return Board::findOrFail($boardId)->title; // it can be further optimized , we may limit multiple query here.
    }

    public function taskAddedFromFluentForms($task)
    {
        $taskActivity = [
            'object_type' => Constant::ACTIVITY_TASK,
            'object_id' => $task->id,
            'action' => 'created', // action type: changed, updated, added, removed
            'column' => 'task',
            'old_value' => $task->title,
            'new_value' => null,
            'description' => " from Fluent Forms",
        ];

        $board = Board::find($task->board_id);
        $settings = $board->settings ?? [];

        if (isset($settings['tasks_count'])) {
            if ($settings['tasks_count'] != 0)  $settings['tasks_count'] += 1;
        } else {
            $settings['tasks_count'] = 1;
        }
        $board->settings = $settings;
        $board->save();
        $taskStage = $task->stage;
        $settings = ['task_id' => $task->id];

        $boardActivity = [
            'object_type' => Constant::ACTIVITY_BOARD,
            'object_id' => $task->board_id,
            'action' => 'created', // action type: changed, updated, added, removed, created
            'column' => 'task',
            'old_value' => null,
            'new_value' => $task->title,
            'description' => 'on stage '. $taskStage->title . ' from Fluent Forms ',
            'settings' => $settings
        ];
        if (intval($task->created_by) > 0) {
            $taskActivity['created_by'] = intval($task->created_by);
            $boardActivity['created_by'] = intval($task->created_by);

        } else {
            $taskActivity['description'] .= ' by '. $task['settings']['author']['name'] . " (" . $task['settings']['author']['email'] . ")";
            $boardActivity['description'] .= ' by '. $task['settings']['author']['name'] . " (" . $task['settings']['author']['email'] . ")";
        }
        Helper::createActivity($taskActivity);
        Helper::createActivity($boardActivity);

    }
    public function taskAttachmentAdded($attachment)
    {
        $this->createLogActivity($attachment->object_id , 'added', 'attachment', null, strlen($attachment->title) > 0 ? $attachment->title : $attachment->full_url);
    }
    public function taskAttachmentDeleted($attachment)
    {
        $this->createLogActivity($attachment->object_id , 'deleted', 'attachment', null, strlen($attachment->title) > 0 ? $attachment->title : $attachment->full_url);
    }
}
