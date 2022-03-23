<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Districts
 *
 * @property int $id
 * @property string|null $abbr 學區代碼
 * @property string|null $school_code 學校代碼
 * @property string|null $thumbnail 縮圖名稱
 * @property int|null $status 狀態
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\DistrictGroup[] $districtGroups
 * @property-read int|null $district_groups_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\DistrictLang[] $districtLang
 * @property-read int|null $district_lang_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\DistrictSubjectField[] $districtSubjectField
 * @property-read int|null $district_subject_field_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\DistrictTbaInfo[] $districtTbaInfo
 * @property-read int|null $district_tba_info_count
 * @method static \Illuminate\Database\Eloquent\Builder|Districts newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Districts newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Districts query()
 * @method static \Illuminate\Database\Eloquent\Builder|Districts whereAbbr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Districts whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Districts whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Districts whereSchoolCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Districts whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Districts whereThumbnail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Districts whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Districts extends Model
{
    protected $table = 'districts';
    protected $primaryKey = 'id';
    protected $fillable = ['abbr', 'school_code', 'status'];

    public function districtGroups()
    {
        return $this->hasMany(DistrictGroup::class);
    }

    public function districtLang()
    {
        return $this->hasMany(DistrictLang::class);
    }

    public function districtSubjectField()
    {
        return $this->hasMany(DistrictSubjectField::class);
    }

    public function districtTbaInfo()
    {
        return $this->hasMany(DistrictTbaInfo::class);
    }
}
