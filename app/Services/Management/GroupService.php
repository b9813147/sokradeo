<?php

namespace App\Services\Management;

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
    public function list($page)
    {
        return $this->repository->list($page);
    }

    //
    public function getGroup($groupId)
    {
        return $this->repository->getGroup($groupId);
    }

    //
    public function setGroup($groupId, $groupData, $admins)
    {
        return $this->repository->setGroup($groupId, $groupData, $admins);
    }

    //
    public function createGroup($group, $admins)
    {
        return $this->repository->createGroup($group, $admins);
    }

    /**
     * 開啟審核機制或關閉 1 open \ 0 close
     *
     * @param $groupId
     * @param $reviewStatus
     * @return bool
     */
    public function setGroupReviewStatus($groupId, $reviewStatus)
    {
        return $this->repository->setGroupReviewStatus($groupId, $reviewStatus);
    }
}
