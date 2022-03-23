<?php

namespace App\Http\Controllers\Api\V1\Groups;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
use App\Http\Resources\Api\V1\GroupChannelResource;
use App\Services\Group\ChannelService;
use App\Services\Group\GroupService;

class ChannelController extends Controller
{
    private $groupSrv   = null;
    private $channelSrv = null;
    
    //
    public function __construct(GroupService $groupSrv, ChannelService $channelSrv)
    {
        $this->module = ['cate' => 'Groups', 'app' => 'Channel'];
        $this->groupSrv   = $groupSrv;
        $this->channelSrv = $channelSrv;
    }
    
    //
    public function index(Request $req)
    {
        $userId  = $req->user()->id;
        $groupId = $req->groupId;
        
        $group = $this->groupSrv->getGroup($groupId);
        
        $this->authorize('view', $group);
        
        $channels = $this->channelSrv->getChannels($groupId)->transform(function ($v) {
            return new GroupChannelResource($v);
        });
        
        return $this->success($channels);
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