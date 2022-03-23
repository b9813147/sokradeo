<?php

namespace App\Http\Controllers\Api\V1\Groups;

use App\Services\App\UserService;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
use App\Http\Resources\Api\V1\GroupMemberResource;
use App\Http\Transformers\Api\V1\GroupMemberTransformer;
use App\Services\Group\GroupService;
use App\Services\Group\MemberService;

class MemberController extends Controller
{
    private $groupSrv = null;
    private $memberSrv = null;

    //
    /**
     * @var UserService
     */
    protected $userService;

    public function __construct(GroupService $groupSrv, MemberService $memberSrv, UserService $userService)
    {
        $this->module      = ['cate' => 'Groups', 'app' => 'Member'];
        $this->groupSrv    = $groupSrv;
        $this->memberSrv   = $memberSrv;
        $this->userService = $userService;
    }

    //
    public function index()
    {

        return $this->success(['message' => 'index']);

    }

    //
    public function store(Request $req)
    {
        $userId  = $req->user()->id;
        $groupId = $req->groupId;

        $group = $this->groupSrv->getGroup($groupId);

        (new GroupMemberTransformer($req))->execute();

        $member = $req->only(['member_status', 'member_duty']);

        $member = $this->memberSrv->createMember($groupId, $userId, $member);

        return $this->success(new GroupMemberResource($member));
    }

    //
    public function show()
    {

        return $this->success(['message' => 'show']);

    }

    //
    public function update(Request $request, int $userId): \Illuminate\Http\JsonResponse
    {
        try {
            if ($userId != auth()->id()) throw new \Exception('Not allowed');
            $userData = $request->only('group_channel_id');
            $this->userService->update(auth()->id(), $userData);

            $response = $this->success(['message' => 'Update success']);
        } catch (\Exception $exception) {
            \Log::error('update user', [$exception->getMessage()]);
            $response = $this->fail(['message' => 'Update failed']);
        }
        return $response;

    }

    //
    public function destroy()
    {

        return $this->success(['message' => 'destroy']);

    }
}
