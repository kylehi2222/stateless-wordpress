<?php

namespace FluentRoadmap\App\Models;

use FluentRoadmap\App\Models\Model;

class User extends Model
{   
    protected $table = 'users';

	protected $primaryKey = 'ID';

	protected $hidden = ['user_pass', 'user_activation_key'];
	
	protected $appends = [ 'photo'];

	/**
	 * Accessor to get dynamic photo attribute
	 * @return string
	 */
	public function getPhotoAttribute()
	{
        return fluent_boards_user_avatar($this->attributes['user_email'], $this->attributes['display_name']);
	}

	public function tasks()
    {
        return $this->hasMany(Idea::class, 'user_id', 'id');
    }
}
