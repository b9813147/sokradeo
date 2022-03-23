<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Observers\Model\ClientObserver;

/**
 * App\Models\Client
 *
 * @property string $id
 * @property string $type
 * @property string $name
 * @property string $secret
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Client newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Client newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Client query()
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Client extends Model
{
    public    $incrementing = false;
    
    protected $keyType      = 'string';
    
    protected $fillable = [
            'type', 'name', 'secret',
    ];
    
    public static function boot()
    {
        parent::boot();
        self::observe(ClientObserver::class);
    }
}
