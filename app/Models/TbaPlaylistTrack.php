<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TbaPlaylistTrack
 *
 * @property int $id
 * @property int $tba_id
 * @property int $ref_tba_id
 * @property int $list_order
 * @property string|null $frag_name
 * @property string|null $frag_description
 * @property float|null $time_start
 * @property float|null $time_end
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|TbaPlaylistTrack newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TbaPlaylistTrack newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TbaPlaylistTrack query()
 * @method static \Illuminate\Database\Eloquent\Builder|TbaPlaylistTrack whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaPlaylistTrack whereFragDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaPlaylistTrack whereFragName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaPlaylistTrack whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaPlaylistTrack whereListOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaPlaylistTrack whereRefTbaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaPlaylistTrack whereTbaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaPlaylistTrack whereTimeEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaPlaylistTrack whereTimeStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TbaPlaylistTrack whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TbaPlaylistTrack extends Model
{
    protected $fillable = [
            'tba_id', 'ref_tba_id', 'list_order', 'frag_name', 'frag_description', 'time_start', 'time_end',
    ];
}
