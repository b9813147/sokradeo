<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

#use Laravel\Scout\Searchable;

/**
 * App\Models\Group
 *
 * @property int $id
 * @property string $school_code
 * @property string $name
 * @property string|null $description
 * @property string|null $thumbnail
 * @property int $status
 * @property int $public
 * @property string $review_status 審核狀態 1=啟用 0=停用
 * @property int $public_note_status 公開點評權限
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\DistrictGroup[] $DistrictGroup
 * @property-read int|null $district_group_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\GroupChannel[] $channels
 * @property-read int|null $channels_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\GroupLang[] $groupLangs
 * @property-read int|null $group_langs_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Group newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Group newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Group query()
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group wherePublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group wherePublicNoteStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereReviewStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereSchoolCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereThumbnail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Group extends Model
{
#    use Searchable;
    //
    protected $fillable = [
        'school_code', 'name', 'description', 'thumbnail', 'status', 'public', 'school_upload_status', 'event_data',
    ];

    //
    public function users()
    {
        return $this->belongsToMany('App\Models\User')->withPivot('member_status', 'member_duty')->withTimestamps();
    }

    //
    public function channels()
    {
        return $this->hasMany('App\Models\GroupChannel');
    }

    public function DistrictGroup()
    {
        return $this->hasMany(DistrictGroup::class, 'groups_id', 'id');
    }

    public function groupLangs()
    {
        return $this->hasMany(GroupLang::class, 'groups_id', 'id');
    }

    public function bbLicenses(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(BbLicense::class)->withPivot('storage', 'deadline', 'status');
    }

}
