<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\GroupLang
 *
 * @property int $id
 * @property string|null $name 名稱
 * @property string|null $description 描述
 * @property int|null $groups_id
 * @property int|null $locales_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Group|null $groups
 * @method static \Illuminate\Database\Eloquent\Builder|GroupLang newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GroupLang newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GroupLang query()
 * @method static \Illuminate\Database\Eloquent\Builder|GroupLang whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupLang whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupLang whereGroupsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupLang whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupLang whereLocalesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupLang whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupLang whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class GroupLang extends Model
{
    protected $table = 'group_langs';
    protected $fillable = ['groups_id', 'locales_id', 'name', 'description'];

    public function groups()
    {
        return $this->belongsTo(Group::class);
    }
}
