<?php

namespace App\Services\App;

use App\Helpers\Custom\GlobalPlatform;
use App\Repositories\UserRepository;
use App\Services\BbLicenseService;
use App\Services\BaseService;
use App\Services\Group\GroupService;

use App\Types\App\DefaultChannelType;

class UserService extends BaseService
{
    protected $repository;

    protected $bbLicenseService;
    protected $groupService;

    //
    public function __construct(UserRepository $userRepo, BbLicenseService $bbLicenseService, GroupService $groupService)
    {
        $this->repository = $userRepo;
        $this->bbLicenseService = $bbLicenseService;
        $this->groupService = $groupService;
    }

    //
    public function getUser($userId)
    {
        return $this->repository->getUser($userId);
    }

    /**
     * Get User Info
     * @param $userId
     */
    public function getUserInfo($userId)
    {
        return $this->repository->getUserInfo($userId);
    }

    /**
     * 使用者加入頻道
     * @param int $user_id
     * @param array $attributes
     * @param int $group_id
     * @return bool
     */
    public function userJoinForGroup(int $user_id, int $group_id, array $attributes): bool
    {
        return $this->repository->userJoinForGroup($user_id, $group_id, $attributes);
    }

    /**
     * Check if user is allowed to operate Obsrv Class
     * @param int $userId
     * @return bool
     */
    public function isAllowedToOperateObsrvClass(int $userId)
    {
        $isAllowed = false;
        if (!$userId)
            return $isAllowed;

        $userInfo = $this->getUser($userId);
        if (!$userInfo)
            return $isAllowed;

        $userGroupId = $userInfo->group_channel_id
            ? GlobalPlatform::convertChannelIdToGroupId($userInfo->group_channel_id)
            : null;
        if (!$userGroupId)
            return $isAllowed;

        // Get User Obsrv BB license
        $obsrvClassBbLicense = $this->bbLicenseService->getObsrvClassBbLicense();
        if (!$obsrvClassBbLicense)
            return $isAllowed;

        $userBbLicense = $userInfo->groups()
            ->where('id', $userGroupId)
            ->first()
            ->bbLicenses;
        $userObsrvBbLicense = $userBbLicense->filter(function ($item) use ($obsrvClassBbLicense) {
            return $item->id == $obsrvClassBbLicense->id;
        });

        // Verify if user's obsrv bb license is valid
        // status = 1 AND deadline is not met: valid
        if (
            $userObsrvBbLicense->count()
            && $userObsrvBbLicense->first()->pivot
            && $userObsrvBbLicense->first()->pivot->status == 1
            && $userObsrvBbLicense->first()->pivot->deadline >= date('Y-m-d')
        )
            $isAllowed = true;

        return $isAllowed;
    }

    /**
     * Setup Default Channel for User
     * @param int $userId
     * @return bool
     */
    public function setupDefaultChannelForUser(int $userId)
    {
        $isSuccessful = false;
        try {
            // Check userId
            if (!$userId)
                throw new \Exception('User ID is required.');

            // Get station code and language code
            $stationCode = GlobalPlatform::getCurrentStation();
            $lang = new \App\Libraries\Lang\Lang();
            $languageCode = $lang->getBrowserLang();

            // Get Default groupSchoolAbbrv
            $groupSchoolAbbrv = DefaultChannelType::getGroupSchoolAbbrv($stationCode, $languageCode);
            if (!$groupSchoolAbbrv)
                throw new \Exception('Default groupSchoolAbbrv not found');

            // Get groupId and channelId from groupSchoolAbbrv
            $groupData = $this->groupService->getGroupByAbbrv($groupSchoolAbbrv);
            if (!$groupData)
                throw new \Exception('Group not found');

            // Set Default Channel and Create Group Member for User
            $defaultGroupId = $groupData->id;
            $groupUserData = $this->groupService->joinGroupAsMemberByUserId($defaultGroupId, $userId);
            if (!$groupUserData)
                throw new \Exception('Group User not found');

            $defaultChannelId = GlobalPlatform::convertGroupIdToChannelId($defaultGroupId);
            $userData = $this->repository->setUserGroupChannelId($userId, $defaultChannelId);
            if (!$userData)
                throw new \Exception('User not found');

            $isSuccessful = true;
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        }

        return $isSuccessful;
    }
}
