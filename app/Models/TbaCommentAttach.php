<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TbaCommentAttach extends Model
{
    protected $fillable = [
        'id','tba_comment_id', 'name', 'ext', 'image_url', 'preview_url'
    ];

    public function tbaComment(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(TbaComment::class);
    }

}
