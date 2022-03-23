<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TbaVideoMap
 *
 * @property int $id
 * @property int $tba_id
 * @property int $video_id
 * @property float $tba_start
 * @property float $tba_end
 * @property float $video_offset
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|TbaVideoMap newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TbaVideoMap newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TbaVideoMap query()
 * @method static \Illuminate\Database\Eloquent\Builder|TbaVideoMap whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaVideoMap whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaVideoMap whereTbaEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaVideoMap whereTbaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaVideoMap whereTbaStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaVideoMap whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaVideoMap whereVideoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaVideoMap whereVideoOffset($value)
 * @mixin \Eloquent
 */
class TbaVideoMap extends Model
{
    //
    protected $fillable = [
            'tba_id', 'video_id', 'tba_start', 'tba_end', 'video_offset',
    ];
    
}
