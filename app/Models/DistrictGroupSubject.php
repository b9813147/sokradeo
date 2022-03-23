<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\DistrictGroupSubject
 *
 * @property int $id
 * @property int|null $group_subject_fields_id
 * @property int|null $district_subjects_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictGroupSubject newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictGroupSubject newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictGroupSubject query()
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictGroupSubject whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictGroupSubject whereDistrictSubjectsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictGroupSubject whereGroupSubjectFieldsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictGroupSubject whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictGroupSubject whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DistrictGroupSubject extends Model
{
    protected $fillable = [
        'district_subjects_id',
        'group_subject_fields_id',
    ];
}
