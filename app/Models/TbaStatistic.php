<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TbaStatistic
 *
 * @property int $tba_id
 * @property int $type
 * @property float|null $freq
 * @property float|null $duration
 * @property float|null $idx
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Tba $tba
 * @method static \Illuminate\Database\Eloquent\Builder|TbaStatistic newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TbaStatistic newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TbaStatistic query()
 * @method static \Illuminate\Database\Eloquent\Builder|TbaStatistic whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaStatistic whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaStatistic whereFreq($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaStatistic whereIdx($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaStatistic whereTbaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaStatistic whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaStatistic whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TbaStatistic extends Model
{
    //
    protected $fillable = [
            'tba_id', 'type', 'freq', 'duration', 'idx',
    ];
    
    //
    public function tba()
    {
        return $this->belongsTo('App\Models\Tba');
    }
}
