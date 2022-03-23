<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Uri
 *
 * @property int $id
 * @property int $resource_id
 * @property string $url
 * @property int|null $duration
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Resource $resource
 * @method static \Illuminate\Database\Eloquent\Builder|Uri newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Uri newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Uri query()
 * @method static \Illuminate\Database\Eloquent\Builder|Uri whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Uri whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Uri whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Uri whereResourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Uri whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Uri whereUrl($value)
 * @mixin \Eloquent
 */
class Uri extends Model
{
    //
    protected $fillable = [
            'resource_id', 'url', 'duration'
    ];
    
    //
    public function resource()
    {
        return $this->belongsTo('App\Models\Resource');
    }
}
