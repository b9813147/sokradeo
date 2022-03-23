<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ObservationClass extends Model
{
    protected $table = 'observation_classes';
    protected $primaryKey = 'id';

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'id',
        'user_id',
        'binding_number',
        'classroom_code',
        'pin_code',
        'duration',
        'status',
        'timestamp',
        'group_id',
        'channel_id',
        'name',
        'content_public',
        'teacher',
        'habook_id',
        'rating_id',
        'group_subject_field_id',
        'grade_id',
        'lecture_date',
        'locale_id',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function group(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function groupChannel(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(GroupChannel::class, 'channel_id');
    }

    public function observationUser(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ObservationUser::class, 'observation_class_id', 'id');
    }

    public function rating(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Rating::class, 'rating_id');
    }

    public function groupSubjectField(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(GroupSubjectFields::class, 'group_subject_field_id');
    }
}
