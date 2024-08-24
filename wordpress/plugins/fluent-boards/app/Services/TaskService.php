<?php

namespace FluentBoards\App\Services;

use FluentBoards\App\Models\NotificationUser;
use FluentBoards\App\Services\Constant;
use FluentBoardsPro\App\Models\Attachment;
use FluentBoards\App\Models\Stage;
use FluentBoards\App\Models\Task;
use FluentBoards\App\Models\Board;
use FluentBoards\App\Models\TaskMeta;
use FluentBoards\App\Models\Activity;
use FluentBoards\App\Models\BoardTerm;
use FluentBoards\Framework\Support\Arr;
use FluentRoadmap\App\Models\IdeaReaction;

class TaskService
{
    public function createTask($data, $boardId)
    {
        $board = Board::select('id', 'type')->find($boardId);

        if (!$board) {
            throw new \Exception(__("Board doesn't exists", 'fluent-boards'));
        }

        $stage = Stage::find($data['stage_id']);
        if (!$stage) {
            throw new \Exception(__("Stage doesn't exists", 'fluent-boards'));
        }

        $data['status'] = $stage->defaultTaskStatus();

        if ($board->type == 'roadmap') {
            $current_user = wp_get_current_user();
            $settingData = array(
                'integration_type' => 'feature',
                'logo'             => '',
                'author'           => [
                    'email' => $current_user->user_email // email of who posted this feature
                ],
            );
            $data['settings'] = $settingData;
            $data['type'] = 'roadmap';
        }

        $providerPosition = Arr::get($data, 'position');

        $data['position'] = $this->getLastPositionOfTasks($stage->id);

        $data['board_id'] = $boardId;

        $data = array_filter($data);
        $task = (new Task())->createTask($data);
        if (isset($data['is_template']) && $data['is_template'] == 'yes') {
            $task->updateMeta(Constant::IS_TASK_TEMPLATE, $data['is_template']);
        }

        if ($providerPosition) {
            $task->moveToNewPosition($providerPosition);
        }

//        $this->taskCreatedAction($task);
        $this->loadWithRelations($task, ['assignees', 'labels', 'board']);

        return $task;
    }

    public function loadWithRelations($task, $relations)
    {
        if (!is_array($relations)) {
            return $task;
        }
        $task->load($relations); // $relations = ['assignees', 'board'] in this case
        $task->isOverdue = $task->isOverdue();

        return $task;
    }

    public function getTasksForBoards($filters = ['overdue', 'upcoming'], $limit = 5, $task_ids = [])
    {
        $overDue = $this->getTasksForBoardsByCategory('overdue', $limit, $task_ids);
        $completed = $this->getTasksForBoardsByCategory('completed', $limit, $task_ids);
        $upcoming = $this->getTasksForBoardsByCategory('upcoming', $limit, $task_ids);


        return [
            'overdue'             => $overDue ?? [],
            'upcoming'            => $upcoming ?? [],
            'completed'           => $completed ?? []
        ];
    }

    public function getTasksForBoardsByCategory($category, $limit, $taskIds)
    {
        unset($taskQuery);
        $taskQuery = Task::whereIn('id', $taskIds)
            ->with(['assignees', 'board', 'stage'])
            ->whereNull('archived_at')
            ->where('parent_id', null)
            ->orderBy('due_at', 'ASC');

        if ('overdue' == $category) {
            $taskQuery->overdue();
        } elseif ('upcoming' == $category) {
            $taskQuery->upcoming();
        } elseif ('upcoming_no_duedate' == $category) {
            $taskQuery->whereNull('due_at');
        } elseif ('completed' == $category) {
            $taskQuery->where('status', 'closed');
        } else {
            return [];
        }

        $tasks = $taskQuery->take($limit)->get();

        return $tasks->toArray();
    }

