<?php

namespace App\Http\Controllers\Group;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Controller;
use App\Services\Group\ChannelService;
use App\Services\Group\GroupService;
use App\Services\Group\MemberService;
use App\Services\Management\RoleService;
use App\Types\Cms\CmsType;
use App\Types\Group\DutyType;
use App\Repositories\GroupRepository;

class ManageController extends Controller
{
    private $channelSrv = null;
    private $groupSrv   = null;
    private $memberSrv  = null;
    
    //
    public function __construct(ChannelService $channelSrv, GroupService $groupSrv, MemberService $memberSrv)
    {
        $this->module = ['cate' => 'Group', 'app' => 'Manage'];
        // $this->permitModule($this->module);
        $this->channelSrv = $channelSrv;
        $this->groupSrv   = $groupSrv;
        $this->memberSrv  = $memberSrv;
    }
    
    //
    public function index(Request $req, RoleService $roleSrv)
    {
        $groupId = $req->groupId;
        
        $group    = $this->getGroup($groupId);
        $channels = $this->channelSrv->getChannels($groupId)->all();
        $roles    = $roleSrv->getRoles();
        $cmses    = CmsType::list();
        $duties   = DutyType::list();

        $modulePath = $this->parseModulePath($this->module);
        
        $data = [
                'module'  => $modulePath,
                'globals' => [
                        'group'  => [
                                'id'       => $group->id,
                                'channels' => $channels,
                        ],
                        'roles'  => $roles,
                        'cmses'  => $cmses,
                        'duties' => $duties,
                ],
        ];
        
        return view($modulePath.'/index', $data);
    }
    
    /*
     * member
     * */
    
    //
    public function members(Request $req)
    {
        $groupId = $req->groupId;
        $conds   = $req->only(['member_status', 'member_duty']);
        $page    = $req->input('page',  1);
        
        $group  = $this->getGroup($groupId);
        $result = $this->memberSrv->list($group->id, $conds, $page);

        return Response::json([
                'status' => true,
                'data'   => $result
        ]);
    }
    
    //
    public function candidates(Request $req)
    {
        $groupId = $req->groupId;
        $conds   = $req->only(['member_status', 'member_duty']);
        $page    = $req->input('page', 1);
        
        $group  = $this->getGroup($groupId);
        $result = $this->memberSrv->candidates($group->id, $conds, $page);
        
        return Response::json([
                'status' => true,
                'data'   => $result
        ]);
    }
    
    //
    public function getMember(Request $req)
    {
        $groupId = $req->groupId;
        $userId  = $req->userId;
        
        $group  = $this->getGroup($groupId);
        $result = $this->memberSrv->getMember($group->id, $userId);
        
        return Response::json([
                'status' => true,
                'data'   => $result
        ]);
    }
    
    //
    public function setMember(Request $req)
    {
        $groupId = $req->groupId;
        
        if (empty($groupId) || !$req->filled(['userId'])) {
            return Response::json([
                    'status' => false
            ]);
        }
        
        $userId = $req->userId;
        $member = $req->only(['member_status', 'member_duty']);
        
        $group = $this->getGroup($groupId);
        $this->memberSrv->setMember($group->id, $userId, $member);
        $result = $this->memberSrv->getMember($group->id, $userId);
        
        return Response::json([
                'status' => true,
                'data'   => $result
        ]);
    }


    //
    public function setMemberAsExpert(Request $req)
    {
        $userId     = auth()->id();
        $schoolCode = $req->schoolCode;

        if (!isset($schoolCode)) {
            return 'schoolCode is undefined';
        }

        try {
            $group = (new GroupRepository())->getGroupBySchoolCode($schoolCode);
        } catch (ModelNotFoundException $e) {
            return 'schoolCode is invalid';
        }

        try {
            $memberInfo = $this->memberSrv->getMember($group->id, $userId);
            if ($memberInfo->member_duty !== DutyType::Expert && $memberInfo->member_duty !== DutyType::Admin) {
                $member = array(
                    'member_status' => 1,
                    'member_duty'   => DutyType::Expert
                );
                $this->memberSrv->setMember($group->id,  $userId, $member);
            }
        } catch (ModelNotFoundException $e) {
            $member = array(
                'member_status' => 1,
                'member_duty'   => DutyType::Expert
            );
            $this->memberSrv->createMember($group->id,  $userId, $member);
        }

        return redirect(Config::get('srvs.teammodel.sokrates.url'));
    }
    
