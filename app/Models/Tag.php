<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $table = 'tags';
    protected $fillable = [
        'id',
        'is_positive',
        'content',
        'type_id',
        'status'
    ];
    public $incrementing = false;
    protected $keyType = 'string';

    public function tbaComment(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(TbaComment::class);
    }

    public function tagType(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(TagType::class, 'type_id', 'id');
    }
}
