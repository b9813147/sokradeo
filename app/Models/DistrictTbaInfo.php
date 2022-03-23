<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\DistrictTbaInfo
 *
 * @property int $id
 * @property int $districts_id
 * @property int $tbas_id
 * @property int|null $subject_fields_id
 * @property string|null $grade
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Districts $districts
 * @property-read \App\Models\SubjectField|null $subjectFields
 * @property-read \App\Models\Tba|null $tbas
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictTbaInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictTbaInfo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictTbaInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictTbaInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictTbaInfo whereDistrictsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictTbaInfo whereGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictTbaInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictTbaInfo whereSubjectFieldsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictTbaInfo whereTbasId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictTbaInfo whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DistrictTbaInfo extends Model
{
    protected $fillable = [
        'districts_id',
        'tbas_id',
        'subject_fields_id',
        'grade',
    ];

    public function districts()
    {
        return $this->belongsTo(Districts::class);
    }

    public function tbas()
    {
        return $this->hasOne(Tba::class);
    }

    public function subjectFields()
    {
        return $this->hasOne(SubjectField::class);
    }
}
