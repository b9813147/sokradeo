<?php

namespace App\Http\Controllers\Group;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Controller;
use App\Services\Group\ChannelService;
use App\Services\Group\GroupService;
use App\Services\Group\MemberService;
use App\Types\Group\DutyType;

class MainController extends Controller
{
    private $channelSrv = null;
    private $groupSrv   = null;
    private $memberSrv  = null;
    
    //
    public function __construct(ChannelService $channelSrv, GroupService $groupSrv, MemberService $memberSrv)
    {
        $this->module = ['cate' => 'Group', 'app' => 'Main'];
        $this->permitModule($this->module);
        $this->channelSrv = $channelSrv;
        $this->groupSrv   = $groupSrv;
        $this->memberSrv  = $memberSrv;
    }
    
    //
    public function index(Request $req)
    {
        $userId = auth()->id();
        
        $groupId = $req->groupId;
        
        $group = $this->groupSrv->getGroup($groupId);
        
        $this->authorize('view', $group);
        
        $channels = $this->channelSrv->getChannels($groupId)->all();
        $member   = $this->memberSrv->getMember($groupId, $userId);
        
        $modulePath = $this->parseModulePath($this->module);
        
        $data = [
                'module'  => $modulePath,
                'groupId' => $groupId,
                'managed' => DutyType::checkManagement($member->member_duty),
                'globals' => [
                        'group'  => [
                                'id'       => $group->id,
                                'channels' => $channels,
                        ],
                ],
        ];
        
        return view($modulePath.'/index', $data);
    }
    
    /*
     * channel content
     * */
    
    //
    public function channelContents(Request $req)
    {
        $groupId   = $req->groupId;
        $channelId = $req->channelId;
        $conds     = []; // 待修改
        $page      = $req->input('page', 1);
        
        $group  = $this->groupSrv->getGroup($groupId);
        $result = $this->channelSrv->contents($group->id, $channelId, $conds, $page);
        
        return Response::json([
                'status' => true,
                'data'   => $result
        ]);
    }
    
}
