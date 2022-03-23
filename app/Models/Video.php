<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Video
 *
 * @property int $id
 * @property int $user_id
 * @property int $resource_id
 * @property string $name
 * @property string|null $description
 * @property string|null $thumbnail
 * @property int $hits
 * @property string|null $encoder
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $author
 * @property string|null $copyright
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\GroupChannel[] $groupChannels
 * @property-read int|null $group_channels_count
 * @property-read \App\Models\Resource $resource
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tag[] $tags
 * @property-read int|null $tags_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TbaVideoMap[] $tbaVideoMaps
 * @property-read int|null $tba_video_maps_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tba[] $tbas
 * @property-read int|null $tbas_count
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\VideoIndex[] $videoIndices
 * @property-read int|null $video_indices_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\VideoMarkStatistic[] $videoMarkStatistics
 * @property-read int|null $video_mark_statistics_count
 * @method static \Illuminate\Database\Eloquent\Builder|Video newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Video newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Video query()
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereAuthor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereCopyright($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereEncoder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereHits($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereResourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereThumbnail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereUserId($value)
 * @mixin \Eloquent
 */
class Video extends Model
{
    //
    protected $fillable = [
            'user_id', 'resource_id', 'name', 'description', 'thumbnail', 'author', 'copyright', 'encoder',
    ];
    
    //
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
    
    //
    public function tags()
    {
        return $this->morphToMany('App\Models\Tag', 'taggable');
    }
    
    //
    public function resource()
    {
        return $this->belongsTo('App\Models\Resource');
    }
    
    //
    public function videoIndices()
    {
        return $this->hasMany('App\Models\VideoIndex');
    }
    
    //
    public function videoMarkStatistics()
    {
        return $this->hasMany('App\Models\VideoMarkStatistic');
    }
    
    //
    public function tbas()
    {
        return $this->belongsToMany('App\Models\Tba')->withPivot('tbavideo_order')->withTimestamps();
    }
    
    //
    public function tbaVideoMaps()
    {
        return $this->hasMany('App\Models\TbaVideoMap');
    }
    
    //
    public function groupChannels()
    {
        return $this->morphToMany('App\Models\GroupChannel', 'content', 'group_channel_contents')->withPivot('content_status')->withTimestamps();
    }
}
