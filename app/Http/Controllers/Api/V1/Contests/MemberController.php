<?php

namespace App\Http\Controllers\Api\V1\Contests;

use App\Services\NotificationMessageService;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Api\Controller;
use App\Http\Transformers\Api\V1\ContestMemberTransformer;
use App\Repositories\UserRepository;
use App\Repositories\GroupRepository;
use App\Services\Group\MemberService;
use App\Services\Contest\ContestService;
use App\Types\Group\DutyType;
use App\Types\Auth\AccountType;

class MemberController extends Controller
{
    protected $memberSrv;
    protected $userRepo;
    protected $groupRepo;

    //
    /**
     * @var NotificationMessageService
     */
    protected $notificationMessageService;

    public function __construct(MemberService $memberSrv, UserRepository $userRepo, GroupRepository $groupRepo, NotificationMessageService $notificationMessageService)
    {
        $this->module                     = ['cate' => 'Contests', 'app' => 'Member'];
        $this->memberSrv                  = $memberSrv;
        $this->userRepo                   = $userRepo;
        $this->groupRepo                  = $groupRepo;
        $this->notificationMessageService = $notificationMessageService;
    }

    //
    public function index()
    {

        return $this->success(['message' => 'index']);

    }

    //
    public function store(Request $req)
    {
        $this->validate($req, [
            'userList' => 'required',
        ]);

        if ($req->input('separated') === true) {
            (new contestMemberTransformer($req))->execute();

            $userList = $req->input('userList');

            $result = [];

            foreach ($userList as $userInfo) {
                if (!isset($userInfo['schoolCode'])) {
                    return $this->success(['status' => 0, 'message' => 'schoolCode is undefined']);
                }
                try {
                    $group   = $this->groupRepo->getGroupBySchoolCode($userInfo['schoolCode']);
                    $channel = $this->groupRepo->getChannels($group->id);
                } catch (ModelNotFoundException $e) {
                    return $this->success(['status' => 0, 'message' => 'schoolCode error: ' . $userInfo['schoolCode']]);
                }
                $accId   = $userInfo['id'];
                $accData = [
                    'name'             => $userInfo['name'],
                    'email'            => $userInfo['email'],
                    'group_channel_id' => $channel[0]->id
                ];

                try {
                    $user = $this->userRepo->getUserByAcc(AccountType::Habook, $accId);
                    $this->userRepo->setUser($user->id, ['group_channel_id' => $channel[0]->id], []);
                    if (empty($user->toArray()['roles'])) {
                        $this->userRepo->setUser($user->id, [], [6]);
                    }
                } catch (ModelNotFoundException $e) {
                    $user = $this->userRepo->createUserByAcc(AccountType::Habook, $accId, $accData);
                    $this->userRepo->setUser($user->id, [], [6]);
                }

                try {
                    $res = $this->memberSrv->getMember($group->id, $user->id);
                } catch (ModelNotFoundException $e) {
                    $member = array(
                        'member_status' => 1,
                        'member_duty'   => DutyType::General
                    );
                    $res    = $this->memberSrv->createMember($group->id, $user->id, $member);
                }

                $result[] = $res;
            }

            return $this->success(['status' => 1, 'data' => $result]);

        } else {
            $this->validate($req, [
                'schoolCode' => 'required',
            ]);

            (new contestMemberTransformer($req))->execute();

            $userList   = $req->input('userList');
            $schoolCode = $req->input('schoolCode');

            try {
                $group   = $this->groupRepo->getGroupBySchoolCode($schoolCode);
                $channel = $this->groupRepo->getChannels($group->id);
            } catch (ModelNotFoundException $e) {
                return $this->success(['status' => 0, 'message' => 'schoolCode error']);
            }

            $result = [];

            foreach ($userList as $userInfo) {
                $accId   = $userInfo['id'];
                $accData = [
                    'name'             => $userInfo['name'],
                    'email'            => $userInfo['email'],
                    'group_channel_id' => $channel[0]->id
                ];

                try {
                    $user = $this->userRepo->getUserByAcc(AccountType::Habook, $accId);
                    $this->userRepo->setUser($user->id, ['group_channel_id' => $channel[0]->id], []);
                    if (empty($user->toArray()['roles'])) {
                        $this->userRepo->setUser($user->id, [], [6]);
                    }
                } catch (ModelNotFoundException $e) {
                    $user = $this->userRepo->createUserByAcc(AccountType::Habook, $accId, $accData);
                    $this->userRepo->setUser($user->id, [], [6]);
                }

                try {
                    $res = $this->memberSrv->getMember($group->id, $user->id);
                } catch (ModelNotFoundException $e) {
                    $member = array(
                        'member_status' => 1,
                        'member_duty'   => DutyType::General
                    );
                    $res    = $this->memberSrv->createMember($group->id, $user->id, $member);
                }

                $result[] = $res;
            }


            return $this->success(['status' => 1, 'data' => $result]);
        }
    }

    //'
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

