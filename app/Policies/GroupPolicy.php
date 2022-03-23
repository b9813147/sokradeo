<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Group;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Types\Group\DutyType;

class GroupPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the group.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Group  $group
     * @return mixed
     */
    public function view(User $user, Group $group)
    {
        $group = $user->groups()->find($group->id);
        return !is_null($group);
    }

    /**
     * Determine whether the user can create groups.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return false; // 待修改
    }

    /**
     * Determine whether the user can update the group.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Group  $group
     * @return mixed
     */
    public function update(User $user, Group $group)
    {
        return false; // 待修改
    }

    /**
     * Determine whether the user can delete the group.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Group  $group
     * @return mixed
     */
    public function delete(User $user, Group $group)
    {
        return false; // 待修改
    }
    
    /**
     * Determine whether the user can manage the group.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Group  $group
     * @return mixed
     */
    public function manage(User $user, Group $group)
    {
        $group = $user->groups()->find($group->id);
        return !is_null($group) && DutyType::checkManagement($group->pivot->member_duty);
    }
}
