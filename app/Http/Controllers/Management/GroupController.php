<?php

namespace App\Http\Controllers\Management;

use App\Enums\NotificationMessageType;
use App\Helpers\CoreService\CoreServiceApi;
use App\Helpers\Custom\GlobalPlatform;
use App\Http\Controllers\Api\Controller;
use App\Notifications\EventChannel;
use App\Services\App\UserService;
use App\Services\Library\BbService;
use App\Types\Group\DutyType;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use App\Services\Management\GroupService;

class GroupController extends Controller
{
    use  CoreServiceApi;

    private $groupSrv = null;

    /**
     * @var UserService
     */
    protected $userService;
    /**
     * @var BbService
     */
    protected $bbService;

    public function __construct(GroupService $groupSrv, UserService $userService, BbService $bbService)
    {
        $this->module = ['cate' => 'Management', 'app' => 'Group'];
//        $this->permitModule($this->module);
        $this->groupSrv    = $groupSrv;
        $this->userService = $userService;
        $this->bbService   = $bbService;
    }

    //
    public function index()
    {
        $modulePath = $this->parseModulePath($this->module, 'index');

        $data = [
            'module' => $modulePath
        ];

        return view($modulePath, $data);
    }

    //
    public function info()
    {
        $modulePath = $this->parseModulePath($this->module, 'info');

        $data = [
            'module' => $modulePath
        ];

        return view($modulePath, $data);
    }

    //
    public function manage()
    {
        $modulePath = $this->parseModulePath($this->module, 'manage');

        $data = [
            'module' => $modulePath
        ];

        return view($modulePath, $data);
    }

    //
    public function list(Request $req)
    {
        $page = $req->input('page', 1);

        $result = $this->groupSrv->list($page);

        return Response::json([
            'status' => true,
            'data'   => $result
        ]);
    }

    //
    public function getGroup(Request $req)
    {
        $groupId = $req->groupId;

        $result = $this->groupSrv->getGroup($groupId);

        return Response::json([
            'status' => true,
            'data'   => $result
        ]);
    }

    //
    public function setGroup(Request $req)
    {
        if (!$req->filled('id')) {
            return Response::json([
                'status' => false
            ]);
        }

        $groupId   = $req->id;
        $groupData = $req->only(['school_code', 'name', 'description', 'status', 'public']);
        $admins    = $req->input('admins');

        $this->groupSrv->setGroup($groupId, $groupData, $admins);
        $result = $this->groupSrv->getGroup($groupId);

        return Response::json([
            'status' => true,
            'data'   => $result
        ]);
    }

    //
    public function createGroup(Request $req)
    {
        $params = ['school_code', 'name', 'description', 'status', 'public', 'admins'];

        if (!$req->filled($params)) {
            return Response::json([
                'status' => false
            ]);
        }

        $group  = $req->only(['school_code', 'name', 'description', 'status', 'public']);
        $admins = $req->input('admins', []);

        $result = $this->groupSrv->createGroup($group, $admins);

        return Response::json([
            'status' => true,
            'data'   => $result
        ]);
    }

    //
    public function searchUsers(Request $req, UserService $userSrv)
    {
        $name = $req->input('name', false);

        $result = $userSrv->searchUsers($name);

        return Response::json([
            'status' => true,
            'data'   => $result
        ]);
    }

    /**
     * ????????????????????????
     *
     * @param Request $request
     * @return mixed
     */
    public function setGroupReviewStatus(Request $request)
    {
        $this->groupSrv->setGroupReviewStatus($request->id, $request->review_status);

        return Response::json([
            'status' => 200,
        ]);
    }

