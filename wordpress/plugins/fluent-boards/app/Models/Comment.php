<?php

namespace FluentBoards\App\Models;

class Comment extends Model
{
    protected $table = 'fbs_comments';

    protected $guarded = ['id'];

    protected $appends = ['avatar'];

    protected $fillable = [
        'board_id',
        'task_id',
        'parent_id',
        'type',
        'privacy',
        'status',
        'author_name',
        'author_email',
        'author_ip',
        'description',
        'settings',
        'created_by',
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->created_by) && is_user_logged_in()) {
                $model->created_by = get_current_user_id();
            }

            if (empty($model->type)) {
                $model->type = 'comment';
            }

            if (empty($model->privacy)) {
                $model->privacy = 'private';
            }

            if (!empty($model->created_by) && empty($model->author_email)) {
                $user = get_user_by('ID', $model->created_by);
                if ($user) {
                    $model->author_email = $user->user_email;
                    $name = trim($user->first_name . ' ' . $user->last_name);
                    if (!$name) {
                        $name = $user->display_name;
                    }
                    $model->author_name = $name;
                }
            }
            
            static::created(function ($model) {
                if ($model->type == 'comment') {
                    $task = $model->task;
                    if ($task) {
                        $task->comments_count = $task->comments_count + 1;
                        $task->save();
                    }
                }
            });

            static::deleted(function ($model) {
                if ($model->type == 'comment') {
                    $task = $model->task;
                    if ($task) {
                        $task->comments_count = $task->comments_count - 1;
                        $task->save();
                    }
                }
            });

        });
    }

    /**
     * One2One: Activity belongs to one User
     * @return \FluentCrm\Framework\Database\Orm\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by', 'ID');
    }

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id', 'id');
    }

    public function scopeByTask($query, $taskId)
    {
        return $query->where('task_id', $taskId);
    }

    public function scopePrivacy($query, $type)
    {
        return $query->where('privacy', $type);
    }

    public function setSettingsAttribute($settings)
    {
        $this->attributes['settings'] = \maybe_serialize($settings);
    }

    public function getSettingsAttribute($settings)
    {
        return \maybe_unserialize($settings);
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id', 'id');
    }

    /**
     * Accessor to get dynamic photo attribute
     * @return string
     */
    public function getAvatarAttribute()
    {
        $fallBack = '';
        if (isset($this->attributes['author_name'])) {
            $fallBack = $this->attributes['author_name'];
        }

        $email = $this->attributes['author_email'];

        if (!$email && $this->created_by) {
            $user = get_user_by('ID', $this->created_by);
            if ($user) {
                $email = $user->user_email;
            }
        }

        return fluent_boards_user_avatar($email, $fallBack);
    }

    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function parentComment()
    {
        return $this->hasOne(Comment::class, 'parent_id', 'id');
    }

}
