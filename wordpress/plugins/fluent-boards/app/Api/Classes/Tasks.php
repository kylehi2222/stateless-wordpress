<?php

namespace FluentBoards\App\Api\Classes;

defined('ABSPATH') || exit;

use FluentBoards\App\Models\Label;
use FluentBoards\App\Models\Task;
use FluentBoards\App\Services\BoardService;
use FluentBoards\App\Services\Helper;
use FluentBoards\App\Services\PermissionManager;
use FluentCrm\App\Models\Subscriber;


/**
 * Contacts Class - PHP APi Wrapper
 *
 * Contacts API Wrapper Class that can be used as <code>FluentBoardsApi('tasks')</code> to get the class instance
 *
 * @package FluentBoards\App\Api\Classes
 * @namespace FluentBoards\App\Api\Classes
 *
 * @version 1.0.0
 */
class Tasks
{
    private $instance = null;

    private $allowedInstanceMethods = [
        'all',
        'get',
        'find',
        'first',
        'paginate'
    ];

    public function __construct(Task $instance)
    {
        $this->instance = $instance;
    }

    /**
     * Get Tasks by board
     *
     * Use:
     * <code>FluentBoardsApi('tasks')->getTasksByBoard();</code>
     *
     * @param string|int $board_id
     * @param array $with
     * @return array|Task Model
     */
    public function getTasksByBoard($board_id, $with = [])
    {
        if (!$board_id) {
            return [];
        }

        $query = Task::query()
            ->where('board_id', $board_id)
            ->whereNull('parent_id')
            ->whereNull('archived_at')
            ->orderBy('due_at', 'ASC');

        if (!empty($with)) {
            $query->with($with);
        }

        $tasks = $query->get();

        $tasks->transform(function ($task) {
            $task->isOverdue = $task->isOverdue();
            $task->isUpcoming = $task->upcoming();
            $task->contact = Helper::crm_contact($task->crm_contact_id);
            $task->is_watching = $task->isWatching();
            return $task;
        });

        return $tasks;
    }


    /**
     * Get Task by id
     *
     * Use:
     * <code>FluentBoardsApi('tasks')->getTask($id);</code>
     *
     * @param int|string $id Task id
     * @return false|Task Model
     */
    public function getTask($id)
    {
        if (!empty($id)) {
            $task = Task::where('id', $id)->first();

            //checking if current user has access to board
            if (!PermissionManager::userHasPermission($task->board_id)) {
                return false;
            }
            return $task;
        }
        return false;
    }


    /**
     * Get Task Created by
     *
     * Use:
     * <code>FluentBoardsApi('tasks')->getTasksCreatedBy($userId);</code>
     *
     * @param int|string $userId Task id
     * @param array $with
     * @return false|Task Model
     */
    public function getTasksCreatedBy($userId, $with = [])
    {
        if (!empty($userId)) {
            $query = Task::where('created_by', $userId);

            if (!empty($with)) {
                $query->with($with);
            }

            $tasks = $query->get();

            //checking if current user has access to board
            if (!PermissionManager::userHasPermission($tasks->board_id)) {
                return false;
            }

            return $tasks;
        }
        return false;
    }


    public function create($data)
    {
        if (empty($data)) {
            return false;
        }

        if (empty($data['title']) || empty($data['board_id']) || empty($data['stage_id'])) {
            return false;
        }

        $taskData = Helper::sanitizeTask($data);

        if(!empty($taskData['priority']) && !in_array($taskData['priority'], ['low', 'medium', 'high'])) {
            $taskData['priority'] = 'low';
        }
        if(!empty($taskData['status']) && !in_array($taskData['status'], ['open', 'closed'])) {
            $taskData['status'] = 'open';
        }
        if(!empty($data['crm_contact_id'])) {
            $taskData['crm_contact_id'] = $data['crm_contact_id'];
        }

        if(!empty($data['contact_email']) && empty($data['crm_contact_id'])) {
            // Find first if the contact exists
            $contact = FluentCrmApi('contacts')->creteOrUpdate([
                'email' => $data['contact_email'],
                'first_name' => $data['contact_first_name'],
                'last_name' => $data['contact_last_name'],
                'status' => 'subscribed'
            ]);
            $taskData['crm_contact_id'] = $contact->id;
        }

        if(!empty($data['assignees']) && is_array($data['assignees'])) {
            // check if the assignees are valid, they have to be wp user ids
            $users = get_users(['include' => $data['assignees']]);
            $taskData['assignees'] = array_map(function($user) {
                return $user->ID;
            }, $users);
            // after successful task creation we have to add them as board members
        }

        if(!empty($data['labels']) && is_array($data['labels'])) {
            $labelIds = [];
            // check if the labels are valid, they have to be label ids
            foreach ($data['labels'] as $label) {
                if(is_numeric($label)) {
                    $labelModel = Label::where('id', $label)->where('board_id', $data['board_id'])->first();
                    if($labelModel) {
                        $labelId = $labelModel->id;
                        $labelIds[] = $labelId;
                    }
                    continue;
                }
                if(is_string($label)) {
                    $labelModel = Label::where('board_id', $data['board_id'])
                                ->where(function($query) use ($label) {
                                    $query->where('title', $label)
                                        ->orWhere('slug', $label);
                                })->first();
                    if($labelModel) {
                        $labelId = $labelModel->id;
                        $labelIds[] = $labelId;
                    }
                }
            }
            $taskData['labels'] =  $labelIds;
            // we have to map the labels after task creation
        }

        $task =  $this->instance->createTask($taskData);
        // push assignees to board Members
        $boardService = new BoardService();
        if(!empty($taskData['assignees'])) {
            foreach ($taskData['assignees'] as $assigneeId) {
                $boardService->addMembersInBoard($data['board_id'], $assigneeId);
            }
        }

        return $task;
    }


    public function createTask($data = [])
    {
        $defaultData = [
            'title',
            'board_id',
            'stage_id',
            'parent_id',
            'status', // open | closed
            'priority', // low | medium | high
            'source',
            'source_id',
            'description',
            'due_at',
            'crm_contact_id',
            // <or>
            'contact_email',
            'contact_first_name', // this is the full name
            'contact_last_name', // this is the full name
            'contact_status', // default subscribed
            // </or>
            'assignees', // WP User IDs / WP User Emails as an array
            'labels', // array of label ids or titles [1, 'New']
        ];

    }
}
