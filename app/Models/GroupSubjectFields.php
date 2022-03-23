<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\GroupSubjectFields
 *
 * @property int $id
 * @property string|null $subject 學科名稱
 * @property string|null $alias 別名
 * @property int|null $groups_id
 * @property int|null $subject_fields_id
 * @property int|null $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SubjectFieldLang[] $subjectFields
 * @property-read int|null $subject_fields_count
 * @method static \Illuminate\Database\Eloquent\Builder|GroupSubjectFields newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GroupSubjectFields newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GroupSubjectFields query()
 * @method static \Illuminate\Database\Eloquent\Builder|GroupSubjectFields whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupSubjectFields whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupSubjectFields whereGroupsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupSubjectFields whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupSubjectFields whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupSubjectFields whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupSubjectFields whereSubjectFieldsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupSubjectFields whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class GroupSubjectFields extends Model
{
    protected $fillable = ['id', 'subject', 'subject_fields_id', 'groups_id', 'order'];

    public function subjectFields()
    {
        return $this->hasMany(SubjectFieldLang::class, 'subject_fields_id', 'subject_fields_id');
    }
}
