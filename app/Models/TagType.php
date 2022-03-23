<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TagType extends Model
{
    protected $fillable = ['id', 'order', 'content', 'group_id', 'user_id', 'status'];
    protected $table = 'tag_types';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    public function group(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tag(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Tag::class, 'type_id', 'id');
    }
}
