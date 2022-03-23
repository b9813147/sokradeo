<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Oauth2Member
 *
 * @property int $users_id
 * @property string $oauth2_account
 * @property string $sso_server
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Oauth2Member newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Oauth2Member newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Oauth2Member query()
 * @method static \Illuminate\Database\Eloquent\Builder|Oauth2Member whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Oauth2Member whereOauth2Account($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Oauth2Member whereSsoServer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Oauth2Member whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Oauth2Member whereUsersId($value)
 * @mixin \Eloquent
 */
class Oauth2Member extends Model
{
    protected $fillable = ['users_id', 'oauth2_account', 'sso_server'];
}
