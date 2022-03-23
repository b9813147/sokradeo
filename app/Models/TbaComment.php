<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TbaComment extends Model
{
    protected $table = 'tba_comments';
    protected $primaryKey = 'id';

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    
    protected $fillable = [
        'nick_name',
        'tag_id',
        'tba_id',
        'user_id',
        'comment_type',
        'time_point',
        'text',
        'public',
        'group_id'
    ];

    public function tag(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Tag::class, 'id', 'tag_id');
    }

    public function tba(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Tba::class);
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tbaCommentAttaches(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TbaCommentAttach::class);
    }

}
