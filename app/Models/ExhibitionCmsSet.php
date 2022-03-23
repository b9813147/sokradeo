<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ExhibitionCmsSet
 *
 * @property int $cms_id
 * @property string $cms_type
 * @property string $type
 * @property int|null $order
 * @property string|null $thumbnail 圖片
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitionCmsSet newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitionCmsSet newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitionCmsSet query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitionCmsSet whereCmsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitionCmsSet whereCmsType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitionCmsSet whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitionCmsSet whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitionCmsSet whereThumbnail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitionCmsSet whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExhibitionCmsSet whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ExhibitionCmsSet extends Model
{
    //
    protected $fillable = [
            'cms_id', 'cms_type', 'type',
    ];
}
