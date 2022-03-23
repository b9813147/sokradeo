<?php

namespace App\Services\Group\Content;

use App\Repositories\GroupRepository;
use App\Repositories\UserRepository;
use App\Repositories\DesignatedVideoRepository;
use App\Repositories\Tba\EvaluateEventModeRepository;
use App\Types\App\RoleType;
use App\Types\Group\DutyType;
use App\Types\Tba\IdentityType;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TbaService
{
    private $groupRepo = null;
    private $userRepo = null;
    private $designatedVideoRepo = null;

    //
    public function __construct(GroupRepository $groupRepo, UserRepository $userRepo, DesignatedVideoRepository $designatedVideoRepo)
    {
        $this->groupRepo           = $groupRepo;
        $this->userRepo            = $userRepo;
        $this->designatedVideoRepo = $designatedVideoRepo;
    }

    /**
     * @param  \App\Models\Tba $tba
     * @return \Illuminate\Support\Collection
     */
    public function getTbaIdentityEvalEventModesSet($tba, $groupId, $userId)
    {
        $roles = session()->get('roles');
        $user = $this->userRepo->getUser($userId);
        $evalEventModeRepo = new EvaluateEventModeRepository();
        $set = collect([]);

        $designatedInfo = $this->designatedVideoRepo->getDesignatedInfo($groupId, $tba->id, $user->habook);
        $hasExpertCommentAuth = (empty($designatedInfo) ? false : $designatedInfo->comment === 1);
        // 個人
        $set->push($evalEventModeRepo->getIdentityEventModes(IdentityType::User));
        // 授課
        if ($tba->playlisted === 0 && $userId === $tba->user_id) {
            $set->push($evalEventModeRepo->getIdentityEventModes(IdentityType::Teacher));
        }
        try {
            $groupUser = $this->groupRepo->getMember($groupId, $userId);
            $group = $this->groupRepo->getGroup($groupId);
            
            // 專家
            if (in_array(RoleType::Expert, $roles) || in_array($groupUser->pivot->member_duty, [DutyType::Expert, DutyType::Admin]) || $hasExpertCommentAuth) {
                $set->push($evalEventModeRepo->getIdentityEventModes(IdentityType::Expert));
            }
            // 成員
            if (in_array($groupUser->pivot->member_duty, [DutyType::General]) && $group->public_note_status === 1 && !$hasExpertCommentAuth) {
                $set->push($evalEventModeRepo->getIdentityEventModes(IdentityType::Visitor));
            }
        } catch (ModelNotFoundException $e) {
            // 專家
            if ($hasExpertCommentAuth) {
                $set->push($evalEventModeRepo->getIdentityEventModes(IdentityType::Expert));
            }
        }
        
        return $set;
    }
    
    public function checkGuestEvaluateEventAuth($tba, $groupId, $userId)
    {
        try {
            $groupUser = $this->groupRepo->getMember($groupId, $userId);
            // 管理員
            if (in_array($groupUser->pivot->member_duty, [DutyType::Admin])) {
                return true;
            }
        } catch (ModelNotFoundException $e) {
        }

        try {
            $user = $this->userRepo->getUser($userId);
            // 授課老師
            if ($tba->playlisted === 0 && $user->habook === $tba->habook_id) {
                return true;
            }
        } catch (ModelNotFoundException $e) {
        }

        // 上傳老師
        if ($tba->playlisted === 0 && $userId === $tba->user_id) {
            return true;
        }
        
        return false;
    }

}
