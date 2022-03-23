<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\DistrictUser
 *
 * @property int $id
 * @property int $districts_id
 * @property int $user_id
 * @property int $member_status
 * @property string $member_duty
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Districts[] $district
 * @property-read int|null $district_count
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictUser whereDistrictsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictUser whereMemberDuty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictUser whereMemberStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictUser whereUserId($value)
 * @mixin \Eloquent
 */
class DistrictUser extends Model
{
    protected $fillable = ['user_id', 'member_duty', 'member_status', 'districts_id'];

    public function district()
    {
        return $this->hasMany(Districts::class, 'id', 'districts_id');
    }
}
