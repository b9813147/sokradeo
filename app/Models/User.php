<?php

namespace App\Models;

use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string|null $habook
 * @property string|null $client_id
 * @property string|null $client_user
 * @property string|null $email
 * @property string $name
 * @property string|null $thumbnail
 * @property string|null $remember_token
 * @property int|null $group_channel_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\DistrictUser[] $districtUser
 * @property-read int|null $district_user_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Group[] $groups
 * @property-read int|null $groups_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Resource[] $resources
 * @property-read int|null $resources_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Role[] $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tba[] $tbas
 * @property-read int|null $tbas_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Video[] $videos
 * @property-read int|null $videos_count
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereClientUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereGroupChannelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereHabook($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereThumbnail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use Notifiable {
        notify as protected laravelNotify;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'habook', 'client_id', 'client_user', 'email', 'name', 'thumbnail', 'group_channel_id', 'notification_count'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'client_id', 'client_user', 'remember_token',
    ];

    /**
     * 接收用戶的頻道廣播通知.
     *
     * @return string
     */
    public function receivesBroadcastNotificationsOn()
    {
        return 'users.' . $this->id;
    }

    public function notify($instance)
    {
        // 通知是該用戶 ，就不通知
//        if ($this->id === \Auth::id()) {
//            return;
//        }
        // 通知+1
        $this->increment(('notification_count'));
        $this->laravelNotify($instance);
    }

    public function markAsRead()
    {
        //通知數量歸0
        $this->notification_count = 0;
        $this->save();
        $this->unreadNotifications->markAsRead();
    }


    //
    public function roles()
    {
        return $this->belongsToMany('App\Models\Role')->withTimestamps();
    }

    //
    public function resources()
    {
        return $this->hasMany('App\Models\Resource');
    }

    //
    public function videos()
    {
        return $this->hasMany('App\Models\Video');
    }

    //
    public function tbas()
    {
        return $this->hasMany('App\Models\Tba');
    }

    //
    public function groups()
    {
        return $this->belongsToMany('App\Models\Group')->withPivot('member_status', 'member_duty', 'user_data')->withTimestamps();
    }

    public function districtUser()
    {
        return $this->hasMany(DistrictUser::class, 'user_id', 'id');
    }

    public function divisions()
    {
        return $this->belongsToMany(Division::class);
    }

    /**
     * Get the entity's notifications.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function notifications(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(DatabaseNotification::class, 'notifiable')->orderBy('data->top', 'desc')->orderBy('created_at', 'desc');
    }
}
