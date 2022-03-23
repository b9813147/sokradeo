<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\DistrictSubject
 *
 * @property int $id
 * @property string|null $subject 學科名稱
 * @property string|null $alias 學科別名
 * @property int|null $order 排序
 * @property int $districts_id
 * @property int|null $subject_fields_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictSubject newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictSubject newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictSubject query()
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictSubject whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictSubject whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictSubject whereDistrictsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictSubject whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictSubject whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictSubject whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictSubject whereSubjectFieldsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DistrictSubject whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DistrictSubject extends Model
{
    protected $fillable = [
        'subject',
        'alias',
        'order',
        'districts_id',
        'subject_fields_id',
    ];
}
