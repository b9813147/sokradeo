<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\GradeLang
 *
 * @property int $id
 * @property int $grades_id
 * @property int $locales_id
 * @property string|null $name 年級
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Grade $grades
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Locale[] $locales
 * @property-read int|null $locales_count
 * @method static \Illuminate\Database\Eloquent\Builder|GradeLang newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GradeLang newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GradeLang query()
 * @method static \Illuminate\Database\Eloquent\Builder|GradeLang whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GradeLang whereGradesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GradeLang whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GradeLang whereLocalesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GradeLang whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GradeLang whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class GradeLang extends Model
{
    protected $fillable = ['name'];

    protected $hidden = ['created_at', 'updated_at'];

    public function grades()
    {
        return $this->belongsTo(Grade::class);
    }

    public function locales()
    {
        return $this->hasMany(Locale::class);
    }
}