    /*
      * TODO: Refactor this function - For me.
	*/
    public function updateTaskProperty($col, $value, $task)
    {
        $oldTask = clone $task;  // normal assigning won't work here. because objects are passed by reference in php
        $validColumns = [
            'board_id',
            'task_type',
            'reminder_type',
            'remind_at',
            'log_minutes',
            'settings'
        ];

        if (in_array($col, $validColumns) && $task->{$col} != $value) {
            $task->{$col} = $value;
            $task->save();
            //            do_action('fluent_boards/task_prop_changed', $col, $task, $oldTask);
        } elseif ('assignees' == $col) {
            if (is_array($value)) {
                foreach ($value as $id) {
                    $this->updateAssignee($id, $task);
                }
            } else {
                $this->updateAssignee($value, $task);
            }

        } elseif ('crm_contact_id' == $col) {
            $this->updateAssociate($value, $task);
        } elseif ('archived_at' == $col) {
            $this->updateArchive($value, $task);
        } elseif ('status' == $col) {
            $this->updateStatus($value, $task);
        } elseif ('parent_id' == $col) {
            $this->updateParent($value, $task);
        } elseif ('title' == $col) {
            $this->updateTitle($col, $value, $task, $oldTask);
        } elseif ('description' == $col) {
            $this->updateDescription($col, $value, $task, $oldTask);
        } elseif ($col == 'due_at') {
            $this->updateDueDate($value, $task);
        } elseif ($col == 'started_at') {
            $this->updateStartedDate($value, $task);
        } elseif ($col == 'priority') {
            $this->updatePriority($value, $task);
        } elseif ($col == 'is_watching') {
            $this->updateObservationOfCurrentUser($value, $task);
        } elseif ($col == 'last_completed_at') {
            $isClosed = $value == 'true' || $value === true;
            if ($isClosed) {
                $task = $task->close();
            } else {
                $task = $task->reopen();
            }
            $task->save();
        } elseif ($col == 'attachment_count') {
            $settings = $task->settings;
            $settings['attachment_count'] = $task->attachments()->count();
            $task->settings = $settings;
            $task->save();
        } elseif ($col == 'subtask_count') {
            $settings = $task->settings;
            $subtasksCount = Task::where('parent_id', $task->id)->count();
            $settings['subtask_count'] = $subtasksCount;
            $task->settings = $settings;
            $task->save();
        } elseif ($col == 'is_template') {
            if (defined('FLUENT_BOARDS_PRO')) {
                $task->updateMeta(Constant::IS_TASK_TEMPLATE, $value);
            }
        }

        return $task;
    }

    public function updateAssignee($payloadAssigneeId, $task)
    {
        $operation = $task->addOrRemoveAssignee($payloadAssigneeId);
        $task->load('assignees');
        $task->updated_at = current_time('mysql');

        $task->save();

        if ($operation == 'added') {
            if ((new NotificationService())->checkIfEmailEnable($payloadAssigneeId, Constant::BOARD_EMAIL_TASK_ASSIGN, $task->board_id)) {
                $this->sendMailAfterTaskModify('add_assignee', $payloadAssigneeId, $task->id);
            }
//            $assigneeIdsToSendEmail = $this->filterAssigneeToSendEmail($task, $idArray, Constant::BOARD_EMAIL_TASK_ASSIGN);
//            $this->sendMailAfterAddAssignees($assigneeIdsToSendEmail, $task->id);
            do_action('fluent_boards/task_assignee_changed', $task, $payloadAssigneeId, $operation);
        } else {
            if ((new NotificationService())->checkIfEmailEnable($payloadAssigneeId, Constant::BOARD_EMAIL_REMOVE_FROM_TASK, $task->board_id)) {
                $this->sendMailAfterTaskModify('remove_assignee', $payloadAssigneeId, $task->id);
            }
            do_action('fluent_boards/task_assignee_changed', $task, $payloadAssigneeId, $operation);
        }

    }

//    public function filterAssigneeToSendEmail($task, $newAssigneeIds, $purpose)
//    {
//        $toSendEmail = array();
//        foreach ($newAssigneeIds as $assigneeId) {
//            if ((new NotificationService())->checkIfEmailEnabled($task->board_id, $assigneeId, $purpose)) {
//                $toSendEmail[] = $assigneeId;
//            }
//        }
//        return $toSendEmail;
//    }

//    public function defaultWatchingTaskByNewUsers($task, $newIds)
//    {
//        foreach ($newIds as $newId) {
//            if (!$task->watchers->contains($newId)) {
//                $task->watchers()->attach(
//                    $newId,
//                    [
//                        'object_type' => Constant::OBJECT_TYPE_USER_TASK_WATCH,
//                    ]
//                );
//            }
//        }
//    }

//    public function checkIfAnybodyRemovedFromTask($newAssigneeIds, $oldAssigneeIds, $task)
//    {
//        $removedAssignees = array_diff($oldAssigneeIds, $newAssigneeIds);
//        $this->sendMailAfterTaskModify('removed_from_task', $removedAssignees, $task->id);
//        dd($removedAssignees);
//    }

