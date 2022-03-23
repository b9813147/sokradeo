<?php

namespace App\Repositories;

use App\Models\GroupUser;
use LogicException;
use App\Models\Group;
use App\Models\GroupChannel;
use App\Models\User;
use App\Types\Group\DutyType;

class GroupRepository extends BaseRepository
{
    protected $model;

    /**
     * GroupRepository constructor.
     * @param Group $group
     */
    public function __construct(Group $group)
    {
        $this->model = $group;
    }

    /*
     * group
     * */

    //
    public function list($page = 1)
    {
        return Group::paginate(null, ['*'], 'page', $page);
    }

    //
    public function listByUserId($userId, $page = 1)
    {
        return User::findOrFail($userId)->groups()->paginate(null, ['*'], 'page', $page);
    }

    //
    public function getGroups()
    {
        return Group::get();
    }

    //
    public function getGroupsByUserId($userId)
    {
        return User::findOrFail($userId)->groups()->where('public', 0)->get();
    }

    //
    public function getGroup($groupId)
    {
        return Group::with([
            'users' => function ($query) {
                $query->where('member_duty', 'Admin');
            }
        ])->findOrFail($groupId);
    }

    //
    public function getGroupBySchoolCode($schoolCode)
    {
        return Group::where('school_code', $schoolCode)->with([
            'users' => function ($query) {
                $query->where('member_duty', 'Admin');
            }
        ])->firstOrFail();
    }

    /**
     * Get Group by Abbrv
     * @param string $abbrv
     * @return Group|null
     */
    public function getGroupByAbbrv(string $abbrv)
    {
        return Group::where('abbr', $abbrv)->first();
    }

    //
    public function setGroup($groupId, $groupData, $admins)
    {
        $group = Group::findOrFail($groupId);
        $group->fill($groupData);
        $group->save();

        $oriAdmins = $group->users()->where('member_duty', 'Admin')->get();

        $users = [];
        foreach ($oriAdmins as $user) {
            $users[$user->id] = ['member_duty' => ''];
        }
        foreach ($admins as $userId) {
            $users[$userId] = ['member_status' => 1, 'member_duty' => 'Admin'];
        }
        $group->users()->syncWithoutDetaching($users);
    }

    //
    public function createGroup($group, $admins)
    {
        if (Group::where('name', $group['name'])->exists()) {
            throw new LogicException('name of group is already exist');
        }
        $group = Group::create($group);

        $users = [];
        foreach ($admins as $v) {
            $users[$v] = ['member_status' => 1, 'member_duty' => 'Admin'];
        }
        $group->users()->attach($users);
        return Group::with('users')->findOrFail($group->id);
    }

    /*
     * member
     * */

    //
    public function members($groupId, $conds = [], $page = 1)
    {
        return Group::findOrFail($groupId)->users()->where($conds)->paginate(null, ['*'], 'page', $page);
    }

    //
    public function candidates($groupId, $conds = [], $page = 1)
    {
        $members = Group::findOrFail($groupId)->users()->where($conds)->select('id')->get();
        return User::where($conds)->whereNotIn('id', $members)->paginate(null, ['*'], 'page', $page);
    }

    //
    public function getMember($groupId, $userId)
    {
        return Group::findOrFail($groupId)->users()->findOrFail($userId);
    }

    //
    public function setMember($groupId, $userId, $member)
    {
        Group::findOrFail($groupId)->users()->updateExistingPivot($userId, $member);
    }

    //
    public function createMember($groupId, $userId, $member)
    {
        Group::findOrFail($groupId)->users()->syncWithoutDetaching([$userId => $member]);
        return $this->getMember($groupId, $userId);
    }

    public function getDutiesByUserIdAndGroupIds(int $userId, array $groupIds)
    {
        return GroupUser::query()
            ->select('group_id', 'member_duty')
            ->where('user_id', $userId)
            ->whereIn('group_id', $groupIds)
            ->get();
    }

    /*
     * channel
     * */

    //
    public function channels($groupId, $conds = [], $page = 1)
    {
        return Group::findOrFail($groupId)->channels()->where($conds)->paginate(null, ['*'], 'page', $page);
    }

    //
    public function getChannels($groupId)
    {
        return Group::findOrFail($groupId)->channels()->get();
    }

    //
    public function totalChannel($groupId)
    {
        return Group::findOrFail($groupId)->channels()->count();
    }

    //
    public function getChannel($groupId, $channelId)
    {
        return Group::findOrFail($groupId)->channels()->findOrFail($channelId);
    }

    //
    public function setChannel($groupId, $channelId, $channelData)
    {
        $channel = Group::findOrFail($groupId)->channels()->findOrFail($channelId);
        $channel->fill($channelData);
        $channel->save();
    }

    //
    public function createChannel($groupId, $channel)
    {
        $channel['group_id'] = $groupId;
        $channel             = new GroupChannel($channel);
        Group::findOrFail($groupId)->channels()->save($channel);
    }

    /*
     * channel cms: video
     * */

    //
    public function channelVideos($groupId, $channelId, $conds, $page)
    {
        return Group::findOrFail($groupId)->channels()->findOrFail($channelId)->videos()->where($conds)->paginate(null, ['*'], 'page', $page);
    }

    /**
     * @param $groupId
     * @param $teamModelId
     * @return array
     */
    public function isMember($groupId, $teamModelId)
    {
        $isTeamModelId = User::query()->select('habook')->where('habook', '!=', null)->where('habook', $teamModelId)->exists();
        $userId        = User::query()->select('id', 'habook')->where('habook', $teamModelId)->value('id');
        $isGroupUser   = GroupUser::query()->select('user_id', 'group_id')->where(['user_id' => $userId, 'group_id' => $groupId])->exists();

        $data = [
            'isTeamModelId' => $isTeamModelId,
            'isGroupUser'   => $isGroupUser
        ];
        return $data;
    }

    /**
     * @param $groupId
     * @param $teamModelId
     * @return bool
     */
    public function joinMemberGroup($groupId, $teamModelId)
    {

        $userId = User::query()->select('id', 'habook')->where('habook', $teamModelId)->value('id');

        GroupUser::query()->create([
            'group_id'      => $groupId,
            'user_id'       => $userId,
            'member_status' => 1,
            'member_duty'   => DutyType::General,
        ]);
        return true;
    }

    /**
     * Join group as member by user id
     * @param int $groupId
     * @param int $userId
     * @return GroupUser
     */
    public function joinGroupAsMemberByUserId(int $groupId, int $userId)
    {
        $groupUser = GroupUser::query()->updateOrCreate([
            'group_id' => $groupId,
            'user_id' => $userId,
            'member_status' => 1,
            'member_duty' => DutyType::General,
        ]);

        return $groupUser;
    }

    /**
     * 開啟審核機制或關閉 1 open \ 0 close
     *
     * @param $reviewStatus
     * @param $groupId
     * @return bool
     */
    public function setGroupReviewStatus($groupId, $reviewStatus)
    {
        return Group::query()->where('id', $groupId)->update([
            'review_status' => $reviewStatus
        ]);
    }

    public function schoolCodeByGroup($school_code)
    {
        return Group::where('school_code', $school_code)->first();
    }
}
