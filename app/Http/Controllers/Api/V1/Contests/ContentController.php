<?php

namespace App\Http\Controllers\Api\V1\Contests;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Api\Controller;
use App\Services\Contest\ContentService;
use App\Services\Group\MemberService;
use App\Repositories\GroupRepository;
use App\Repositories\Tba\StatisticRepository;
use App\Types\Group\DutyType;

class ContentController extends Controller
{
    private $contestSrv = null;
    private $memberSrv = null;
    private $statRepo = null;

    //
    /**
     * @var GroupRepository
     */
    protected $groupRepository;

    public function __construct(ContentService $contentSrv, MemberService $memberSrv, StatisticRepository $statRepo, GroupRepository $groupRepository)
    {
        $this->module          = ['cate' => 'Contests', 'app' => 'Content'];
        $this->contentSrv      = $contentSrv;
        $this->memberSrv       = $memberSrv;
        $this->statRepo        = $statRepo;
        $this->groupRepository = $groupRepository;
    }

    //
    public function index()
    {

        return $this->success(['message' => 'index']);

    }

    //
    public function store(Request $req)
    {
        return $this->success(['message' => 'store']);
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

    //
    public function getSubmissionStatus(Request $req)
    {
        $this->validate($req, [
            'tmids'      => 'required',
            'schoolCode' => 'required',
        ]);

        $tmIds        = $req->input('tmids');
        $schoolCode   = $req->input('schoolCode');
        $clientUserId = auth()->id();

        try {
            $group = $this->groupRepository->getGroupBySchoolCode($schoolCode);
        } catch (ModelNotFoundException $e) {
            return $this->error(['status' => 0, 'data' => [], 'msg' => 'This action is unauthorized.']);
        }
        if ($group->public !== 1) {
            return $this->error(['status' => 0, 'data' => [], 'msg' => 'This action is unauthorized.']);
        }

        $result = [];
        foreach ($tmIds as $tmId) {
            $submissionStatus = $this->contentSrv->getSubmissionStatus($group->id, $tmId);
            if (count($submissionStatus) > 0) {
                $techInteract = $this->statRepo->getTechInteractIdx($submissionStatus[0]->content_id);
                $methodAnal   = $this->statRepo->getMethodAnal($submissionStatus[0]->content_id);
                $contentIdx   = $this->statRepo->getContentIdx($submissionStatus[0]->content_id);
                $to           = base64_encode(url('/exhibition/tbavideo#/content') . '/' . $submissionStatus[0]->content_id . '?groupIds=' . $group->id . '&channelId=' . $submissionStatus[0]->group_channel_id . '&memberChannel=0');
                $result[]     = [
                    'id'         => $tmId,
                    'name'       => $submissionStatus[0]->name,
                    'created_at' => $submissionStatus[0]->created_at,
                    'link'       => url('/exhibition/tbavideo/check-with-habook') . '/?to=' . $to,
                    'score'      => null,
                    'indexes'    => ['techInteract' => round($techInteract['idx']), 'methodAnal' => round($methodAnal['value']), 'content' => $contentIdx['value']],
//                        'status'     => $submissionStatus[0]->content_status,
                ];
            } else {
                $result[] = [
                    'id'         => $tmId,
                    'name'       => null,
                    'created_at' => null,
                    'link'       => null,
                    'score'      => null,
                    'indexes'    => ['techInteract' => null, 'methodAnal' => null],
                ];
            }

        }

        return $this->success(['status' => 1, 'data' => $result]);
    }
}