    private function updateAssociate($value, $task)
    {
        // if task has no crm contact and got value null then return current task
        if (($task->crm_contact_id == null || $task->crm_contact_id == 0) && $value == null) {
            return $task;
        }

        $oldAssociateId = $task->crm_contact_id;
        $task->crm_contact_id = $value;
        $task->save();
        $task->contact = Task::lead_contact($task->crm_contact_id);
        do_action('fluent_boards/contact_added_to_task', $task);
        do_action('fluent_boards/associate_user_add_change_remove_activity', $oldAssociateId, $task->crm_contact_id, $task->id);
    }

    private function updateArchive($value, $task)
    {
        if ($value != null) {
            $task->position = 0;
        } else {
            $task->moveToNewPosition(1);
        }
        $task->archived_at = $value == null ? null : current_time('mysql');
        $task->save();
        do_action('fluent_boards/board_task_archived', $task);
        $wathersToSendEmail = (new NotificationService())->filterAssigneeToSendEmail($task->id, Constant::BOARD_EMAIL_TASK_ARCHIVE);
        $this->sendMailAfterTaskModify('task_archived', $wathersToSendEmail, $task->id);
    }

    private function updateStatus($value, $task)
    {
        if ($value == 'closed') {
            $task = $task->close();
        } else {
            $task = $task->reopen();
        }

        do_action('fluent_boards/task_completed_activity', $task, $value);
    }

    private function updateParent($value, $task)
    {
        $task->parent_id = $value;
        $task->save();
    }

    private function updateTitle($col, $value, $task, $oldTask)
    {
        $task->title = $value;
        $task->save();
        do_action('fluent_boards/task_content_updated', $task, $col, $oldTask);
    }

    private function updateDescription($col, $value, $task, $oldTask)
    {
        $task->description = $value;
        $task->save();
        do_action('fluent_boards/task_content_updated', $task, $col, $oldTask);
    }

    private function updateDueDate($value, $task)
    {
        $oldValue = $task->due_at;
        $value = $this->filterNullDate($value);
        $task->due_at = $value;
        $task->save();

        $task = $task->reopen();

        do_action('fluent_boards/task_date_changed', $task, $oldValue, 'Due Date');

        $wathersToSendEmail = (new NotificationService())->filterAssigneeToSendEmail($task->id, Constant::BOARD_EMAIL_DUE_DATE_CHANGE);
        $this->sendMailAfterTaskModify('due_date_update', $wathersToSendEmail, $task->id);
    }

    private function updateStartedDate($value, $task)
    {
        $oldValue = $task->started_at;
        $value = $this->filterNullDate($value);
        $task->started_at = $value;
        $task->save();

        do_action('fluent_boards/task_date_changed', $task, $oldValue, 'Start Date');

    }