    public function updateMemberChannel(Request $req)
    {
        $this->validate($req, [
            'userList' => 'required',
        ]);

        if ($req->input('separated') === true) {
            (new contestMemberTransformer($req))->execute();

            $userList = $req->input('userList');

            $result = [];
            foreach ($userList as $userInfo) {
                if (!isset($userInfo['schoolCode'])) {
                    continue;
                }
                try {
                    $group    = $this->groupRepo->getGroupBySchoolCode($userInfo['schoolCode']);
                    $channels = $this->groupRepo->getChannels($group->id);
                } catch (ModelNotFoundException $e) {
                    continue;
                }
                $accId = $userInfo['id'];

                try {
                    $user = $this->userRepo->getUserByAcc(AccountType::Habook, $accId);
                    $this->userRepo->setUser($user->id, ['group_channel_id' => $channels[0]->id], []);
                } catch (ModelNotFoundException $e) {
                    continue;
                }

                try {
                    $res = $this->memberSrv->getMember($group->id, $user->id);
                } catch (ModelNotFoundException $e) {
                    $member = array(
                        'member_status' => 1,
                        'member_duty'   => DutyType::General
                    );
                    $res    = $this->memberSrv->createMember($group->id, $user->id, $member);
                }
                $result[] = $res;
            }

            return $this->success(['status' => 1, 'data' => $result]);
        } else {
            $this->validate($req, [
                'schoolCode' => 'required',
            ]);

            (new contestMemberTransformer($req))->execute();

            $userList   = $req->input('userList');
            $schoolCode = $req->input('schoolCode');

            try {
                $group    = $this->groupRepo->getGroupBySchoolCode($schoolCode);
                $channels = $this->groupRepo->getChannels($group->id);
            } catch (ModelNotFoundException $e) {
                return $this->success(['status' => 0, 'message' => 'schoolCode error']);
            }

            $result = [];
            foreach ($userList as $userInfo) {
                $accId = $userInfo['id'];

                try {
                    $user = $this->userRepo->getUserByAcc(AccountType::Habook, $accId);
                    $this->userRepo->setUser($user->id, ['group_channel_id' => $channels[0]->id], []);
                } catch (ModelNotFoundException $e) {
                    continue;
                }

                try {
                    $res = $this->memberSrv->getMember($group->id, $user->id);
                } catch (ModelNotFoundException $e) {
                    $member = array(
                        'member_status' => 1,
                        'member_duty'   => DutyType::General
                    );
                    $res    = $this->memberSrv->createMember($group->id, $user->id, $member);
                }
                $result[] = $res;
            }

            return $this->success(['status' => 1, 'data' => $result]);
        }
    }

    //
    public function setGroupUserDuty(Request $req)
    {
        $this->validate($req, [
            'user'       => 'required',
            'schoolCode' => 'required',
        ]);

        (new contestMemberTransformer($req))->execute();

        $user       = $req->input('user');
        $schoolCode = $req->input('schoolCode');
        $duty       = $user['duty'];

        try {
            $group   = $this->groupRepo->getGroupBySchoolCode($schoolCode);
            $channel = $this->groupRepo->getChannels($group->id);
        } catch (ModelNotFoundException $e) {
            return $this->success(['status' => 0, 'message' => 'schoolCode error']);
        }

        $result = [];

        $accId   = $user['id'];
        $accData = [
            'name'  => $user['name'],
            'email' => $user['email'],
//            'group_channel_id' => $channel[0]->id
        ];

        try {
            $user = $this->userRepo->getUserByAcc(AccountType::Habook, $accId);
//            $this->userRepo->setUser($user->id, ['group_channel_id' => $channel[0]->id], []);
            if (empty($user->toArray()['roles'])) {
                $this->userRepo->setUser($user->id, [], [6]);
            }
        } catch (ModelNotFoundException $e) {
            $user = $this->userRepo->createUserByAcc(AccountType::Habook, $accId, $accData);
            $this->userRepo->setUser($user->id, [], [6]);
        }

        switch ($duty) {
            case DutyType::Admin:
                $member = array(
                    'member_status' => 1,
                    'member_duty'   => DutyType::Admin
                );
                break;
            case DutyType::Expert:
                $member = array(
                    'member_status' => 1,
                    'member_duty'   => DutyType::Expert
                );
                break;
            case DutyType::General:
                $member = array(
                    'member_status' => 1,
                    'member_duty'   => DutyType::General
                );
                break;
            default:
                return $this->success(['status' => 0, 'message' => 'Duty type error']);
                break;
        }

        try {
            $this->memberSrv->getMember($group->id, $user->id);
            $this->memberSrv->setMember($group->id, $user->id, $member);
            $res = $this->memberSrv->getMember($group->id, $user->id);
        } catch (ModelNotFoundException $e) {
            $res = $this->memberSrv->createMember($group->id, $user->id, $member);
        }

        $result[] = $res;


        return $this->success(['status' => 1, 'data' => $result]);
    }
}