    //
    public function createMember(Request $req) // 待修改
    {
        $groupId = $req->groupId;

        $group = $this->getGroup($groupId);

        return Response::json([
                'status' => false
        ]);
    }

    /**
     * @Description 驗證使用者存不存在
     * @return \Illuminate\Http\JsonResponse
     */
    public function isMember(Request $request)
    {

        $result = $this->memberSrv->isMember($request->groupId, $request->teamModelId);

        return Response::json([
            'status' => true,
            'data'   => $result
        ]);
    }

    public function joinMemberGroup(Request $request)
    {

        $request = $this->memberSrv->joinMemberGroup($request->groupId,$request->teamModelId);

        return Response::json([
            'status'=>true,
            'data'=> $request,
        ]);
    }

    /**
     * channel
     */
    public function channels(Request $req)
    {
        $groupId = $req->groupId;
        $conds   = []; // 待修改
        $page    = $req->input('page', 1);
        
        $group  = $this->getGroup($groupId);
        $result = $this->channelSrv->list($group->id, $conds, $page);
        
        return Response::json([
                'status' => true,
                'data'   => $result
        ]);
    }
    
    //
    public function getChannel(Request $req)
    {
        $groupId   = $req->groupId;
        $channelId = $req->channelId;
        
        $group  = $this->getGroup($groupId);
        $result = $this->channelSrv->getChannel($group->id, $channelId);
        
        return Response::json([
                'status' => true,
                'data'   => $result
        ]);
    }
    
    //
    public function setChannel(Request $req)
    {
        if(! $req->filled(['group_id', 'id'])) {
            return Response::json([
                    'status' => false
            ]);
        }
        
        $groupId     = $req->group_id;
        $channelId   = $req->id;
        $channelData = $req->only(['status', 'public']);
        
        $group = $this->getGroup($groupId);
        $this->channelSrv->setChannel($group->id, $channelId, $channelData);
        $result = $this->channelSrv->getChannel($group->id, $channelId);
        
        return Response::json([
                'status' => true,
                'data'   => $result
        ]);
    }
    
    //
    public function createChannel(Request $req)
    {
        $params = ['cms_type', 'name', 'description', 'status', 'public'];
        
        $groupId = $req->groupId;
        
        if (empty($groupId) || !$req->filled($params)) {
            return Response::json([
                    'status' => false
            ]);
        }
        
        $channel = $req->only($params);
        
        $group  = $this->getGroup($groupId);
        $result = $this->channelSrv->createChannel($group->id, $channel);
        
        return Response::json([
                'status' => true,
                'data'   => $result
        ]);
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
        
        $group  = $this->getGroup($groupId);
        $result = $this->channelSrv->contents($group->id, $channelId, $conds, $page);
        
        return Response::json([
                'status' => true,
                'data'   => $result
        ]);
    }
    
    //
    public function getChannelContent(Request $req)
    {
        $groupId   = $req->groupId;
        $channelId = $req->channelId;
        $contentId = $req->contentId;
        
        $group  = $this->getGroup($groupId);
        $result = $this->channelSrv->getContent($group->id, $channelId, $contentId);

        return Response::json([
                'status' => true,
                'data'   => $result
        ]);
    }
    
    //
    public function setChannelContent(Request $req)
    {
        $groupId   = $req->groupId;
        $channelId = $req->channelId;

        if(empty($groupId) || empty($channelId) || !$req->filled(['contentId'])) {
            return Response::json([
                    'status' => false
            ]);
        }
        
        $contentId   = $req->contentId;
        $contentData = $req->only(['content_status']);

        $group = $this->getGroup($groupId);
        $this->channelSrv->setContent($group->id, $channelId, $contentId, $contentData);
        $result = $this->channelSrv->getContent($group->id, $channelId, $contentId);

        return Response::json([
                'status' => true,
                'data'   => $result
        ]);
    }

    //
    private function getGroup($groupId)
    {
        $group = $this->groupSrv->getGroup($groupId);
        $this->authorize('manage', $group);
        return $group;
    }
    
}
