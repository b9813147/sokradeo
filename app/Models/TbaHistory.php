<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TbaHistory
 *
 * @property int $user_id
 * @property int $tba_id
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Tba $tba
 * @method static \Illuminate\Database\Eloquent\Builder|TbaHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TbaHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TbaHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder|TbaHistory whereTbaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaHistory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaHistory whereUserId($value)
 * @mixin \Eloquent
 */
class TbaHistory extends Model
{
    public $timestamps = false;

    protected $dates = ['updated_at'];

    //
    protected $fillable = [
        'user_id', 'tba_id', 'updated_at', 'url'
    ];

    //
    public function tba()
    {
        return $this->belongsTo('App\Models\Tba');
    }
}
