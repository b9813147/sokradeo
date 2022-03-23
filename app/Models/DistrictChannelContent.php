<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\DistrictChannelContent
 *
 * @property int $id
 * @property int $content_id
 * @property int $districts_id
 * @property int $groups_id
 * @property int $ratings_id
 * @property int|null $grades_id
 * @property int|null $group_subject_fields_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Rating[] $groupRatingFields
 * @property-read int|null $group_rating_fields_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\GroupSubjectFields[] $groupSubjectFields
 * @property-read int|null $group_subject_fields_count
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictChannelContent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictChannelContent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictChannelContent query()
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictChannelContent whereContentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictChannelContent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictChannelContent whereDistrictsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictChannelContent whereGradesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictChannelContent whereGroupSubjectFieldsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictChannelContent whereGroupsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictChannelContent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictChannelContent whereRatingsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictChannelContent whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DistrictChannelContent extends Model
{
    protected $fillable = ['content_id', 'districts_id', 'groups_id', 'ratings_id', 'grades_id', 'group_subject_fields_id'];

    public function groupSubjectFields()
    {
        return $this->hasMany(GroupSubjectFields::class, 'id', 'group_subject_fields_id');
    }

    public function groupRatingFields()
    {
        return $this->hasMany(Rating::class, 'id', 'ratings_id');
    }
}
