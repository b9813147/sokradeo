<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Vod
 *
 * @property int $id
 * @property int $resource_id
 * @property string $type
 * @property string $rid
 * @property string $rstatus
 * @property string|null $rdata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Resource $resource
 * @method static \Illuminate\Database\Eloquent\Builder|Vod newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Vod newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Vod query()
 * @method static \Illuminate\Database\Eloquent\Builder|Vod whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vod whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vod whereRdata($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vod whereResourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vod whereRid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vod whereRstatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vod whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vod whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Vod extends Model
{
    //
    protected $fillable = [
            'resource_id', 'type', 'rid', 'rstatus', 'rdata',
    ];
    
    //
    public function resource()
    {
        return $this->belongsTo('App\Models\Resource');
    }
}