    /**
     * ?????????????????????
     * @param int $group_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getGroupUsers(int $group_id): \Illuminate\Http\JsonResponse
    {
        try {
            $user_ids = $this->groupSrv->find($group_id)->users()->pluck('id')->toArray();
            return Response::json([
                'user_list' => $user_ids,
                'message'   => 'success',
                'status'    => 1,
            ], 200);
        } catch (Exception $exception) {
            return Response::json([
                'message' => [
                    'fail',
                    $exception->getMessage(),
                ],
                'status'  => 0,
            ], 404);
        }
    }

    /**
     * ?????????????????????
     * @param int $group_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function userJoinForGroup(int $group_id): \Illuminate\Http\JsonResponse
    {
        try {
            $groupSrv       = $this->groupSrv->find($group_id);
            $maxParticipant = (int)json_decode($groupSrv->event_data)->maxParticipant;

            // Allow to join by default if maxParticipant is set to 0
            if ($maxParticipant > 0 && $groupSrv->users()->where('member_duty', DutyType::General)->count() > $maxParticipant) {
                return Response::json([
                    'message' => [
                        'fail',
                        'Maximum number reached',
                    ],
                    'status'  => 0,
                ], 412);
            }

            $this->userService->userJoinForGroup(auth()->id(), $group_id, ['member_status' => 1, 'member_duty' => DutyType::General]);
            //????????????
            $group_id = auth()->user()->group_channel_id ? GlobalPlatform::convertChannelIdToGroupId(auth()->user()->group_channel_id) : null;
            $group    = auth()->user()->groups->firstWhere('id', $group_id);
            switch ($group->country_code) {
                case 886:
                    \App::setLocale('tw');
                    break;
                case 1:
                    \App::setLocale('en');
                    break;
                default:
                    \App::setLocale('cn');
            }

            // ??????????????????
            if ($groupSrv->public != 0) {
                // ??????????????????
                $event_data = json_decode($groupSrv->event_data);
                // ??????????????????
                if (!empty($event_data) && (bool)($event_data->enableTrial)) {
                    $schoolCode     = ($groupSrv->school_code . $groupSrv->name) ?? null;
                    $LicenseData    = [
                        "id"          => auth()->user()->habook,               //???????????????
                        "name"        => auth()->user()->name ?? auth()->user()->habook,                 //??????
                        "email"       => $userInfo->email ?? null,             //email
                        "productCode" => $event_data->productCode,             //????????????
                        "trialDay"    => $event_data->trialDeadline,           //??????????????????
                        "cqty"        => $event_data->cqty,                    //?????????
                        "schoolCode"  => $event_data->school_code ?? null,     //???????????? ????????????
                        "schoolName"  => $group->name ?? null,                 //???????????? ????????????
                        "ap"          => collect($event_data->ap)->where('val', true), //????????????
                    ];
                    $hiTeachMessage = json_encode([
                        'content' => $LicenseData,
                    ]);
                    $licence        = $this->bbService->getLicence($schoolCode, $LicenseData);
                    Log::info('License', [$licence]);
                    $licence->serial = $licence->serial ?? null;
                    // ????????????
                    auth()->user()->groups()->updateExistingPivot($groupSrv->id, ['user_data' => json_encode(['license' => $licence->serial])]);
                    $message = [
                        'channel_id'  => GlobalPlatform::convertGroupIdToChannelId($groupSrv->id),
                        'title'       => __('app/license.title'),
                        'content'     => __('app/license.content') . __('app/license.license') . $licence->serial . "\n\n" . __('app/license.expire') . $event_data->trialDeadline,
                        'isOperating' => false,
                        'top'         => false,
                    ];
                    auth()->user()->notify(new EventChannel($message));
                }
                Log::info('sendNotify', [
                    'status'   => GlobalPlatform::sendNotify($groupSrv->id, [auth()->user()->id], NotificationMessageType::Join),
                    'group_id' => $groupSrv->id, 'user_id' => auth()->user()->id
                ]);
                \Log::info('App Notify', ['status' => $this->sendNotify([auth()->user()->habook], $hiTeachMessage, $message['title'], $groupSrv->notify_status)]);
            }
        } catch (Exception $exception) {
            return $this->setStatus(404)->fail([
                'message' => 'fail',
                'status'  => 0,
            ]);
        }
        return $this->setStatus(201)->success([
            'message' => 'success',
            'status'  => 1,
        ]);
    }
}
