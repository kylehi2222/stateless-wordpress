<?php

namespace FluentRoadmap\App\Models;

use FluentBoards\Framework\Database\Orm\Builder;
use FluentRoadmap\App\Models\Model;

class TaskComment extends Model
{
	protected $table = 'frm_task_comments';
	protected $guarded = ['id'];

	protected $fillable = [
		'board_id',
		'task_id',
		'parent_id',
		'message',
		'type',
		'status',
		'user_id',
		'extra',
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'user_id', 'ID');
	}

	public function commentReactions()
	{
		return $this->hasMany(TaskActivity::class, 'object_id', 'id');
	}

	public function task()
	{
		return $this->belongsTo(Idea::class);
	}
}
