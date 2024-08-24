<?php

namespace FluentRoadmap\App\Models;

use FluentBoards\Framework\Database\Orm\Builder;
use FluentRoadmap\App\Models\Model;

class IdeaReaction extends Model
{
	protected $table = 'frm_idea_reactions';
	protected $guarded = ['id'];

	protected $fillable = [
		'user_id',
		'object_type',
		'object_id',
		'type',
		'author_ip',
	];

	public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->user_id)) {
                if ($userId = get_current_user_id()) {
                    $model->user_id = $userId;
                }
            }
        });
    }


}
