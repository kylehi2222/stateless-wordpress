<?php

namespace FluentBoardsPro\App\Models;

use FluentBoards\App\Models\Task;

class Attachment extends Model
{
    protected $table = 'fbs_attachments';

    protected $guarded = ['id'];
    protected $fillable = ['file_hash', 'object_type', 'object_id', 'settings', 'file_path', 'full_url', 'file_size', 'attachment_type'];
//    protected $hidden = ['full_url', 'file_path'];

    protected $appends = ['secure_url'];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $uid = wp_generate_uuid4();
            $model->file_hash = md5($uid . mt_rand(0, 1000));
        });
        static::created(function ($model) {
            do_action('fluent_boards/task_attachment_added', $model);
        });
    }

    public function setSettingsAttribute($settings)
    {
        $this->attributes['settings'] = \maybe_serialize($settings);
    }

    public function getSettingsAttribute($settings)
    {
        return \maybe_unserialize($settings);
    }

    public function getSecureUrlAttribute()
    {
        if ($this->attachment_type === 'url') {
            return $this->full_url;
        }
        return add_query_arg([
            'fbs'               => 1,
            'fbs_attachment'    => $this->file_hash,
            'secure_sign' => md5($this->id . date('YmdH'))
        ], site_url('/index.php'));
    }

    public function scopeWithTask($query)
    {
        return $query->where('object_type', 'TASK')->with('task', function ($query) {
            $query->select('id', 'title', 'board_id');
        });
    }

    public function task()
    {
        return $this->belongsTo(Task::class, 'object_id', 'id');
    }


}

