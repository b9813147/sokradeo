<?php

namespace App\Policies;

use App\Models\DesignatedVideo;
use App\Models\User;
use App\Models\Group;
use App\Models\Tba;
use App\Models\TbaEvaluateEvent;
use App\Models\TbaEvaluateEventMode;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Types\App\RoleType;
use App\Types\Group\DutyType;
use App\Types\Tba\IdentityType;

class TbaEvaluateEventPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the tba eval event.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\TbaEvaluateEvent  $tbaEvalEvent
     * @return mixed
     */
    public function view(User $user, TbaEvaluateEvent $tbaEvalEvent)
    {
        return false; // 待修改
    }

    /**
     * Determine whether the user can create tba eval events.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\TbaEvaluateEventMode  $tbaEvalEventMode
     * @param  \App\Models\Tba  $tba
     * @return mixed
     */
    public function create(User $user, TbaEvaluateEventMode $tbaEvalEventMode, Tba $tba)
    {
        $valid = false;
        
        $roles = session()->get('roles');
        
        switch ($tbaEvalEventMode->identity) {
            case IdentityType::Expert:
                $valid = in_array(RoleType::Expert, $roles);
                break;
            case IdentityType::Visitor:
                $valid = in_array(RoleType::Teacher, $roles);
                break;
            case IdentityType::Teacher:
                $valid = $user->id === $tba->user_id;
                break;
            case IdentityType::User:
                $valid = true;
                break;
            default:
                return false;
        }
        
        return $valid;
    }

    /**
     * Determine whether the user can update the tba eval event.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\TbaEvaluateEvent  $tbaEvalEvent
     * @return mixed
     */
    public function update(User $user, TbaEvaluateEvent $tbaEvalEvent)
    {
        return (is_null($tbaEvalEvent->user_id))
            ? $tbaEvalEvent->tbaEvaluateUser->user_id === $user->id
            : $tbaEvalEvent->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the tba eval event.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\TbaEvaluateEvent  $tbaEvalEvent
     * @return mixed
     */
    public function delete(User $user, TbaEvaluateEvent $tbaEvalEvent)
    {
        return (is_null($tbaEvalEvent->user_id))
            ? $tbaEvalEvent->tbaEvaluateUser->user_id === $user->id
            : $tbaEvalEvent->user_id === $user->id;
    }
    
    /**
     * Determine whether the user can edit(update and delete) the tba eval event.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\TbaEvaluateEvent  $tbaEvalEvent
     * @return mixed
     */
    public function edit(User $user, TbaEvaluateEvent $tbaEvalEvent)
    {
        return $this->update($user, $tbaEvalEvent) && $this->delete($user, $tbaEvalEvent);
    }
    
    /**
     * Determine whether the user can create tba eval events.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\TbaEvaluateEventMode  $tbaEvalEventMode
     * @param  \App\Models\Tba  $tba
     * @param  int $groupId
     * @return mixed
     */
    public function createInGroup(User $user, TbaEvaluateEventMode $tbaEvalEventMode, Tba $tba, $groupId)
    {
        $valid = false;
        
        $roles = session()->get('roles');
        $user  = User::query()->where('id', $user->id)->first();
        $groupUser = Group::query()->where('status', 1)->find($groupId)->users()->where('member_status', 1)->find($user->id);
        switch ($tbaEvalEventMode->identity) {
            case IdentityType::Expert:
                if (is_null($groupUser)) {
                    $designatedVideoInfo = DesignatedVideo::query()->where(['group_id' => $groupId, 'tba_id' => $tba->id, 'team_model_id' => $user->habook])->first();
                    $hasExpertCommentAuth = (empty($designatedVideoInfo) ? false : $designatedVideoInfo->comment === 1);
                    $valid = $hasExpertCommentAuth;
                } else {
                    $valid = (in_array(RoleType::Expert, $roles) || in_array($groupUser->pivot->member_duty, [DutyType::Expert, DutyType::Admin]));
                }
                break;
            case IdentityType::Visitor:
                $valid = in_array($groupUser->pivot->member_duty, [DutyType::General]);
                break;
            case IdentityType::Teacher:
                $valid = $user->id === $tba->user_id;
                break;
            case IdentityType::User:
                $valid = true;
                break;
            default:
                return false;
        }
        
        return $valid;
    }
    
}
