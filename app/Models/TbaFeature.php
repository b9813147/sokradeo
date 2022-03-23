<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TbaFeature
 *
 * @property int $id
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|TbaFeature newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TbaFeature newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TbaFeature query()
 * @method static \Illuminate\Database\Eloquent\Builder|TbaFeature whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaFeature whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaFeature whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaFeature whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TbaFeature extends Model
{
    //
    protected $fillable = [
            'type'
    ];
}
