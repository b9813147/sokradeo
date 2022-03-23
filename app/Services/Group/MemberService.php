<?php

namespace App\Services\Group;

use App\Repositories\GroupRepository;
use App\Repositories\UserRepository;

class MemberService
{
    private $groupRepo = null;
    
    //
    public function __construct(GroupRepository $groupRepo)
    {
        $this->groupRepo = $groupRepo;
    }
    
    //
    public function list($groupId, $conds, $page)
    {
        $paginator = $this->groupRepo->members($groupId, $conds, $page);
        $paginator->getCollection()->transform(function ($v) {
            return $this->toMember($v);
        });
        return $paginator;
    }
    
    //
    public function candidates($groupId, $conds, $page)
    {
        $paginator = $this->groupRepo->candidates($groupId, $conds, $page);
        $paginator->getCollection()->transform(function ($v) {
            return $this->toMember($v);
        });
        return $paginator;
    }
    
    //
    public function getMember($groupId, $userId)
    {
        $user = $this->groupRepo->getMember($groupId, $userId);
        return $this->toMember($user);
    }
    
    //
    public function setMember($groupId, $userId, $member)
    {
        return $this->groupRepo->setMember($groupId, $userId, $member);
    }
    
    //
    public function createMember($groupId, $userId, $member)
    {
        $user = $this->groupRepo->createMember($groupId, $userId, $member);
        return $this->toMember($user);
    }
    
    //
    private function toMember(& $user)
    {
        $user->member_status = is_null($user->pivot) ? null : $user->pivot->member_status;
        $user->member_duty   = is_null($user->pivot) ? null : $user->pivot->member_duty;
        unset($user->pivot);
        return $user;
    }
    
    //
    /*
    private function transformMemberToUser(& $member)
    {
        $member = collect($member);
        
        if($member->has('user_id')) {
            $v = $member->get('user_id');
            $member->put('id', $v);
            $member->pull('user_id');
        }
        
        if($member->has('status')) {
            $v = $member->get('status');
            $member->put('member_status', $v);
            $member->pull('status');
        }
        
        if($member->has('duty')) {
            $v = $member->get('duty');
            $member->put('member_duty', $v);
            $member->pull('duty');
        }
        
        return $member->toArray();
    }
    */
    /**
     * @param $groupId
     * @param $teamModelId
     * @return array
     */
    public function isMember($groupId, $teamModelId)
    {
        $memberInfo = $this->groupRepo->isMember($groupId, $teamModelId);

        return $memberInfo;

    }
    /**
     * @param $groupId
     * @param $teamModelId
     * @return bool
     */
    public function joinMemberGroup($groupId, $teamModelId)
    {
        return $this->groupRepo->joinMemberGroup($groupId, $teamModelId);
    }
}
