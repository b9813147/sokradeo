<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BbLicense extends Model
{
    protected $fillable = ['name', 'code'];


    public function groups(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Group::class)->withPivot('storage', 'deadline', 'status');
    }

}
