<?php

namespace FluentRoadmap\App\Models;

use FluentBoards\App\Models\Comment;
use FluentBoards\App\Models\Stage;
use FluentRoadmap\App\Services\Helper;
use FluentBoards\App\Models\Task;
use FluentBoards\App\Models\TaskMeta;
use FluentBoards\Framework\Database\Orm\Builder;
use FluentForm\Framework\Support\Arr;

class Idea extends Task
{
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->created_by)) {
                if ($userId = get_current_user_id()) {
                    $model->created_by = $userId;
                }
            }

            if (empty($model->slug)) {
                $model->slug = sanitize_title($model->title, 'idea-' . time());
            }

            if (empty($model->type)) {
                $model->type = 'roadmap';
            }

            $model->source = 'page'; //coming from public roadmap page

        });

        /* global scope for task type which means only task_type = deal will be fetched from everywhere in  */
        static::addGlobalScope('type', function (Builder $builder) {
            $builder->where('type', '=', 'roadmap');
        });
    }

    public function getAuthorData($public = true)
    {
        if ($this->created_by) {
            $user = get_user_by('ID', $this->created_by);
            if ($user) {
                $name = trim($user->first_name . ' ' . $user->last_name);
                $name = $name ? $name : $user->display_name;
                $data = [
                    'name'    => $name,
                    'avatar'  => fluent_boards_user_avatar($user->user_email, $name),
                    'user_id' => $user->ID
                ];

                if (!$public) {
                    $data['email'] = $user->user_email;
                }

                return $data;
            }
        }

        $authorData = Arr::get($this->settings, 'author', []);

        if ($authorData) {
            $email = Arr::get($authorData, 'email');
            $name = Arr::get($authorData, 'name') ?? 'Anonymous';
            $data = [
                'name'    => $name,
                'avatar'  => fluent_boards_user_avatar($email, $name),
                'user_id' => null
            ];

            if (!$public) {
                $data['email'] = $email;
            }

            return $data;
        }

        return [
            'name'   => 'Anonymous',
            'avatar' => fluent_boards_user_avatar('', 'Anonymous'),
            'email'  => null
        ];
    }

    public function getCurrentUserVote($type = 'upvote')
    {
        $userId = get_current_user_id();

        if ($userId) {
            return IdeaReaction::where('user_id', $userId)
                ->where('object_id', $this->id)
                ->where('object_type', 'idea')
                ->where('type', $type)
                ->first();
        }

        $ipAddress = Helper::getClientIP();

        return IdeaReaction::where('author_ip', $ipAddress)
            ->where('object_id', $this->id)
            ->where('object_type', 'idea')
            ->first();
    }

    public function increaseComment($type = 'comments_count')
    {
        $exist = TaskMeta::where('task_id', $this->id)->where('key', $type)->first();
        if ($exist) {
            $exist->value += 1;
            $exist->save();
            return $exist;
        }

        return TaskMeta::create([
            'value'   => 1,
            'key'     => $type,
            'task_id' => $this->id
        ]);
    }

    public function toggleVote($type = 'upvote')
    {
        $exist = $this->getCurrentUserVote($type);

        if ($exist) {
            $exist->delete();
            $this->decreaseVote($type);
            return null;
        }

        // Create the vote now
        $data = [
            'object_type' => 'idea',
            'object_id'   => $this->id,
            'type'        => $type,
            'author_ip'   => Helper::getClientIP()
        ];

        $idea = IdeaReaction::create($data);
        $this->increaseVote($type);
        return $idea;
    }

    public function increaseVote($type = 'upvote')
    {
        $exist = TaskMeta::where('task_id', $this->id)->where('key', $type)->first();
        if ($exist) {
            $exist->value += 1;
            $exist->save();
            return $exist;
        }

        return TaskMeta::create([
            'value'   => 1,
            'key'     => $type,
            'task_id' => $this->id
        ]);
    }

    public function decreaseVote($type = 'upvote')
    {
        $exist = TaskMeta::where('task_id', $this->id)->where('key', $type)->first();
        if ($exist) {
            if ($exist->value > 0) {
                $exist->value -= 1;
            }
            $exist->save();
            return $exist;
        }

        return null;
    }

    public function stage()
    {
        return $this->belongsTo(Stage::class, 'stage_id', 'id');
    }
}
