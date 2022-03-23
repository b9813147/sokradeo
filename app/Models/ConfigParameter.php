<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ConfigParameter
 *
 * @property string $cate
 * @property string $attr
 * @property string|null $val
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ConfigParameter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ConfigParameter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ConfigParameter query()
 * @method static \Illuminate\Database\Eloquent\Builder|ConfigParameter whereAttr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ConfigParameter whereCate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ConfigParameter whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ConfigParameter whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ConfigParameter whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ConfigParameter whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ConfigParameter whereVal($value)
 * @mixin \Eloquent
 */
class ConfigParameter extends Model
{
    //
}
