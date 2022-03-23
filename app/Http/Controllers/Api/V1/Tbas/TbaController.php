<?php

namespace App\Http\Controllers\Api\V1\Tbas;

use App\Helpers\Custom\GlobalPlatform;
use App\Libraries\Azure\Blob;
use App\Libraries\Lang\Lang;
use App\Services\App\UserService;
use App\Services\Group\GroupService;
use App\Services\GroupSubjectFieldsService;
use App\Services\RatingService;
use App\Types\Group\DutyType;
use App\Types\Src\SrcType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
use App\Http\Transformers\Api\V1\TbaTransformer;
use App\Models\Tba;
use App\Services\Cms\TbaService;
use Illuminate\Http\JsonResponse;

class TbaController extends Controller
{
    private $tbaSrv = null;
    protected $ratingService;
    protected $groupSubjectFieldsService;
    protected $groupService;

    //
    /**
     * @var UserService
     */
    protected $userService;

    public function __construct(TbaService $tbaSrv, RatingService $ratingService, GroupSubjectFieldsService $groupSubjectFieldsService, GroupService $groupService, UserService $userService)
    {
        $this->module                    = ['cate' => 'Tbas', 'app' => 'Tba'];
        $this->tbaSrv                    = $tbaSrv;
        $this->ratingService             = $ratingService;
        $this->groupSubjectFieldsService = $groupSubjectFieldsService;
        $this->groupService              = $groupService;
        $this->userService               = $userService;
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
            'info'          => 'required|file', // json
            'thum'          => 'file', // img
            'evalEventFile' => 'file', // zip
            'statisticImg'  => 'file', // zip 註:暫時使用
            'annexFile'     => 'file', // zip
        ]);

        $this->authorize('create', Tba::class);

        $info = $req->file('info')->path();
        $info = json_decode(file_get_contents($info), true);
        $info = collect($info);

        $tba = $info->only([
            'name', 'description', 'teacher',
            'subject_field_id', 'subject', 'educational_stage_id', 'grade',
            'lecture_type', 'lecture_date', 'locale_id', 'mark', 'channel_id', 'habook_id',
            'student_count', 'irs_count', 'binding_number'
        ])->toArray();
        $req->merge([
            'tba'   => $tba,
            'anal'  => $info->get('anal'),
            'eval'  => $info->get('eval'),
            'stat'  => $info->get('stat'),
            'annex' => $info->get('annex'),
        ]);

        (new TbaTransformer($req))->execute();

        $userId = $req->user()->id;
        $tba    = $req->input('tba');
        $anal   = $req->input('anal');
        $eval   = $req->input('eval');
        $stat   = $req->input('stat');
        $annex  = $req->input('annex');

        if (isset($tba['channel_id'])) {
            $channel_id = $tba['channel_id'];
        } else if (isset($req->user()->group_channel_id)) {
            $channel_id = $req->user()->group_channel_id;
        } else {
            return $this->error(['message' => 'channel_id is undefined.']);
        }

        $existTbas = $this->tbaSrv->getTbasInChannel($channel_id, ['locale_id' => $tba['locale_id'], 'mark' => $tba['mark']])->toArray();

        if ($req->hasFile('thum')) {
            $tba['thum'] = $req->file('thum');
        }
        if ($req->hasFile('evalEventFile')) {
            $eval['eventFile'] = $req->file('evalEventFile');
        }
        if ($req->hasFile('statisticImg')) {
            $stat['img'] = $req->file('statisticImg');
        }
        if ($req->hasFile('annexFile')) {
            $annex['file'] = $req->file('annexFile');
        }
        $convertedGrade = GlobalPlatform::convertGrade($tba['educational_stage_id'], $tba['grade']);
        $tba['grade']   = is_null($convertedGrade) ? $tba['grade'] : $convertedGrade;

        $double_green_light_status        = collect($stat['list'])->whereIn('type', ['TechDex', 'PedaDex'])->where('idx', '>=', 70)->count();
        $tba['double_green_light_status'] = $double_green_light_status === 2 ? 1 : 0;

        if (empty($existTbas)) {
            $tba = $this->tbaSrv->createTba($userId, $tba, $anal, $eval, $stat, $annex);

            return $this->success([$tba->id]);
        } else {
            $isValidated = true;
            foreach ($existTbas as $existTba) {
                if ($existTba->content_public === 1) {
                    $isValidated = false;
                    $error       = '10001';
                    break;
                }
                if ($existTba->content_update_limit === 1) {
                    $isValidated = false;
                    $error       = '10002';
                    break;
                }
                if ($existTba->upload_limit === 1) {
                    $isValidated = false;
                    $error       = '10003';
                    break;
                }
                if (!is_null($existTba->upload_ended_at)) {
                    if (time() > strtotime($existTba->upload_ended_at)) {
                        $isValidated = false;
                        $error       = '10004';
                        break;
                    }
                }
            }
            if ($isValidated) {
                $tbaIds = [];
                $thum   = $tba['thum'];
                foreach ($existTbas as $existTba) {
                    $tbaIds[] = $existTba->id;
                    $tba      = [
                        'user_id'                   => $existTba->user_id,
                        'name'                      => $tba['name'],
                        'description'               => $tba['description'],
                        'teacher'                   => $tba['teacher'],
                        'subject_field_id'          => $tba['subject_field_id'],
                        'subject'                   => $tba['subject'],
                        'educational_stage_id'      => $tba['educational_stage_id'],
                        'grade'                     => $tba['grade'],
                        'habook_id'                 => $tba['habook_id'],
                        'lecture_type'              => $tba['lecture_type'],
                        'lecture_date'              => $tba['lecture_date'],
                        'double_green_light_status' => $tba['double_green_light_status'],
                        'student_count'             => $tba['student_count'],
                        'irs_count'                 => $tba['irs_count'],
                        'binding_number'            => $tba['binding_number'],
                    ];
                    $tba      = $this->tbaSrv->updateTba($existTba->id, $tba, $thum, $anal, $eval, $stat, $annex, $channel_id);
                }

                return $this->success($tbaIds);
            } else {

                return $this->error($error);
            }
        }
    }

    //
    public function show(int $channel_id)
    {
        try {
            $tbaInfo = $this->tbaSrv->getShareVideo($channel_id, auth()->user()->habook);
        } catch (\Exception $exception) {
            return $this->fail(['message' => $exception]);
        }

        return $this->success($tbaInfo);

    }

    //
    public function update()
    {

        return $this->success(['message' => 'update']);

    }

    /**
     * Delete tba content (group_channel_content)
     * @param Request $req
     * @param int $channelId
     * @param int $contentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $req, int $channelId, int $contentId)
    {
        $isSuccessful = false;
        try {
            // Check params
            if (!$channelId) throw new \Exception('channelId is undefined.');
            if (!$contentId) throw new \Exception('contentId is undefined.');

            // Check authority
            $userId = $req->user()->id;
            $tba = $this->tbaSrv->getTba($contentId);
            if ($tba->user_id !== $userId) {
                throw new \Exception('You are not authorized.');
            }

            // Execute deletion
            $isSuccessful = $this->tbaSrv->deletePrivateGroupChannelContent($channelId, $contentId);

            return $this->success([
                'status' => $isSuccessful,
            ]);
        } catch (\Exception $exception) {
            $errMsg = $exception->getMessage();
            Log::error($errMsg);
            return $this->fail([
                'status' => $isSuccessful,
                'message' => $errMsg
            ]);
        }
    }

    /**
     * 分享影片至其他 頻道
     * @param Request $request
     * @param int $channel_id
     * @return mixed
     */
    public function shareVideo(Request $request, int $channel_id)
    {
        $subject                 = $request->input('subject') ?? null;
        $grade                   = $request->input('grade') ?? null;
        $ratings_id              = $request->input('ratings_id') ?? null;
        $group_subject_fields_id = $request->input('group_subject_fields_id') ?? null;
        $tba_id                  = $request->input('content_id');
        $group_id                = GlobalPlatform::convertChannelIdToGroupId($channel_id);
        try {
            $this->userService->userJoinForGroup(auth()->id(), $group_id, ['member_status' => 1, 'member_duty' => DutyType::General]);
            $alias         = $subject;
            $review_status = (int)$this->groupService->findBy('id', $group_id)->review_status;

            $this->tbaSrv->createGroupChannelContent($tba_id, $channel_id, [
                'group_id'                => $group_id,
                'content_status'          => $review_status === 0 ? 1 : 2,
                'group_subject_fields_id' => $group_subject_fields_id,
                'grades_id'               => $grade,
                'ratings_id'              => $ratings_id,
                'share_status'            => 0,
            ]);
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return $this->fail(['message' => $exception->getMessage()]);
        }
        return $this->success(['message' => 'Successful sharing']);
    }

    /**
     * 刪除分享影片
     * @param Request $request
     * @param int $channel_id
     * @return bool
     */
    public function deleteShareVideo(Request $request, int $channel_id): bool
    {
        $tba_id = $request->input('content_id');
        return $this->tbaSrv->deleteGroupChannelContent($tba_id, $channel_id);
    }

    public function uploadVideoToGroup(Request $request, $channel_id): \Illuminate\Http\JsonResponse
    {
        $group_id = GlobalPlatform::convertChannelIdToGroupId($channel_id);

        // Check file existence
        if (!$request->hasFile('video'))
            return $this->fail(['message' => 'File does not exist']);

        // Check file format (extension)
        $fileExt = strtolower($request->file('video')->getClientOriginalExtension());
        if (!in_array($fileExt, ['mp4']))
            return $this->fail(['message' => 'Incorrect format']);

        try {
            $file             = $request->file('video');
            $FileOriginalName = explode('.', $file->getClientOriginalName());
            $fileName         = $FileOriginalName[0];
            $user             = auth()->user();
            $user_id          = $user->id;
            $rating_id        = $this->ratingService->firstWhere(['groups_id' => $group_id, 'type' => 0])->id;
            $review_status    = (int)$this->groupService->findBy('id', $group_id)->review_status;
            $tbaData          = [
                'user_id'      => $user_id,
                'name'         => $fileName,
                'teacher'      => $user->name,
                'description'  => null,
                'lecture_date' => Carbon::now()->format('Y-m-d'),
                'locale_id'    => Lang::getConvertByLangString(\App::getLocale()),
                'habook_id'    => auth()->user()->habook,

            ];
            $resourceData     = [
                'user_id'  => $user_id,
                'src_type' => SrcType::Vod,
                'name'     => $fileName,
                'status'   => 1
            ];

            $groupChannelDate = [
                'content_status' => $review_status === 0 ? 1 : 2,
                'group_id'       => $group_id,
                'ratings_id'     => $rating_id,
                'author_id'      => $user_id,
                'share_status'   => 1
            ];
            return $this->success($this->tbaSrv->uploadVideo($user_id, $tbaData, $resourceData, $fileName, $file, $groupChannelDate));
        } catch (\Exception $exception) {
            return $this->fail(['message' => $exception->getMessage()]);
        }
    }

    /**
     * Set Tba TimePoints
     * @param Request $request
     * @param int $tbaId
     * @return \Illuminate\Http\JsonResponse
     */
    public function setTbaTimePoints(Request $request, int $tbaId): \Illuminate\Http\JsonResponse
    {
        try {
            if (!$tbaId) throw new \Exception('tbaId is required');

            $timePoint = $request->input('timePoint');
            if (!$timePoint) throw new \Exception('timePoint is required');
            if (!is_numeric($timePoint)) throw new \Exception('timePoint is not numeric');

            $mode = $request->input('mode');
            if (!$mode) throw new \Exception('mode is required');

            $isSuccessful = $this->tbaSrv->updateTbaTimePoints($tbaId, $timePoint, $mode);
            if (!$isSuccessful) throw new \Exception('Update failed');

            return $this->success([
                'status' => $isSuccessful,
                'message' => 'Successful'
            ]);
        } catch (\Exception $exception) {
            return $this->fail([
                'status' => false,
                'message' => $exception->getMessage()
            ]);
        }
    }
}