    private function updatePriority($value, $task)
    {
        $oldPriority = $task->priority;
        $task->priority = $value;
        $task->save();
        do_action('fluent_boards/task_priority_changed', $task, $oldPriority);
    }

    public function updateObservationOfCurrentUser($value, $task)
    {
        $currentUserId = get_current_user_id();

        if ($value == 'stop') {
            $task->watchers()->detach($currentUserId);
        } else {
            $task->watchers()->syncWithoutDetaching([$currentUserId => ['object_type' => Constant::OBJECT_TYPE_USER_TASK_WATCH]]);
        }
        $task->updated_at = current_time('mysql');
        $task->save();

        if ($value == 'stop') {
            $task->is_watching = false;
        } else {
            $task->is_watching = true;
        }
    }

    public function taskCoverPhotoUpdate($taskId, $imagePath)
    {
        $task = Task::find($taskId);
        if (!$task) {
            return null;
        }

        $settings = unserialize($task->settings);

        $settings['logo'] = $imagePath;
        $task->settings = serialize($settings);
        $task->save();

        return $task;
    }

    public function taskStatusUpdate($taskId, $integrationType)
    {
        $task = Task::find($taskId);
        if (!$task) {
            return null;
        }

        $settings = $task->settings;
        $settings['integration_type'] = $integrationType;
        $task->settings = serialize($settings);
        $task->save();

        return $task;
    }

    public function assignYourselfInTask($boardId, $taskId)
    {
        $task = Task::find($taskId);
        $authUserId = get_current_user_id();

        $boardService = new BoardService();
        if (!$boardService->isAlreadyMember($boardId, $authUserId)) {
            $boardService->addMembersInBoard($boardId, $authUserId);
        }

        $task->addOrRemoveAssignee($authUserId);
        // when user assign himself then he will be watching that task
        $task->watchers()->syncWithoutDetaching([$authUserId => ['object_type' => Constant::OBJECT_TYPE_USER_TASK_WATCH]]);

        $task->load('assignees');

        return $task;
    }

    public function detachYourselfFromTask($boardId, $taskId)
    {
        $task = Task::find($taskId);
        $task->addOrRemoveAssignee(get_current_user_id());
        $task->load('assignees');

        return $task;
    }

    public function deleteTask($task)
    {
        $deleted = $task->delete();

        if ($deleted) {

            //task assignees watchers removed
            $task->watchers()->detach();
            $task->assignees()->detach();

            //removing all task related notifications
            $notificationIds = $task->notifications->pluck('id');
            $task->notifications()->delete();
            NotificationUser::whereIn('notification_id', $notificationIds)->delete();

            //task labels removed
            $task->labels()->detach();

            //task custom field value
            $task->customFields()->detach();

            do_action('fluent_boards/task_deleted', $task);
            TaskMeta::where('task_id', $task->id)->delete();
        }
    }

    public function filterNullDate($date)
    {
        if ('0000-00-00 00:00:00' == $date || false === strtotime($date)) {
            return null;
        }
        return $date;
    }

    // this is invoked when task is moved to another board

    /**
     * @throws \Exception
     */
    public function changeBoardByTask($task, $targetBoardId)
    {
        if ($task->board_id == $targetBoardId) {
            return $task;
        }

        $oldBoard = Board::find($task->board_id);

        $newBoard = Board::find($targetBoardId);
        if (!$newBoard) {
            throw new \Exception('Invalid board id', 400);
        }
        $task->board_id = $targetBoardId;
        $task->save();
        //delete labels of that task because labels have board dependencies
        $task->labels()->detach();

        do_action('fluent_boards/task_moved_from_board', $task, $oldBoard, $newBoard);

        return $task;
    }


