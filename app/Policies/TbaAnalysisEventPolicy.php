<?php

namespace App\Policies;

use App\Models\User;
use App\Models\TbaAnalysisEvent;
use Illuminate\Auth\Access\HandlesAuthorization;

class TbaAnalysisEventPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the tba anal event.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\TbaAnalysisEvent  $tbaAnalEvent
     * @return mixed
     */
    public function view(User $user, TbaAnalysisEvent $tbaAnalEvent)
    {
        return false;
    }

    /**
     * Determine whether the user can create tba anal events.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return false;
    }

    /**
     * Determine whether the user can update the tba anal event.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\TbaAnalysisEvent  $tbaAnalEvent
     * @return mixed
     */
    public function update(User $user, TbaAnalysisEvent $tbaAnalEvent)
    {
        return false;
    }

    /**
     * Determine whether the user can delete the tba anal event.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\TbaAnalysisEvent  $tbaAnalEvent
     * @return mixed
     */
    public function delete(User $user, TbaAnalysisEvent $tbaAnalEvent)
    {
        return false;
    }

    /**
     * Determine whether the user can edit(update and delete) the tba anal event.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\TbaAnalysisEvent  $tbaAnalEvent
     * @return mixed
     */
    public function edit(User $user, TbaAnalysisEvent $tbaAnalEvent)
    {
        return $this->update($user, $tbaAnalEvent) && $this->delete($user, $tbaAnalEvent);
    }
}
