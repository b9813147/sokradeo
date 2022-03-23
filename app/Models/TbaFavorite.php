<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TbaFavorite
 *
 * @property int $id
 * @property int $user_id
 * @property int $channel_id
 * @property int $group_id
 * @property int $tba_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Tba $tba
 * @method static \Illuminate\Database\Eloquent\Builder|TbaFavorite newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TbaFavorite newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TbaFavorite query()
 * @method static \Illuminate\Database\Eloquent\Builder|TbaFavorite whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaFavorite whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaFavorite whereTbaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaFavorite whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaFavorite whereUserId($value)
 * @mixin \Eloquent
 */
class TbaFavorite extends Model
{
    //
    protected $fillable = [
            'user_id', 'channel_id', 'group_id', 'tba_id'
    ];
    
    //
    public function tba()
    {
        return $this->belongsTo('App\Models\Tba');
    }
}
