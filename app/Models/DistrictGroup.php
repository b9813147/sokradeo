<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\DistrictGroup
 *
 * @property int $id
 * @property int $districts_id
 * @property int $groups_id
 * @property int|null $list_order 排序
 * @property int|null $list_top 置頂
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\DistrictLang[] $districtLang
 * @property-read int|null $district_lang_count
 * @property-read \App\Models\Districts $districts
 * @property-read \App\Models\Group $groups
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictGroup whereDistrictsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictGroup whereGroupsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictGroup whereListOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictGroup whereListTop($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictGroup whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DistrictGroup extends Model
{
    protected $table = 'district_groups';
    protected $primaryKey = 'id';
    protected $fillable = ['list_order', 'list_top', 'districts_id', 'groups_id'];

    public function districts()
    {
        return $this->belongsTo(Districts::class);
    }

    public function groups()
    {
        return $this->belongsTo(Group::class);
    }

    public function districtLang()
    {
        return $this->hasOne(DistrictLang::class, 'districts_id', 'districts_id');
    }
}
