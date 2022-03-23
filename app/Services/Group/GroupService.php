<?php

namespace App\Services\Group;

use App\Repositories\GroupRepository;
use App\Services\BaseService;

class GroupService extends BaseService
{
    protected $repository;

    //
    public function __construct(GroupRepository $groupRepo)
    {
        $this->repository = $groupRepo;
    }

    //
    public function list($userId, $page)
    {
        return $this->repository->listByUserId($userId, $page);
    }

    //
    public function getGroups($userId)
    {
        return $this->repository->getGroupsByUserId($userId);
    }

    //
    public function getGroup($groupId)
    {
        return $this->repository->getGroup($groupId);
    }

    //
    public function getGroupBySchoolCode($schoolCode)
    {
        return $this->repository->getGroupBySchoolCode($schoolCode);
    }

    public function getSchoolCodeByGroup($school_code)
    {
        return $this->repository->schoolCodeByGroup($school_code);
    }

    /**
     * Get groupData by abbrv
     * @param string $abbrv
     * @return Object|null
     */
    public function getGroupByAbbrv(string $abbrv)
    {
        return $this->repository->getGroupByAbbrv($abbrv);
    }

    /**
     * Join group as member by user id
     * @param $groupId
     * @param $userId
     * @return Object|null
     */
    public function joinGroupAsMemberByUserId($groupId, $userId)
    {
        return $this->repository->joinGroupAsMemberByUserId($groupId, $userId);
    }
}
