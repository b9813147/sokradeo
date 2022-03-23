<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Resource
 *
 * @property int $id
 * @property int $user_id
 * @property string $src_type
 * @property string $name
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\File|null $file
 * @property-read \App\Models\Uri|null $uri
 * @property-read \App\Models\User $user
 * @property-read \App\Models\Vod|null $vod
 * @method static \Illuminate\Database\Eloquent\Builder|Resource newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Resource newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Resource query()
 * @method static \Illuminate\Database\Eloquent\Builder|Resource whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resource whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resource whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resource whereSrcType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resource whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resource whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resource whereUserId($value)
 * @mixin \Eloquent
 */
class Resource extends Model
{
    //
    protected $fillable = [
            'user_id', 'src_type', 'name', 'status',
    ];
    
    //
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
    
    //
    public function src()
    {
        return $this[$this->src_type];
    }
    
    //
    public function file()
    {
        return $this->hasOne('App\Models\File');
    }
    
    //
    public function uri()
    {
        return $this->hasOne('App\Models\Uri');
    }
    
    //
    public function vod()
    {
        return $this->hasOne('App\Models\Vod');
    }
}
