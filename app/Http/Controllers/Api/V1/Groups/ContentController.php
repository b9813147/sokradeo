<?php

namespace App\Http\Controllers\Api\V1\Groups;

use App\Helpers\Custom\GlobalPlatform;
use App\Services\Cms\TbaService;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Api\Controller;
use App\Http\Resources\Api\V1\GroupChannelContentResource;
use App\Http\Transformers\Api\V1\GroupChannelContentTransformer;
use App\Models\GroupChannelContent;
use App\Models\GroupChannel;
use App\Models\Tba;
use App\Models\GroupSubjectFields;
use App\Services\Group\ContentService;
use App\Services\Group\MemberService;
use App\Types\Group\DutyType;
use App\Repositories\UserRepository;

class ContentController extends Controller
{
    private $contentSrv = null;
    private $memberSrv = null;

    //
    /**
     * @var TbaService
     */
    protected $tbaService;

    public function __construct(ContentService $contentSrv, MemberService $memberSrv, UserRepository $userRepo, TbaService $tbaService)
    {
        $this->module     = ['cate' => 'Groups', 'app' => 'Content'];
        $this->contentSrv = $contentSrv;
        $this->memberSrv  = $memberSrv;
        $this->userRepo   = $userRepo;
        $this->tbaService = $tbaService;
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
            'content' => 'required',
        ]);

        $group_user = $this->setGroupUser($req->channelId);
        $extra_info = $req->extra;

        $this->authorize('create', [GroupChannelContent::class, $req->channelId]);

        (new GroupChannelContentTransformer($req))->execute();

        try {
            $content = $this->contentSrv->getContent($req->channelId, $req->input('content'), 'Tba');
        } catch (ModelNotFoundException $e) {
            $content = $this->contentSrv->createContent($req->channelId, $req->input('content'), $extra_info);
        }

        $new_content = new GroupChannelContentResource($content);

        $response = [
            'status' => 1,
            'data'   => $this->getChannelContentUrl($req->channelId, $new_content->content_id)
        ];

        // 私人影片不發
        if ($new_content->content_status != 2) {
            // 是學校頻道才發送通知
            \Log::info('頻道新增課例數據通知', ['status' => $this->tbaService->sendNotifyForCustom((int)$new_content->content_id, (int)$req->channelId)]);
        }
        return response()->json($response, 200, [], JSON_UNESCAPED_UNICODE);
    }

    //
    public function storeWithSchoolCode(Request $req)
    {
        $this->validate($req, [
            'content'    => 'required',
            'schoolCode' => 'required',
        ]);

        $this->authorize('createWithSchoolCode', [GroupChannelContent::class, $req->schoolCode]);

        (new GroupChannelContentTransformer($req))->execute();

        $content = $this->contentSrv->createContentWithSchoolCode($req->schoolCode, $req->input('content'));

        return $this->success(new GroupChannelContentResource($content));
    }

    //
    public function storeWithMemberChannel(Request $req)
    {
        $this->validate($req, [
            'content' => 'required',
        ]);

        $clientUserId = auth()->id();
        $user         = $this->userRepo->getUser($clientUserId);
        $channelId    = $user->group_channel_id;

//        $this->authorize('create', [GroupChannelContent::class, $channelId]);

        (new GroupChannelContentTransformer($req))->execute();

        $content = $this->contentSrv->createContent($channelId, $req->input('content'));

        return $this->success(new GroupChannelContentResource($content));
    }

    //
    public function setGroupUser($channelId)
    {
        $userId = auth()->id();
        $group  = GroupChannel::findOrFail($channelId)->group()->firstOrFail();

        try {
            $res = $this->memberSrv->getMember($group->id, $userId);
        } catch (ModelNotFoundException $e) {
            $member = array(
                'member_status' => 1,
                'member_duty'   => DutyType::General
            );
            $res    = $this->memberSrv->createMember($group->id, $userId, $member);
        }

        return $res;
    }

    public function getChannelContentUrl($channelId, $contentId)
    {
        $group           = GroupChannel::findOrFail($channelId)->group()->firstOrFail();
        $url             = url('/exhibition/tbavideo#/content') . '/' . $contentId . '?groupIds=' . $group->id . '&channelId=' . $channelId . '&memberChannel=0';
        $to              = base64_encode(url('/exhibition/tbavideo#/content') . '/' . $contentId . '?groupIds=' . $group->id . '&channelId=' . $channelId . '&memberChannel=0');
        $url_with_ticket = url('/exhibition/tbavideo/check-with-habook') . '/?to=' . $to;

        return [
            'url'             => $url,
            'url_with_ticket' => $url_with_ticket
        ];
    }

    public function setGradesAndSubjectsByBatch()
    {
        $filterByGrades   = GroupChannelContent::query()->whereNull('grades_id')->get()->toArray();
        $filterBySubjects = GroupChannelContent::query()->whereNull('group_subject_fields_id')->get()->toArray();
//        print_r(count($filterByGrades));
//        print_r('<br>');
//        print_r(count($filterBySubjects));exit;
        if (!empty($filterByGrades)) {
            foreach ($filterByGrades as $idx => $item) {
                $tba       = Tba::query()->where('id', $item['content_id'])->first();
                $grades_id = GlobalPlatform::convertGrade($tba->educational_stage_id, $tba->grade);
                if (is_null($grades_id)) {
                    $grades_id = $tba->grade;
                }
                GroupChannelContent::query()->where([
                    'group_id'         => $item['group_id'],
                    'group_channel_id' => $item['group_channel_id'],
                    'content_id'       => $item['content_id']
                ])->update([
                    'grades_id' => $grades_id
                ]);
            }
        }
        if (!empty($filterBySubjects)) {
            foreach ($filterBySubjects as $idx => $item) {
                $tba = Tba::query()->where('id', $item['content_id'])->first();
                if (!empty($tba->subject)) {
                    $group_subject = GroupSubjectFields::query()->where(['groups_id' => $item['group_id'], 'subject' => $tba->subject])->first();
                    if (!empty($group_subject)) {
                        GroupChannelContent::query()->where([
                            'group_id'         => $item['group_id'],
                            'group_channel_id' => $item['group_channel_id'],
                            'content_id'       => $item['content_id']
                        ])->update([
                            'group_subject_fields_id' => $group_subject->id
                        ]);
                    }
                }
            }
        }
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
