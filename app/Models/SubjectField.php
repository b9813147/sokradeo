<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SubjectField
 *
 * @property int $id
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectField newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectField newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectField query()
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectField whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectField whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectField whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectField whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SubjectField extends Model
{
    //
    protected $fillable = [
            'type'
    ];
}
