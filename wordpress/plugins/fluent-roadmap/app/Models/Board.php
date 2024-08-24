<?php

namespace FluentRoadmap\App\Models;

use FluentBoards\App\Models\BoardTerm;
use FluentBoards\App\Models\Meta;
use FluentBoards\App\Services\Constant;
use FluentBoards\Framework\Database\Orm\Builder;
use FluentBoards\Framework\Support\Arr;
use FluentRoadmap\App\Models\Model;

class Board extends Model
{
    protected $table = 'fbs_boards';

    protected $guarded = ['id'];


    protected $fillable = [
        'title',
        'type',
        'slug',
        'description',
        'currency',
        'archived_at',
        'background',
        'settings',
        'created_by'
    ];

    protected $appends = ['meta'];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = $model->created_by ?: get_current_user_id();
            $model->type = $model->type ?: 'roadmap';
        });

        /* global scope for board type which means only type = sales-pipeline will be fetched from everywhere   */
        static::addGlobalScope('type', function (Builder $builder) {
            $builder->where('type', '=', 'roadmap');
        });
    }

    public function setBackgroundAttribute($settings)
    {
        $this->attributes['extra'] = \maybe_serialize($settings);
    }

    public function getBackgroundAttribute($settings)
    {
        return \maybe_unserialize($settings);
    }


    public function boardTerm()
    {
        return $this->hasMany(BoardTerm::class, 'board_id');
    }

    public function stages()
    {
        return $this->hasMany(BoardTerm::class, 'board_id')
            ->where('type', 'stage')
            ->where('archived_at', null)
            ->orderBy('position', 'ASC');
    }

    public function getPublicStates()
    {
        $stages = BoardTerm::where('board_id', $this->id)
            ->where('type', 'stage')
            ->where('archived_at', null)
            ->orderBy('position', 'ASC')
            ->get();

        $formattedStates = [];

        foreach ($stages as $stage) {
            if (!Arr::get($stage->settings, 'is_public')) {
                continue;
            }

            $formattedStates[] = [
                'id'         => $stage->id,
                'slug'       => $stage->slug,
                'label'      => $stage->title,
                'stage_type' => Arr::get($stage->settings, 'stage_type')
            ];
        }

        return $formattedStates;
    }

    public function getNewIdeaStage()
    {
        return BoardTerm::where('board_id', $this->id)
            ->where('type', 'stage')
            ->where('archived_at', null)
            ->orderBy('position', 'ASC')
            ->first();
    }

    public function getMetaAttribute()
    {
        return $this->getMeta();
    }

    public function getMeta() // get Board Meta only
    {
        $meta = Meta::where('object_id', $this->id)
                    ->where('object_type', Constant::OBJECT_TYPE_BOARD)
                    ->get();

        $formattedMeta = [];

        foreach ($meta as $m) {
            $formattedMeta[$m->key] = $m->value;
        }

        return $formattedMeta;
    }
}
