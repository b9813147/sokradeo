<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\DesignatedVideo
 *
 * @property int $id
 * @property int|null $group_id 頻道 ID
 * @property int|null $tba_id 影片 ID
 * @property string|null $team_model_id 醍摩豆帳號
 * @property int $view 觀看
 * @property int $comment 點頻
 * @property int $score 打分數
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|DesignatedVideo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DesignatedVideo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DesignatedVideo query()
 * @method static \Illuminate\Database\Eloquent\Builder|DesignatedVideo whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DesignatedVideo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DesignatedVideo whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DesignatedVideo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DesignatedVideo whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DesignatedVideo whereTbaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DesignatedVideo whereTeamModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DesignatedVideo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DesignatedVideo whereView($value)
 * @mixin \Eloquent
 */
class DesignatedVideo extends Model
{
    protected $fillable = ['group_id','tba_id','team_model_id','score','point','read'];
}
