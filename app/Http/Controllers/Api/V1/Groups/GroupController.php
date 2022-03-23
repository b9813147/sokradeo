<?php

namespace App\Http\Controllers\Api\V1\Groups;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Api\Controller;
use App\Http\Resources\Api\V1\GroupResource;
use App\Http\Resources\Api\V1\GroupChannelResource;
use App\Services\Group\GroupService;
use App\Services\Group\ChannelService;

class GroupController extends Controller
{
    private $groupSrv    = null;
    private $channelsSrv = null;

    //
    public function __construct(GroupService $groupSrv, ChannelService $channelsSrv)
    {
        $this->module      = ['cate' => 'Groups', 'app' => 'Group'];
        $this->groupSrv    = $groupSrv;
        $this->channelsSrv = $channelsSrv;
    }

    //
    public function index(Request $req)
    {
        $userId = $req->user()->id;

        $groups = $this->groupSrv->getGroups($userId)->transform(function ($v) {
            return new GroupResource($v);
        });

        foreach ($groups as $index => $group) {
            $channels                   = $this->channelsSrv->getChannels($group['id'])->transform(function ($v) {
                return new GroupChannelResource($v);
            });
            $groups[$index]['channels'] = $channels;
        }

        return $this->success($groups);
    }

    //
    public function getSchoolGroup(Request $req)
    {
        $schoolCode = $req->schoolCode;
        try {
            $group             = $this->groupSrv->getGroupBySchoolCode($schoolCode);
            $channels          = $this->channelsSrv->getChannels($group['id'])->transform(function ($v) {
                return new GroupChannelResource($v);
            });
            $group['channels'] = $channels;
        } catch (ModelNotFoundException $e) {
            $group = null;
        }


        return $this->success($group);
    }

    //
    public function store(Request $req)
    {

        return $this->success(['message' => 'store']);

    }

    //
    public function show()
    {

        return $this->success(['message' => 'show']);

    }

    //
    public function update()
    {

        return $this->success(['message' => 'update']);

    }

    //
    public function destroy()
    {

        return $this->success(['message' => 'destroy']);

    }
}