    public function getIdeaVoteStatistics($taskId)
    {
        $reactionTypes = [
            [
                'label' => 'Upvote',
                'type'  => 'upvote'
            ],
            [
                'label' => 'Downvote',
                'type'  => 'downvote'
            ]
        ];

        $reactionCounts = [];

        foreach ($reactionTypes as $reactionType) {
            $count = IdeaReaction::where('object_id', $taskId)
                ->where('object_type', 'idea')
                ->where('type', $reactionType['type'])
                ->count();

            $reactionCounts[] = [
                'label' => $reactionType['label'],
                'type'  => $reactionType['type'],
                'count' => $count
            ];
        }

        return $reactionCounts;
    }


    /**
     * Summary of getArchivedOrCompletedTasks
     * this function will return completd tasks or archived tasks based on users input and also can search by name
     * @param mixed $data
     * @param mixed $taskType
     * @return mixed
     * @throws \Exception
     */
    public function getArchivedTasks($data, $boardId)
    {
        $per_page = isset($data['per_page']) ? $data['per_page'] : 25;
        $page = isset($data['page']) ? $data['page'] : 1;
        $tasksQuery = Task::where('board_id', $boardId)->whereNotNull('archived_at');
        if (isset($data['searchInput'])) {
            $tasksQuery = $tasksQuery->where('title', 'LIKE', '%' . $data['searchInput'] . '%');
        }

        // if board_id is not passed then throw an exception
        if (!$boardId) {
            throw new \Exception('Board id is required', 'fluent-boards');
        }

        return $tasksQuery->orderBy('created_at', 'DESC')->with('assignees')->paginate($per_page, ['*'], 'page', $page);
    }

    public function sendMailAfterTaskModify($column, $assigneeIds, $taskId)
    {
        $current_user_id = get_current_user_id();
        /* this will run in background as soon as possible */
        /* sending Model or Model Instance won't work here */

        as_enqueue_async_action('fluent_boards/one_time_schedule_send_email_for_'.$column, [$taskId, $assigneeIds, $current_user_id], 'fluent-boards');
    }

    public function getStageByTask($task_id)
    {
        $task = Task::find($task_id);
        return $task->stage;
    }

    public function moveTaskToNextStage($task_id)
    {
        $task = Task::findOrFail($task_id);

        $oldStage = $task->stage;

        $nextStage = Stage::where('board_id', $task->board_id)
            ->where('position', '>', $oldStage->position)
            ->orderBy('position', 'ASC')
            ->first();

        if (!$nextStage) {
            return $task;
        }

        if ($nextStage->defaultTaskStatus() == 'closed' && $task->status != 'closed') {
            $task->status = 'closed';
            if (!$task->last_completed_at) {
                $task->last_completed_at = current_time('mysql');
            }
        }

        $task->stage_id = $nextStage->id;
        $task->save();

        $task->load(['board', 'stage', 'attachments']);

        $task = $this->loadNextStage($task);

        return $task;
    }

    public function loadNextStage($task)
    {
        $stage = $task->stage;
        $nextStage = Stage::where('board_id', $task->board_id)
            ->where('position', '>', $stage->position)
            ->orderBy('position', 'ASC')
            ->first();

        $task->nextStage = $nextStage ? $nextStage->title : null;
        return $task;
    }

    public function getActivities($taskId, $perPage, $filter = 'newest')
    {
        $activityQuery = Activity::where('object_id', $taskId)
            ->where('object_type', Constant::ACTIVITY_TASK);
        if ($filter == 'newest') {
            $activityQuery = $activityQuery->latest();
        } else if ($filter == 'oldest') {
            $activityQuery = $activityQuery->oldest();
        }
        return $activityQuery->with('user')->paginate($perPage);
    }

