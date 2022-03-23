<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\VideoHistory
 *
 * @property int $user_id
 * @property int $video_id
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Video $video
 * @method static \Illuminate\Database\Eloquent\Builder|VideoHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VideoHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VideoHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder|VideoHistory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VideoHistory whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VideoHistory whereVideoId($value)
 * @mixin \Eloquent
 */
class VideoHistory extends Model
{
    public $timestamps = false;
    
    protected $dates = ['updated_at'];
    
    //
    protected $fillable = [
            'user_id', 'video_id', 'updated_at',
    ];
    
    //
    public function video()
    {
        return $this->belongsTo('App\Models\Video');
    }
}
