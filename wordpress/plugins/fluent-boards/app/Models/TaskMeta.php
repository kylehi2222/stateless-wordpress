<?php

namespace FluentBoards\App\Models;

class TaskMeta extends Model
{
    protected $table = 'fbs_task_metas';

    protected $guarded = ['id'];

    protected $fillable = [
        'task_id',
        'key',
        'value'
    ];

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id', 'id');
    }

    public function setValueAttribute($value)
    {
        $this->attributes['value'] = \maybe_serialize($value);
    }

    public function getValueAttribute($value)
    {
        return \maybe_unserialize($value);
    }
}
