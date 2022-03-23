<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TbaEvaluateEventMode
 *
 * @property int $id
 * @property string $identity
 * @property string $event
 * @property string|null $mode
 * @property int $type
 * @property string|null $color
 * @property string|null $style
 * @property int|null $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TbaEvaluateEvent[] $tbaEvaluateEvents
 * @property-read int|null $tba_evaluate_events_count
 * @method static \Illuminate\Database\Eloquent\Builder|TbaEvaluateEventMode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TbaEvaluateEventMode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TbaEvaluateEventMode query()
 * @method static \Illuminate\Database\Eloquent\Builder|TbaEvaluateEventMode whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaEvaluateEventMode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaEvaluateEventMode whereEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaEvaluateEventMode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaEvaluateEventMode whereIdentity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaEvaluateEventMode whereMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaEvaluateEventMode whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaEvaluateEventMode whereStyle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaEvaluateEventMode whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaEvaluateEventMode whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TbaEvaluateEventMode extends Model
{
    //
    public function tbaEvaluateEvents()
    {
        return $this->hasMany('App\Models\TbaEvaluateEvent');
    }
}
