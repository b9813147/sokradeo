<?php

namespace App\Policies;

use App\Models\DesignatedVideo;
use App\Models\Tba;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Group;
use App\Models\GroupChannel;
use App\Models\GroupChannelContent;
use App\Types\Group\DutyType;
use Illuminate\Auth\Access\HandlesAuthorization;

class GroupChannelContentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the groupChannelContent.
     *
     * @param \App\Models\User $user
     * @param \App\GroupChannelContent $groupChannelContent
     * @return mixed
     */

    private function check_group_auth($user, $groupChannelContent, $groupIds)
    {
        //使用者 user_id
        $user_id = $user->id;

        // 判斷身份是不是群組Expert
        $expert = $user->groups()->where('status', 1)->where('id', $groupIds)->where('member_duty', DutyType::Admin)->exists();

        if ($expert) {
            return true;
        }

        // 判斷身份是不是群組Admin
        $admin = $user->groups()->where('status', 1)->where('id', $groupIds)->where('member_duty', DutyType::Expert)->exists();

        if ($admin) {
            return true;
        }

        //判斷該影片是否是 owner
        $owner = DB::table('tbas')->where('id', $groupChannelContent->content_id)->where('user_id', $user_id)->exists();
        if ($owner) {
            return true;
        }

        //判斷是否有指定影片觀看權限
        $designatedVideoInfo = DesignatedVideo::query()->where(['group_id' => $groupChannelContent->group_id, 'tba_id' => $groupChannelContent->content_id, 'team_model_id' => $user->habook])->first();
        $hasViewAuth = (empty($designatedVideoInfo) ? false : $designatedVideoInfo->view === 1);
        if ($hasViewAuth) {
            return true;
        }

        return false;
    }

    public function view(User $user, GroupChannelContent $groupChannelContent)
    {
        if ($groupChannelContent->content_status !== 1 && $this->check_group_auth($user, $groupChannelContent, $groupChannelContent->group_id) !== true) {
            return false;
        }

        if (Group::findOrFail($groupChannelContent->group_id)->status !== 1) {
            return false;
        }

        if ($groupChannelContent->content_public === 1) {
            return true;
        }

        return $user->groups()->where('status', 1)->select('id')->get()->contains('id', $groupChannelContent->group_id);
    }

    /**
     * Determine whether the user can create groupChannelContents.
     *
     * @param \App\Models\User $user
     * @param int $channelId
     * @return mixed
     */
    public function create(User $user, $channelId)
    {
        $channel = GroupChannel::where('status', 1)->find($channelId);
        if (is_null($channel)) {
            return false;
        }
        return $user->groups()->where('status', 1)->where('id', $channel->group_id)->exists();
    }

    /**
     * Determine whether the user can update the groupChannelContent.
     *
     * @param \App\Models\User $user
     * @param \App\GroupChannelContent $groupChannelContent
     * @return mixed
     */
    public function update(User $user, GroupChannelContent $groupChannelContent)
    {
        return false; // 待修改
    }

    /**
     * Determine whether the user can delete the groupChannelContent.
     *
     * @param \App\Models\User $user
     * @param \App\GroupChannelContent $groupChannelContent
     * @return mixed
     */
    public function delete(User $user, GroupChannelContent $groupChannelContent)
    {
        return false; // 待修改
    }

    /**
     * Determine whether the user can view the groupChannelContent by content(id and type) and groupIds.
     * @param \App\Models\User $user
     * @param GroupChannelContent $groupChannelContent
     * @param array $groupIds
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function viewByGroupIds(User $user, GroupChannelContent $groupChannelContent, $groupIds): bool
    {

        if ($this->check_group_auth($user, $groupChannelContent, $groupIds) === true) {
            return true;
        }

        $valid = DB::table('group_channel_contents')
            ->where('group_channel_contents.content_id', $groupChannelContent->content_id)
            ->where('group_channel_contents.content_type', $groupChannelContent->content_type)
            ->where('group_channel_contents.content_status', 1)
            ->whereIn('group_channel_contents.content_public', [0, 1, 2])
            ->whereIn('group_channel_contents.group_id', $groupIds)
            ->join('groups', 'group_channel_contents.group_id', '=', 'groups.id')
//            ->join('group_user', 'groups.id', '=', 'group_user.group_id')
//            ->where('group_user.user_id', $user->id)
            ->where('groups.status', 1)
            ->exists();

        if ($valid) {
            return true;
        }

        $groupIds = $user->groups()->where('status', 1)->select('id')->get()->map(function ($v) {
            return $v->id;
        })->toArray();

        $this->deny(__('app/auth.no-permissions'));

        return GroupChannelContent::where([
            'content_id'     => $groupChannelContent->content_id,
            'content_type'   => $groupChannelContent->content_type,
            'content_status' => 1,
        ])->whereIn('group_id', $groupIds)->exists();
    }

    /**
     * Determine whether the user can view the groupChannelContent by content(id and type).
     *
     * @param \App\Models\User $user
     * @param \App\GroupChannelContent $groupChannelContent
     * @return mixed
     */
    public function viewByContentId(User $user, GroupChannelContent $groupChannelContent)
    {
        $groupIds = $user->groups()->where('status', 1)->select('id')->get()->map(function ($v) {
            return $v->id;
        })->toArray();

        $sql = '(group_channel_contents.content_public = 1' . (!empty($groupIds) ? ' OR group_channel_contents.group_id IN (' . implode(',', $groupIds) . ')' : '') . ')';
        return $query = DB::table('group_channel_contents')
            ->where('content_type', $groupChannelContent->content_type)
            ->where('content_id', $groupChannelContent->content_id)
            ->where('content_status', 1)
            ->whereRaw($sql)
            ->join('groups', 'group_channel_contents.group_id', '=', 'groups.id')
            ->where('groups.status', 1)
            ->exists();
    }

    //
    public function createWithSchoolCode(User $user, $schoolCode)
    {
        return $user->groups()->where('status', 1)->where('school_code', $schoolCode)->exists();
    }

}
