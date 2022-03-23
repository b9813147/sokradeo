<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupChannel extends Model
{
    //
    protected $fillable = [
        'group_id', 'cms_type', 'name', 'description', 'status', 'public', 'stage', 'upload_ended_at',
    ];

    //
    public function group()
    {
        return $this->belongsTo('App\Models\Group');
    }

    //
    public function videos()
    {
        return $this->morphedByMany('App\Models\Video', 'content', 'group_channel_contents')->withPivot('content_status')->withTimestamps();
    }

    //
    public function tbas()
    {
        return $this->morphedByMany('App\Models\Tba', 'content', 'group_channel_contents')->withPivot('content_status', 'content_public')->withTimestamps();
    }

    public function groupLangs()
    {
        return $this->hasMany(GroupLang::class, 'groups_id');
    }

    public function districtGroup()
    {
        return $this->hasMany(DistrictGroup::class, 'groups_id', 'group_id');
    }

    public function semesters()
    {
        return $this->hasMany(Semester::class, 'group_id');
    }

    public function ObservationClasses()
    {
        return $this->hasMany(ObservationClass::class, 'id');
    }
}
