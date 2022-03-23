<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ObservationUser extends Model
{
    protected $table = 'observation_user';
    protected $primaryKey = 'id';
    protected $fillable = ['observation_class_id', 'user_id'];

    public function observationClass(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ObservationClass::class, 'observation_class_id');
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
