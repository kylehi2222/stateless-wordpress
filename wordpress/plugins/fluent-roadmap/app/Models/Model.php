<?php

namespace FluentRoadmap\App\Models;

use FluentBoards\Framework\Database\Orm\Model as BaseModel;

class Model extends BaseModel
{
    protected $guarded = ['id', 'ID'];
}