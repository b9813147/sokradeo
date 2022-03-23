<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\GroupChannelContent
 *
 * @property int $group_id
 * @property int $group_channel_id
 * @property int $content_id
 * @property string $content_type
 * @property int $content_status
 * @property int $content_public
 * @property int|null $group_subject_fields_id
 * @property int|null $grades_id
 * @property int|null $ratings_id
 * @property int|null $author_id 最後修改作者
 * @property int $content_update_limit 限制課例上傳的更新 1=啟用 0=停用
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|\Eloquent $content
 * @property-read \App\Models\GradeLang|null $grade
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Rating[] $groupRatingFields
 * @property-read int|null $group_rating_fields_count
 * @property-read \App\Models\GroupSubjectFields|null $groupSubjectFields
 * @method static \Illuminate\Database\Eloquent\Builder|GroupChannelContent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GroupChannelContent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GroupChannelContent query()
 * @method static \Illuminate\Database\Eloquent\Builder|GroupChannelContent whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupChannelContent whereContentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupChannelContent whereContentPublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupChannelContent whereContentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupChannelContent whereContentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupChannelContent whereContentUpdateLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupChannelContent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupChannelContent whereGradesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupChannelContent whereGroupChannelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupChannelContent whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupChannelContent whereGroupSubjectFieldsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupChannelContent whereRatingsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupChannelContent whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class GroupChannelContent extends Model
{
    //
    protected $fillable = [
        'group_id', 'group_channel_id', 'content_id', 'content_type', 'content_status', 'group_subject_fields_id', 'grades_id', 'ratings_id', 'share_status', 'c_score', 'division_id'
    ];

    //
    public function content()
    {
        return $this->morphTo('content');
    }

    public function groupSubjectFields()
    {
        return $this->hasOne(GroupSubjectFields::class, 'id', 'group_subject_fields_id');
    }

    public function groupRatingFields()
    {
        return $this->hasMany(Rating::class, 'id', 'ratings_id');
    }

    public function grade()
    {
        return $this->hasOne(GradeLang::class, 'grades_id', 'grades_id');
    }
}