    public function getLastOneMinuteUpdatedTasks($boardId, $lastUpdated = null)
    {
        if (!$lastUpdated) {
            $lastUpdated = gmdate('Y-m-d H:i:s', current_time('timestamp') - 60);
        }

        $tasks = Task::query()
            ->where([
                'board_id'  => $boardId,
                'parent_id' => null,
            ])
            ->where('updated_at', '>', $lastUpdated)
            ->with(['assignees', 'labels', 'watchers'])
            ->orderBy('due_at', 'ASC')
            ->get();

        foreach ($tasks as $task) {
            $task->isOverdue = $task->isOverdue();
            $task->isUpcoming = $task->upcoming();
            $task->is_watching = $task->isWatching();
            $task->contact = Task::lead_contact($task->crm_contact_id);
            $task->assignees = Helper::sanitizeUserCollections($task->assignees);
            $task->watchers = Helper::sanitizeUserCollections($task->watchers);
        }
        return $tasks;
    }

    public function getLastPositionOfTasks($stage_id)
    {
        $lastPosition = Task::query()
            ->where('stage_id', $stage_id)
            ->where('parent_id', null)
            ->whereNull('archived_at')
            ->orderBy('position', 'desc')
            ->pluck('position')
            ->first();

        return $lastPosition + 1;
    }

    public function getAssociatedTasks($associatedId)
    {
        $tasks = Task::query()
            ->where('crm_contact_id', $associatedId)
            ->with(['board', 'stage', 'assignees', 'labels', 'watchers',])
            ->orderBy('due_at', 'ASC')
            ->get();

        foreach ($tasks as $task) {
            $task->isOverdue = $task->isOverdue();
            $task->isUpcoming = $task->upcoming();
            $task->contact = Task::lead_contact($task->crm_contact_id);
            $task->is_watching = $task->isWatching();

            $task->assignees = Helper::sanitizeUserCollections($task->assignees);
            $task->watchers = Helper::sanitizeUserCollections($task->watchers);

            $subTasks = Task::query()
                ->where('parent_id', $task->id)
                ->with(['assignees'])
                ->whereNull('archived_at')
                ->orderBy('position', 'ASC')
                ->get();

            foreach ($subTasks as $subTask) {
                $subTask->assignees = Helper::sanitizeUserCollections($subTask->assignees);
            }

            $task->subtasks = $subTasks;
        }

        return $tasks;
    }

    public function copyTasks($boardId, $stageMap, $newBoard)
    {
        $allActiveTasks = Task::where('board_id', $boardId)->whereNull('archived_at')->get();
        $taskMap = [];
        foreach ($allActiveTasks as $task) {
            $newTask = array();
            $newTask['title'] = $task->title;
            $newTask['parent_id'] = $task->parent_id ? $taskMap[$task->parent_id] : null;
            $newTask['description'] = $task->description;
            $newTask['board_id'] = $newBoard->id;
            $newTask['stage_id'] = $stageMap[$task->stage_id];
            $newTask['status'] = $task->status;
            $newTask['priority'] = $task->priority;
            $newTask['position'] = $task->position;
            $newTask['due_at'] = $task->due_at;
            $newTask = Task::create($newTask);
            if(!$task->parent_id){
                $taskMap[$task['id']] = $newTask->id;
                //duplicate labels to task
                $labelIds = $task->labels->pluck('id');
                if($labelIds){
                    $newTask->labels()->attach($labelIds, [
                        'object_type' => Constant::OBJECT_TYPE_TASK_LABEL
                    ]);
                }
            } else {
                $this->subtaskCountUpdate($newTask->parent_id);
            }
        }

        //initiate task count
        $totalTasks = sizeof($allActiveTasks);
        $board = Board::findOrFail($newBoard->id);
        $settings = [];
        $settings['tasks_count'] = $totalTasks;
        $board->settings = $settings;
        $board->save();
    }

    private function subtaskCountUpdate($taskId){
        $parentTask = Task::findOrFail($taskId);
        $settings = $parentTask->settings;
        $settings['subtask_count'] = (int)($settings['subtask_count'] ?? 0) + 1;
        $parentTask->settings = $settings;
        $parentTask->save();
    }


}
