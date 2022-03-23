<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\VideoIndex
 *
 * @property int $id
 * @property int $video_id
 * @property string $name
 * @property int $time
 * @property string|null $thumbnail
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Video $video
 * @method static \Illuminate\Database\Eloquent\Builder|VideoIndex newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VideoIndex newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VideoIndex query()
 * @method static \Illuminate\Database\Eloquent\Builder|VideoIndex whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VideoIndex whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VideoIndex whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VideoIndex whereThumbnail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VideoIndex whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VideoIndex whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VideoIndex whereVideoId($value)
 * @mixin \Eloquent
 */
class VideoIndex extends Model
{
    //
    protected $fillable = [
            'video_id', 'name', 'time', 'thumbnail',
    ];
    
    //
    public function video()
    {
        return $this->belongsTo('App\Models\Video');
    }
}
