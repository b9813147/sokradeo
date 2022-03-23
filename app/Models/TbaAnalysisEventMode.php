<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TbaAnalysisEventMode
 *
 * @property int $id
 * @property string $event
 * @property string|null $mode
 * @property int $type
 * @property string|null $color
 * @property string|null $style
 * @property int|null $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TbaAnalysisEvent[] $tbaAnalysisEvents
 * @property-read int|null $tba_analysis_events_count
 * @method static \Illuminate\Database\Eloquent\Builder|TbaAnalysisEventMode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TbaAnalysisEventMode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TbaAnalysisEventMode query()
 * @method static \Illuminate\Database\Eloquent\Builder|TbaAnalysisEventMode whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaAnalysisEventMode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaAnalysisEventMode whereEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaAnalysisEventMode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaAnalysisEventMode whereMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaAnalysisEventMode whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaAnalysisEventMode whereStyle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaAnalysisEventMode whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaAnalysisEventMode whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TbaAnalysisEventMode extends Model
{
    //
    public function tbaAnalysisEvents()
    {
        return $this->hasMany('App\Models\TbaAnalysisEvent');
    }
}
