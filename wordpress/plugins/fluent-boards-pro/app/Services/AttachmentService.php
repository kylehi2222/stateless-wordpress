<?php

namespace FluentBoardsPro\App\Services;

use FluentBoardsPro\App\Models\Attachment;
use FluentBoards\App\Models\Task;
use FluentBoards\App\Models\TaskMeta;
class AttachmentService
{
    /**
     * Adds a task attachment to the specified task.
     *
     * @param int $taskId The ID of the task to which the attachment is added.
     * @param string $title The title of the attachment.
     * @param string $url The URL of the attachment.
     *
     * @return Attachment The updated list of task attachments.
     * @throws \Exception
     */
    public function addTaskAttachment($taskId, $data)
    {
        /*
         * I will refactor this function later- within March 2024 Last Week
         */
        $initialDataData = [
            'type' => 'url',
            'url' => '',
            'name' => '',
            'size' => 0,
        ];

        $attachData = array_merge($initialDataData, $data);
        $task = Task::find($taskId);
        if(!isset($task->id)) {
            throw new \Exception(__("Task doesn't exists", 'fluent-boards-pro'));
        }
        $UrlMeta = [];
        if($attachData['type'] == 'url') {
            $UrlMeta = RemoteUrlParser::parse($attachData['url']);
        }
        $attachment = new Attachment();
        $attachment->object_id = $task->id;
        $attachment->object_type = "TASK";
        $attachment->attachment_type = $attachData['type'];
        $attachment->title = $this->setTitle($attachData['type'], $attachData['name'], $UrlMeta);
        $attachment->file_path = $attachData['type'] != 'url' ?  $attachData['file'] : null;
        $attachment->full_url = esc_url($attachData['url']);
        $attachment->file_size = $attachData['size'];
        $attachment->settings = $attachData['type'] == 'url' ? [
            'meta' => $UrlMeta
        ] : '';
        $attachment->driver = 'local';
        $attachment->save();
        $taskSettings = $task->settings;
        $taskSettings['attachment_count'] = (int)($taskSettings['attachment_count'] ?? 0) + 1;
        $task->settings = $taskSettings;
        $task->save();

        return $attachment;

    }
    
    private function setTitle($type, $title, $UrlMeta)
    {
        if($type != 'url') {
            return sanitize_file_name($title);
        }
        return $title ?? $UrlMeta['title'] ?? '';
    }

    public function deleteTaskAttachment($taskId, $attachmentId)
    {
        $task = Task::find($taskId);
        $attachment = Attachment::find($attachmentId);
        $deletedAttachment = clone $attachment;
        $attachment->delete();

        do_action('fluent_boards/task_attachment_deleted', $deletedAttachment);

        $taskSettings = $task->settings;
        $taskSettings['attachment_count'] = max((int)($taskSettings['attachment_count'] ?? 0) - 1, 0);
        $task->settings = $taskSettings;
        $task->save();
        // Return the updated list of task attachments
        return $task->attachments;
    }

    public function updateTaskAttachment($attachmentId, $attachmentTitle)
    {
        $attachment = Attachment::find($attachmentId);

        $attachment->title = $attachmentTitle;
        $attachment->save();

        // Return the updated list of task attachments
        return $attachment;
    }

    public function getAttachTaskFilesLink($task_id)
    {
        return TaskMeta::where('task_id', $task_id)->where('key', 'task-attachment')->orWhere('key', 'task-uploaded-file')->get();
    }

}