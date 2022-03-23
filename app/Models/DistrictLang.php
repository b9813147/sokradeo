<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\DistrictLang
 *
 * @property int $id
 * @property int $districts_id
 * @property int $locales_id
 * @property string|null $name 學區名稱
 * @property string|null $description 學區描述
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Districts $districts
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Locale[] $locales
 * @property-read int|null $locales_count
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictLang newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictLang newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictLang query()
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictLang whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictLang whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictLang whereDistrictsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictLang whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictLang whereLocalesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictLang whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictLang whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DistrictLang extends Model
{
    protected $table = 'district_langs';
    protected $primaryKey = 'id';
    protected $fillable = ['lang', 'name', 'description','districts_id'];


    public function districts()
    {
        return $this->belongsTo(Districts::class);
    }

    public function locales()
    {
        return $this->hasMany(Locale::class);
    }
}
