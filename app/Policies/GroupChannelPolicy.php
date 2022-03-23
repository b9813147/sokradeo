<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Group;
use App\Models\GroupChannel;
use Illuminate\Auth\Access\HandlesAuthorization;

class GroupChannelPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the groupChannel.
     *
     * @param  \App\Models\User  $user
     * @param  \App\GroupChannel  $groupChannel
     * @return mixed
     */
    public function view(User $user, GroupChannel $groupChannel)
    {
        if ($groupChannel->status !== 1) {
            return false;
        }
        
        if (Group::findOrFail($groupChannel->group_id)->status !== 1) {
            return false;
        }
        
        if ($groupChannel->public === 1) {
            return true;
        }
        
        return $user->groups()->where('status', 1)->select('id')->get()->contains('id', $groupChannel->group_id);
    }

    /**
     * Determine whether the user can create groupChannels.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return false; // 待修改
    }

    /**
     * Determine whether the user can update the groupChannel.
     *
     * @param  \App\Models\User  $user
     * @param  \App\GroupChannel  $groupChannel
     * @return mixed
     */
    public function update(User $user, GroupChannel $groupChannel)
    {
        return false; // 待修改
    }

    /**
     * Determine whether the user can delete the groupChannel.
     *
     * @param  \App\Models\User  $user
     * @param  \App\GroupChannel  $groupChannel
     * @return mixed
     */
    public function delete(User $user, GroupChannel $groupChannel)
    {
        return false; // 待修改
    }
}
