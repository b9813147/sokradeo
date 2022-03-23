<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\DistrictSubjectField
 *
 * @property int $id
 * @property string|null $subject
 * @property int $districts_id
 * @property int|null $subject_fields_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Districts $district
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictSubjectField newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictSubjectField newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictSubjectField query()
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictSubjectField whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictSubjectField whereDistrictsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictSubjectField whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictSubjectField whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictSubjectField whereSubjectFieldsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictSubjectField whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DistrictSubjectField extends Model
{
    protected $table = 'district_subject_fields';
    protected $primaryKey = 'id';
    protected $fillable = ['subject', 'districts_id', 'subject_fields_id'];

    public function district()
    {
        return $this->belongsTo(Districts::class);
    }
}
