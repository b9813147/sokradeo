<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TbaEvaluateEvent
 *
 * @property int $id
 * @property int $tba_id
 * @property int|null $tba_evaluate_user_id
 * @property int $tba_evaluate_event_mode_id
 * @property int|null $user_id
 * @property int|null $group_id
 * @property float|null $time_point
 * @property string|null $text
 * @property int $evaluate_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Tba $tba
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TbaEvaluateEventFile[] $tbaEvaluateEventFiles
 * @property-read int|null $tba_evaluate_event_files_count
 * @property-read \App\Models\TbaEvaluateEventMode $tbaEvaluateEventMode
 * @property-read \App\Models\TbaEvaluateUser|null $tbaEvaluateUser
 * @method static \Illuminate\Database\Eloquent\Builder|TbaEvaluateEvent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TbaEvaluateEvent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TbaEvaluateEvent query()
 * @method static \Illuminate\Database\Eloquent\Builder|TbaEvaluateEvent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaEvaluateEvent whereEvaluateType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaEvaluateEvent whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaEvaluateEvent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaEvaluateEvent whereTbaEvaluateEventModeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaEvaluateEvent whereTbaEvaluateUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaEvaluateEvent whereTbaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaEvaluateEvent whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaEvaluateEvent whereTimePoint($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaEvaluateEvent whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaEvaluateEvent whereUserId($value)
 * @mixin \Eloquent
 */
class TbaEvaluateEvent extends Model
{
    //
    protected $fillable = [
        'tba_id',
        'tba_evaluate_user_id',
        'tba_evaluate_event_mode_id',
        'user_id',
        'group_id',
        'time_point',
        'text',
        'evaluate_type',
        'tba_comment_id',
        'created_at',
        'updated_at',
    ];

    //
    public function tba()
    {
        return $this->belongsTo('App\Models\Tba');
    }

    //
    public function tbaEvaluateUser()
    {
        return $this->belongsTo('App\Models\TbaEvaluateUser');
    }

    //
    public function tbaEvaluateEventMode()
    {
        return $this->belongsTo('App\Models\TbaEvaluateEventMode');
    }

    //
    public function tbaEvaluateEventFiles()
    {
        return $this->hasMany('App\Models\TbaEvaluateEventFile');
    }

    public function TbaComment()
    {
        return $this->belongsTo('App\Models\TbaComment');
    }
}
