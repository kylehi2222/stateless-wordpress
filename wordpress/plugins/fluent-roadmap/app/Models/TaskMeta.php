<?php

namespace FluentRoadmap\App\Models;

use FluentRoadmap\App\Models\Model;


class TaskMeta extends Model
{
	protected $table = 'ft_task_metas';
	protected $guarded = ['id'];

	public function task()
	{
		return $this->belongsTo(Idea::class, 'task_id', 'id');
	}

	public function label()
	{
		return $this->belongsTo(BoardTerm::class, 'meta_value', 'id');
	}
    public function setMetaValueAttribute($value)
    {
        $this->attributes['meta_value'] = \maybe_serialize($value);
    }
    public function getMetaValueAttribute($value)
    {
        return \maybe_unserialize($value);
    }


}
