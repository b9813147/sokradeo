<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TbaEvaluateEventFile
 *
 * @property int $id
 * @property int $tba_evaluate_event_id
 * @property string|null $name
 * @property string|null $ext
 * @property string|null $image_url
 * @property string|null $preview_url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\TbaEvaluateEvent $tbaEvaluateEvent
 * @method static \Illuminate\Database\Eloquent\Builder|TbaEvaluateEventFile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TbaEvaluateEventFile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TbaEvaluateEventFile query()
 * @method static \Illuminate\Database\Eloquent\Builder|TbaEvaluateEventFile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaEvaluateEventFile whereExt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaEvaluateEventFile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaEvaluateEventFile whereImageUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaEvaluateEventFile whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaEvaluateEventFile wherePreviewUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaEvaluateEventFile whereTbaEvaluateEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaEvaluateEventFile whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TbaEvaluateEventFile extends Model
{
    //
    protected $fillable = [
            'name', 'ext', 'image_url', 'preview_url'
    ];
    
    //
    public function tbaEvaluateEvent()
    {
        return $this->belongsTo('App\Models\TbaEvaluateEvent');
    }
    
}
