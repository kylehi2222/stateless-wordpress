<?php

namespace FluentRoadmap\App\Models;

use FluentRoadmap\App\Models\Model;

class Comments extends Model
{
	protected $table = 'ft_task_activities';

	protected $guarded = ['id'];

	protected $fillable = [
		'task_id',
		'activity_type',
		'description',
		'extra',
		'created_by'
	];

	public static function boot()
	{
		static::creating(function ($model) {
			$model->created_by = $model->created_by ?: get_current_user_id();
		});
	}

	/**
	 * One2One: Activity belongs to one Task
	 * @return \FluentRoadmap\Framework\Database\Orm\Relations\BelongsTo
	 */
	public function task()
	{
		return $this->belongsTo(Idea::class, 'task_id', 'id');
	}


	/**
	 * One2One: Activity belongs to one User
	 * @return \FluentRoadmap\Framework\Database\Orm\Relations\BelongsTo
	 */
	public function user()
	{
		return $this->belongsTo(User::class, 'created_by', 'ID');
	}

	public function scopeByTask($query, $taskId)
	{
		return $query->where('task_id', $taskId);
	}

	public function scopeType($query, $type)
	{
		return $query->where('activity_type', $type);
	}

	public function setExtraAttribute($settings)
	{
		$this->attributes['extra'] = \maybe_serialize($settings);
	}

	public function getExtraAttribute($settings)
	{
		return \maybe_unserialize($settings);
	}

}
