<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Tba;
use Illuminate\Auth\Access\HandlesAuthorization;

class TbaPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the tba.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Tba  $tba
     * @return mixed
     */
    public function view(User $user, Tba $tba)
    {
        return $user->id === $tba->user_id;
    }

    /**
     * Determine whether the user can create tbas.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the tba.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Tba  $tba
     * @return mixed
     */
    public function update(User $user, Tba $tba)
    {
        return $user->id === $tba->user_id;
    }

    /**
     * Determine whether the user can delete the tba.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Tba  $tba
     * @return mixed
     */
    public function delete(User $user, Tba $tba)
    {
        return $user->id === $tba->user_id;
    }
    
    /**
     * Determine whether the user can update the tba and video.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Tba  $tba
     * @param  \Illuminate\Support\Collection  $videos
     * @return mixed
     */
    public function updateTbaVideo(User $user, Tba $tba, $videos)
    {
        $userId = $user->id;
        
        if ($userId !== $tba->user_id) {
            return false;
        }
        
        $inValid = $videos->contains(function ($v) use ($userId) {
            return $userId !== $v->user_id;
        });
        
        return (! $inValid);
    }
    
}
