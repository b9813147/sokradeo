<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TbaAnnex
 *
 * @property int $id
 * @property int $tba_id
 * @property int $resource_id
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Resource $resource
 * @property-read \App\Models\Tba $tba
 * @method static \Illuminate\Database\Eloquent\Builder|TbaAnnex newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TbaAnnex newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TbaAnnex query()
 * @method static \Illuminate\Database\Eloquent\Builder|TbaAnnex whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaAnnex whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaAnnex whereResourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaAnnex whereTbaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaAnnex whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaAnnex whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TbaAnnex extends Model
{
    //
    protected $fillable = [
            'tba_id', 'resource_id', 'type',
    ];
    
    //
    public function tba()
    {
        return $this->belongsTo('App\Models\Tba');
    }
    
    //
    public function resource()
    {
        return $this->belongsTo('App\Models\Resource');
    }
}
