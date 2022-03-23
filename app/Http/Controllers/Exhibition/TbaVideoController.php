<?php

namespace App\Http\Controllers\Exhibition;

use App\Exports\TableExport;
use App\Helpers\Code\ImageUploadHandler;
use App\Helpers\Custom\GlobalPlatform;
use App\Http\Resources\TbaCollection;
use App\Http\Resources\TbaCommentObsrvCollection;
use App\Http\Resources\TbaUserCommentCollection;
use App\Http\Resources\TbaVideosObservedCollection;
use App\Models\GroupUser;
use App\Models\GroupChannel;
use App\Services\AnnexService;
use App\Services\Group\Content\TbaService as TbaContentService;
use App\Services\Group\GroupService;
use App\Services\GroupSubjectFieldsService;
use App\Services\RecommendedVideoService;
use App\Types\Group\DutyType;
use App\Types\Record\RecordType;
use App\Types\Tba\AnnexType;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Controller;
use App\Models\ExhibitionCmsSet;
use App\Models\GroupChannelContent;
use App\Models\TbaEvaluateEvent;
use App\Factories\Src\SrcServiceFactory;
use App\Repositories\ConfigRepository;
use App\Services\App\UserService;
use App\Services\CommentService;
use App\Services\BbLicenseService;
use App\Services\Cms\TbaService;
use App\Services\Cms\TbaVideoService;
use App\Services\Cms\VideoService;
use App\Services\Exhibition\ExhibitionService;
use App\Services\Exhibition\TbaVideoService as TbaVideoExhibitionService;
use App\Types\Cms\CmsType;
use App\Types\Exhibition\SetType;
use App\Types\Tba\InfoType;
use Maatwebsite\Excel\Facades\Excel;

class TbaVideoController extends Controller
{
    use ImageUploadHandler;

    private $exhibitionSrv = null;
    private $tbaVideoExhibitionSrv = null;
    private $tbaContentSrv = null;
    private $recommendedVideoSrv = null;

    //
    /**
     * @var GroupSubjectFieldsService
     */
    protected $groupSubjectFieldsService;
    protected $groupService;
    protected $tbaService;
    protected $annexService;
    protected $bbLicenseService;

    public function __construct(ExhibitionService         $exhibitionSrv,
                                TbaVideoExhibitionService $tbaVideoExhibitionSrv,
                                GroupSubjectFieldsService $groupSubjectFieldsService,
                                TbaContentService         $tbaContentSrv,
                                RecommendedVideoService   $recommendedVideoSrv,
                                GroupService              $groupService,
                                TbaService                $tbaService,
                                AnnexService              $annexService,
                                CommentService            $commentService,
                                UserService               $userService,
                                BbLicenseService          $bbLicenseService
    )
    {
        $this->module                    = ['cate' => 'Exhibition', 'app' => 'TbaVideo'];
        $this->exhibitionSrv             = $exhibitionSrv;
        $this->tbaVideoExhibitionSrv     = $tbaVideoExhibitionSrv;
        $this->groupSubjectFieldsService = $groupSubjectFieldsService;
        $this->tbaContentSrv             = $tbaContentSrv;
        $this->recommendedVideoSrv       = $recommendedVideoSrv;
        $this->groupService              = $groupService;
        $this->tbaService                = $tbaService;
        $this->annexService              = $annexService;
        $this->commentService            = $commentService;
        $this->userService               = $userService;
        $this->bbLicenseService          = $bbLicenseService;
    }

    //
    public function index(UserService $userSrv)
    {
        $userId = auth()->check() ? auth()->id() : null;
        $userInfo = $userId ? $userSrv->getUser($userId) : null;
        if ($userInfo) {
            $convertChannelIdToGroupId    = $userInfo->group_channel_id ? GlobalPlatform::convertChannelIdToGroupId($userInfo->group_channel_id) : null;
            $userInfo->group_channel_name = $convertChannelIdToGroupId ? $userInfo->groups()->where('id', $convertChannelIdToGroupId)->first()->channels->first()->name : null;
            $userInfo->obsrv_class_allowed = $userSrv->isAllowedToOperateObsrvClass($userId);
        }

        $modulePath = $this->parseModulePath($this->module, 'index');

        $data = [
            'module'  => $modulePath,
            'globals' => [
                'user'             => $userInfo,
                'genObsrvDutyList' => DutyType::listGeneralObservation(),
            ],
        ];

        return view($modulePath, $data);
    }

    //
    public function watch(Request $req, TbaVideoService $tbaVideoSrv, TbaService $tbaSrv)
    {
        $userId = auth()->id();

        $contentId      = $req->contentId;
        $groupIds       = $req->groupIds;
        $appointedStart = $req->start;
        $groupIds       = is_null($groupIds) ? null : explode(',', $groupIds);

        if (is_null($groupIds)) {
            $this->authorize('viewByContentId', new GroupChannelContent(['content_id' => $contentId, 'content_type' => CmsType::Tba]));
        } else {
            $this->authorize(
                'viewByGroupIds',
                [new GroupChannelContent(['content_id' => $contentId, 'content_type' => CmsType::Tba]), $groupIds]
            );
        }

        $tbaVideo = $tbaVideoSrv->getTbaVideo($contentId);
        $tbaVideoSrv->hitTbaVideo($contentId, $userId);

        $hasGuestComments = $this->commentService->hasGuestComments($contentId);
        $tbaPlayerInfo    = [
            'info'            => $tbaVideo['tba'],
            'playlist'        => $tbaVideoSrv->getTbaVideoPlaylist($tbaVideo),
            'appointedTime'   => ['start' => intval($appointedStart)],
            'evaluateOptions' => [
                'hasGuestEventAuth'  => $hasGuestComments,
                'hasPersonEventAuth' => isset($userId) ? true : false,
                'enableGuestEvents'  => $hasGuestComments, // Enable if there are guest comments
                'enablePersonEvents' => false
            ]
        ];

        $identityEventModesSet = $tbaSrv->getTbaIdentityEvalEventModesSet($tbaVideo['tba'], $groupIds[0], $userId);

        $tbaPlayerOpts = [
            'cpnts' => [
                'tools'          => false,
                'videoPlayer'    => [
                    'ezStation' => [
                        'hiddens' => ['starMark', 'hardMark'],
                    ],
                ],
                'chartEvalEvent' => [
                    'identities'     => $identityEventModesSet->pluck('identity'),
                    'eventModesList' => $identityEventModesSet->pluck('eventModes'),
                ],
            ],
        ];

        $modulePath = $this->parseModulePath(['cate' => 'Group', 'app' => 'Watch'], 'tbavideo');
        $configPath = $this->parseConfigPath($this->module, 'import');
        $action     = explode('/', $req->url());
        $action     = end($action);
        $urlCtrler  = substr_replace($req->url(), '', -1 * strlen($action));

        $data = [
            'module'  => $modulePath,
            'imports' => Config::get($configPath)['watch'],
            'globals' => [
                'tbaPlayerInfo' => $tbaPlayerInfo,
                'tbaPlayerOpts' => $tbaPlayerOpts,
                'path'          => [
                    'tbaPlayerApiSrv' => $urlCtrler,
                ],
            ],
        ];

        return view($modulePath, $data);
    }

    //
    public function watchAsOpen(Request $req, TbaVideoService $tbaVideoSrv)
    {
        $userId = auth()->id();

        $contentId      = $req->contentId;
        $appointedStart = $req->start;

        try {
            $this->authorize('view', new ExhibitionCmsSet(['cms_id' => $contentId, 'cms_type' => CmsType::TbaVideo]));
        } catch (Exception $e) {
            if (!$this->exhibitionSrv->checkCmsSet($contentId, CmsType::TbaVideo, [SetType::Top])) {
                throw $e;
            }
        }

        $tbaVideo = $tbaVideoSrv->getTbaVideo($contentId);
        $tbaVideoSrv->hitTbaVideo($contentId, $userId);

        $tbaPlayerInfo = [
            'info'            => $tbaVideo['tba'],
            'playlist'        => $tbaVideoSrv->getTbaVideoPlaylist($tbaVideo),
            'appointedTime'   => ['start' => intval($appointedStart)],
            'evaluateOptions' => [
                'hasGuestEventAuth'  => false,
                'hasPersonEventAuth' => isset($userId) ? true : false,
                'enableGuestEvents'  => false,
                'enablePersonEvents' => false
            ]
        ];

        $tbaPlayerOpts = [
            'cpnts' => [
                'tools'          => false,
                'videoPlayer'    => [
                    'ezStation' => [
                        'hiddens' => ['starMark', 'hardMark'],
                    ],
                ],
                'chartEvalEvent' => [
                    'identities'     => [],
                    'eventModesList' => [],
                ],
            ],
        ];

        $modulePath = $this->parseModulePath(['cate' => 'Group', 'app' => 'Watch'], 'tbavideo');
        $configPath = $this->parseConfigPath($this->module, 'import');
        $action     = explode('/', $req->url());
        $action     = end($action);
        $urlCtrler  = substr_replace($req->url(), '', -1 * strlen($action));

        $data = [
            'module'  => $modulePath,
            'imports' => Config::get($configPath)['watch'],
            'globals' => [
                'tbaPlayerInfo' => $tbaPlayerInfo,
                'tbaPlayerOpts' => $tbaPlayerOpts,
                'path'          => [
                    'tbaPlayerApiSrv' => $urlCtrler,
                ],
            ],
        ];

        return view($modulePath, $data);
    }

    //
    public function filters(Request $req): \Illuminate\Http\JsonResponse
    {
        $userId = auth()->id();

        $page        = $req->input('page', 1);
        $perPage     = $req->input('perPage', 12);
        $order       = $req->input('order', null);
        $conds       = $req->input('conds', []);
        $tbaFeatures = $req->input('tbaFeatures', []);
        $year        = $req->input('year', null);
        $search      = trim($req->input('search', ''));
        $channelId   = $req->input('channelId', null);
        $abbr        = $req->input('abbr', null);

        $result = $this->tbaVideoExhibitionSrv->filters(
            $page,
            $perPage,
            $userId,
            $order,
            $conds,
            $year,
            $search,
            $tbaFeatures,
            $channelId,
            $abbr
        );

        return Response::json([
            'status' => true,
            'data'   => $result
        ]);
    }

    //
    public function checkWithHabook(Request $req)
    {
        $this->validate($req, [
            'ticket' => 'required',
            'to'     => 'required',
        ]);

        $accHabookSrvConfig = Config::get('srvs.habook.account');
        $url                = $accHabookSrvConfig['url'] . '?ticket=' . $req->ticket . '&callback=' . route('auth.login.callbackhabook') . '?to=' . $req->to;

        return redirect($url);
    }

    /**
     * @param Request $req
     * @return \Illuminate\Http\JsonResponse
     */
    public function getExhibitInfo(Request $req): \Illuminate\Http\JsonResponse
    {
        $data                     = [
            'cms'     => [],
            'channel' => [],
        ];
        $data['cms']['tops']      = $this->exhibitionSrv->getCmsSets(CmsType::TbaVideo, SetType::Top);
        $data['cms']['hits']      = $this->tbaVideoExhibitionSrv->getRanks(12, [['col' => 'hits', 'dir' => 'desc']]);
        $data['cms']['news']      = $this->tbaVideoExhibitionSrv->getRanks(12, [['col' => 'lecture_date', 'dir' => 'desc'], ['col' => 'created_at', 'dir' => 'desc']]);
        $data['cms']['recommend'] = $this->recommendedVideoSrv->getPlatformRecommendedVideos(12);
        $data['channel']['excs']  = $this->exhibitionSrv->getGroupChannelSets(CmsType::TbaVideo);
        $data['channel']['hits']  = []; // 待修改:訂閱數為參考依據

        return Response::json([
            'status' => true,
            'data'   => $data
        ]);
    }

    /**
     * Get Data for My Sokrates
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getExhibitInfoByUser(Request $request): \Illuminate\Http\JsonResponse
    {
        $groupIds  = auth()->user()->groups()->pluck('group_id');
        $dataReqd  = $request->boolean('dataReqd', true);
        $totalReqd = $request->boolean('totalReqd', true);
        $excsReqd  = $request->boolean('excsReqd', true);

        // Initital data
        $data = [
            'channel' => [],
        ];

        // Get content
        if ($dataReqd) $data['channel']['data'] = $this->exhibitionSrv->getUserVideoCount(auth()->id());
        if (in_array(true, [$totalReqd, $excsReqd])) {
            $userGroupChannels = $this->exhibitionSrv->getGroupChannelSetsByUser(CmsType::TbaVideo, SetType::Excellent, $groupIds);
            if ($totalReqd) $data['channel']['total'] = $userGroupChannels->count();
            if ($excsReqd) $data['channel']['excs'] = $userGroupChannels->sortByDesc('total_content')->take(40);
        }

        return Response::json([
            'status' => true,
            'data'   => $data
        ]);
    }

    /**
     * get User Comments
     * @param Request $request
     * @return TbaUserCommentCollection
     */
    public function getCommentsByUser(Request $request): TbaUserCommentCollection
    {
        $userId = auth()->id();
        $mode   = $request->input('mode', false);
        $filter = $request->input('filter', '');
        $size   = $request->input('size', 100);
        return new TbaUserCommentCollection($this->commentService->getCommentsByUser($userId, $mode, $filter, $size));
    }

    //
    public function getFilters(Request $request): \Illuminate\Http\JsonResponse
    {
        $channelId     = $request->input('channelId') ?? false;
        $subjectFields = ($channelId) ? $this->exhibitionSrv->getChanelBySubjectFieldFilter($channelId) : $this->exhibitionSrv->getSubjectFieldFilter();

        $data = [
            'eduStages'     => $this->exhibitionSrv->getEduStageFilter(),
            'grades'        => $this->exhibitionSrv->getGrade(),
            'subjectFields' => $subjectFields,
            'lectureTypes'  => $this->exhibitionSrv->getLectureTypeFilter(),
            'tbaFeatures'   => $this->exhibitionSrv->getTbaFeatureFilter(),
            'years'         => $this->exhibitionSrv->getYearFilter(),
        ];

        return Response::json([
            'status' => true,
            'data'   => $data
        ]);
    }

    //
    public function searchKeywords(Request $req): \Illuminate\Http\JsonResponse
    {
        $name = $req->input('name', false);

        $result = $this->exhibitionSrv->searchKeywords($name);

        return Response::json([
            'status' => true,
            'data'   => $result
        ]);
    }

    //
    public function getPlaylistInfo(Request $req, TbaService $tbaSrv)
    {
        $userId = auth()->id();

        $data                       = [
            'mine'       => [],
            'subscribed' => [],
        ];
        $data['mine']['list']       = $tbaSrv->getTbas($userId, 10, [['col' => 'created_at', 'dir' => 'desc']], ['playlisted' => 1]);
        $data['favorite']['list']   = $tbaSrv->getTbaFavs($userId);
        $data['subscribed']['list'] = [];

        return Response::json([
            'status' => true,
            'data'   => $data
        ]);
    }

    //取得自己所在的頻道
    public function getMyChannelInfo(Request $req, TbaService $tbaService)
    {
        $data   = [];
        $userId = auth()->id();

        $channels = $tbaService->getMyChannelInfo($userId);

        foreach ($channels as $item) {

            $data['myChannel']['list'][] = $item->channels[0];

        }
        $data['allow_channel_upload']['list'] = $channels->where('school_upload_status', 1);


        return Response::json([
            'status' => true,
            'data'   => $data
        ]);

    }

    public function getGroupInfo(Request $req, TbaService $tbaService)
    {

        $data = $tbaService->getGroupInfo($req->groupId);
        return Response::json([
            'status' => true,
            'data'   => $data
        ]);
    }

    //取得學區影片 todo 待修改
    public function getDistrictGroupChannel(Request $req, TbaService $tbaService)
    {

        $data['list'] = $tbaService->getDistrictGroupChannel($req->abbr, $req->size);

        return Response::json([
            'status' => true,
            'data'   => $data
        ]);
    }

    //取得群組影片
    public function getMyGroupChannel(Request $req, TbaService $tbaService)
    {

        $userId = auth()->id();

        $data['list'] = $tbaService->getGroupChannel($req->channelId, $userId, $req->size);

        return Response::json([
            'status' => true,
            'data'   => $data
        ]);
    }


    /**
     *  學區內部統計用
     * @param Request $req
     * @param TbaService $tbaService
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDistrictGroupByCount(Request $req, TbaService $tbaService)
    {

        $abbr = $req->abbr;

        $data['list']            = $tbaService->getDistrictGroupChannelTotalByCount($abbr);
        $data['list']['subject'] = $tbaService->getDistrictSubjectCount($abbr);
        $data['list']['grade']   = $tbaService->getDistrictGradeCount($abbr);
        $data['list']['rating']  = $tbaService->getDistrictRatingCount($abbr);

        return Response::json([
            'status' => true,
            'data'   => $data
        ]);
    }

    public function getDistrictGroupByFilter(Request $req, TbaService $tbaService)
    {

        $abbr            = $req->abbr;
        $resultFilter    = collect(json_decode($req->filter));
        $resultFilter    = $resultFilter->filter(function ($value, $key) {
            return $value != null && $value != 'none';
        });
        $gradeCount      = $tbaService->getDistrictGradeCount($abbr, $resultFilter->get('districtSubjectFields'), $resultFilter->get('rating'));
        $subjectCount    = $tbaService->getDistrictSubjectCount($abbr, $resultFilter->get('rating'));
        $data['list']    = $tbaService->getDistrictGroupByFilter($abbr, $resultFilter, $req->input('size'));
        $data['grade']   = $gradeCount;
        $data['subject'] = $subjectCount;

        return Response::json([
            'status' => true,
            'data'   => $data
        ]);
    }


    public function getMyGroupByFilter(Request $req, TbaService $tbaService): \Illuminate\Http\JsonResponse
    {
        $userId = auth()->id();

        $resultFilter = collect(json_decode($req->filter));
        $resultFilter = $resultFilter->filter(function ($value, $key) {
            return $value != null && $value != 'none';
        });

        $gradeCount      = $tbaService->getGradeCount($req->channelId, $userId, $resultFilter->get('subjectFields'), $resultFilter->get('rating'));
        $subjectCount    = $tbaService->getSubjectCount($req->channelId, $userId, $resultFilter->get('rating'));
        $data['list']    = $tbaService->getGroupByFilter($req->channelId, $userId, $resultFilter, $req->size);
        $data['grade']   = $gradeCount;
        $data['subject'] = $subjectCount;

        return Response::json([
            'status' => true,
            'data'   => $data
        ]);
    }

    /**
     *  頻道內部統計用
     * @param Request $req
     * @param TbaService $tbaService
     * @return \Illuminate\Http\JsonResponse
     */
    public function getGroupChannelTotalByCount(Request $req, TbaService $tbaService)
    {
        $userId = auth()->id();

        $data['list']            = $tbaService->getGroupChannelTotalByCount($req->channelId);
        $data['list']['subject'] = $tbaService->getSubjectCount($req->channelId, $userId);
        $data['list']['grade']   = $tbaService->getGradeCount($req->channelId, $userId);
        $data['list']['rating']  = $tbaService->getRatingCount($req->channelId, $userId);

        return Response::json([
            'status' => true,
            'data'   => $data
        ]);
    }

    //取得待審核頻道影片
    public function getMyMovies(Request $req, TbaService $tbaService): \Illuminate\Http\JsonResponse
    {

        $userId = auth()->id();

        $data['list'] = $tbaService->getMyMovies($userId, $req->size);


        return Response::json([
            'status' => true,
            'data'   => $data
        ]);
    }

    // Get a movie
    public function getMyMovie(Request $req, TbaService $tbaService): \Illuminate\Http\JsonResponse
    {
        $userId    = auth()->id();
        $contentId = $req->contentId;

        return Response::json([
            'status' => true,
            'data'   => $tbaService->getMyMovie($userId, $contentId)
        ]);
    }

    // Get filter movie
    public function getFilterMovie(Request $req, TbaService $tbaService): \Illuminate\Http\JsonResponse
    {
        $contentId = is_array($req->tba_ids) ? $req->tba_ids : explode(',', $req->tba_ids);
        $paginate  = $req->input('size', 0);

        return Response::json([
            'status' => true,
            'data'   => $tbaService->getFilterMovie($contentId, $paginate)
        ]);
    }

    /**
     * Get My Observed Movies (the videos that have this user's comments)
     * @param Request $req
     * @param TbaService $tbaService
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMyObservedMovies(Request $req, TbaService $tbaService): \Illuminate\Http\JsonResponse
    {
        $userId = auth()->id();
        $paginate = $req->input('size', 100);

        return Response::json([
            'status' => true,
            'data' => $tbaService->getMyObservedMovies($userId, $paginate)
        ]);
    }

    /**
     * Get Recommended Movies
     * @param Request $req
     * @param RecommendedVideoService $recommendedVideoSrv
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRecommendedMovies(Request $req, RecommendedVideoService $recommendedVideoSrv): \Illuminate\Http\JsonResponse
    {
        $paginate = $req->input('size', 100);

        return Response::json([
            'status' => true,
            'data' => $recommendedVideoSrv->getRecommendationVideos($paginate)
        ]);
    }

    /**
     * Get Latest Movies
     * @param Request $req
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLatestMovies(Request $req): \Illuminate\Http\JsonResponse
    {
        $limit = $req->input('limit', 50);

        return Response::json([
            'status' => true,
            'data' => $this->tbaVideoExhibitionSrv->getRanks(
                $limit,
                [
                    ['col' => 'lecture_date', 'dir' => 'desc'],
                    ['col' => 'created_at', 'dir' => 'desc']
                ]
            )
        ]);
    }

    // Get Submission Choices
    public function getSubmissionChoices(Request $req): \Illuminate\Http\JsonResponse
    {
        $channelId = $req->channel_id;
        $data      = $this->exhibitionSrv->getSubmissionChoices($channelId);
        return Response::json([
            'status' => true,
            'data'   => $data
        ]);
    }

    /**
     * @param Request $req
     * @param TbaService $tbaService
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getChannel(Request $req, TbaService $tbaService)
    {

        $data = $tbaService->getChannel($req->channelId);

        return Response::json([
            'status' => true,
            'data'   => $data
        ]);
    }

    /**
     * Get Watch History
     * @param Request $req
     * @param TbaVideoService $tbaVideoSrv
     * @return \Illuminate\Http\JsonResponse
     */
    public function getHists(Request $req, TbaVideoService $tbaVideoSrv): \Illuminate\Http\JsonResponse
    {
        $userId   = auth()->id();
        $paginate = $req->input('size', 0);
        $result   = $tbaVideoSrv->getTbaVideoHists($userId, $paginate);

        return Response::json([
            'status' => true,
            'data'   => $result
        ]);
    }

    /**
     * Delete All Watch History from User
     * @param Request $req
     * @param TbaVideoService $tbaVideoSrv
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteHists(Request $req, TbaVideoService $tbaVideoSrv): \Illuminate\Http\JsonResponse
    {
        $userId       = auth()->id();
        $isSuccessful = $tbaVideoSrv->deleteTbaVideoHists($userId);
        return Response::json(['status' => $isSuccessful]);
    }

    /*
     * content
     * */

    //
    public function checkPolicy(Request $req)
    {
        $contentId = $req->contentId;

        $result = $this->exhibitionSrv->checkPolicy($contentId, CmsType::TbaVideo, [SetType::Top]);

        return Response::json([
            'status' => true,
            'data'   => $result
        ]);
    }

    //
    public function getContentInfo(Request $req): \Illuminate\Http\JsonResponse
    {

        $contentId = $req->contentId;
        $groupIds  = $req->groupIds;

        $groupIds = is_null($groupIds) ? null : explode(',', $groupIds);

        if (is_null($groupIds)) {
            try {
                $this->authorize('viewByContentId', new GroupChannelContent(['content_id' => $contentId, 'content_type' => CmsType::Tba]));
            } catch (Exception $e) {
                if (!$this->exhibitionSrv->checkCmsSet($contentId, CmsType::TbaVideo, [SetType::Top])) {
                    throw $e;
                }
            }
        } else {
            try {
                $this->authorize(
                    'viewByGroupIds',
                    [new GroupChannelContent(['content_id' => $contentId, 'content_type' => CmsType::Tba]), $groupIds]
                );
            } catch (Exception $e) {
                if (!$this->exhibitionSrv->checkCmsSet($contentId, CmsType::TbaVideo, [SetType::Top])) {
                    throw $e;
                }
            }
        }


        $result = $this->tbaVideoExhibitionSrv->getContentInfo($contentId, $groupIds);

        return Response::json([
            'status' => true,
            'data'   => $result
        ]);
    }

    // Update content info
    public function setContentInfo(Request $req): \Illuminate\Http\JsonResponse
    {
        $errorRes    = Response::json(['status' => false]);
        $contentId   = $req->input('contentId', null);
        $groupIds    = $req->input('groupIds', null);
        $userId      = $req->input('userId', null);
        $contentData = json_decode($req->input('contentData', null));
        $fileName    = null;
        $files       = $req->file('thumbnail');

        if ($files) {
            $fileName = $this->imageSave($files, 'tba', $contentId);
        }
        if ($req->file('HiTeachNote')) {
            $this->annexService->saveFile($contentId, $userId, $req->file('HiTeachNote'), AnnexType::HiTeachNote);
        }
        if ($req->file('LessonPlan')) {
            $this->annexService->saveFile($contentId, $userId, $req->file('LessonPlan'), AnnexType::LessonPlan);
        }
        if ($req->file('Material')) {
            $this->annexService->saveFile($contentId, $userId, $req->file('Material'), AnnexType::Material);
        }

        $contentData->thumbnail = $fileName;

        if (!$contentId || !$groupIds || !$userId || !$contentData) return $errorRes;

        // Update tbas
        $result = $this->tbaVideoExhibitionSrv->setContentInfo($contentId, $groupIds, (array)$contentData);
        if (!$result['status']) return $errorRes;

        // Update annexes
        // To be written

        return Response::json([
            'status' => $result['status'],
            'data'   => $result['data']
        ]);
    }

    //
    public function exeContentAnnex(Request $req, TbaService $tbaSrv)
    {
        $annexId   = $req->annexId;
        $annex     = $tbaSrv->getTbaAnnex($annexId);
        $contentId = $annex->tba_id;
        $groupIds  = $req->groupIds;
        $groupIds  = is_null($groupIds) ? null : explode(',', $groupIds);

        if (is_null($groupIds)) {
            try {
                $this->authorize('viewByContentId', new GroupChannelContent(['content_id' => $contentId, 'content_type' => CmsType::Tba]));
            } catch (Exception $e) {
                if (!$this->exhibitionSrv->checkCmsSet($contentId, CmsType::TbaVideo, [SetType::Top])) {
                    throw $e;
                }
            }
        } else {
            $this->authorize(
                'viewByGroupIds',
                [new GroupChannelContent(['content_id' => $contentId, 'content_type' => CmsType::Tba]), $groupIds]
            );
        }

        $resrc  = $annex->resource;
        $srcSrv = (new SrcServiceFactory(new ConfigRepository()))->create($resrc->src_type);

        return $srcSrv->getExecuting($resrc->src());
    }

    /*
     * tbavideo
     * */

    //
    public function getTbaVideoSectMap(Request $req, TbaVideoService $tbaVideoSrv)
    {
        $contentId = $req->contentId;
        $groupIds  = $this->get_client_groupIds();
        $groupIds  = is_null($groupIds) ? null : explode(',', $groupIds);

        if (is_null($groupIds)) {
            try {
                $this->authorize('viewByContentId', new GroupChannelContent(['content_id' => $contentId, 'content_type' => CmsType::Tba]));
            } catch (Exception $e) {
                if (!$this->exhibitionSrv->checkCmsSet($contentId, CmsType::TbaVideo, [SetType::Top])) {
                    throw $e;
                }
            }
        } else {
            $this->authorize(
                'viewByGroupIds',
                [new GroupChannelContent(['content_id' => $contentId, 'content_type' => CmsType::Tba]), $groupIds]
            );
        }

        $result = $tbaVideoSrv->getTbaVideoSectMap($contentId);

        return Response::json([
            'status' => true,
            'data'   => $result
        ]);
    }

    //
    public function setTbaVideoMaps(Request $req, TbaVideoService $tbaVideoSrv)
    {
        // 待實作:功能暫不開放
        return Response::json([
            'status' => false,
        ]);
    }

    /**
     * Get getUserDuties from userId and groupIds
     * @param Request $req
     * @param TbaService $tbaSrv
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserDuties(Request $req, TbaService $tbaSrv)
    {
        try {
            $userId = auth()->id();
            if (!$userId) throw new Exception('User not found');
            $groupIds   = $this->get_client_groupIds();
            $groupIds   = is_null($groupIds) ? [] : explode(',', $groupIds);
            $userDuties = $tbaSrv->getUserDuties($userId, $groupIds);
            return Response::json([
                'status' => true,
                'data'   => $userDuties
            ]);
        } catch (Exception $e) {
            return Response::json([
                'status' => false,
                'error'  => $e->getMessage(),
                'data'   => []
            ]);
        }
    }

    /**
     * Get User Info
     * @param Request $req
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserInfo(Request $req)
    {
        try {
            $userId = auth()->id();
            if (!$userId) throw new Exception('User not found');
            $userInfo = $this->userService->getUserInfo($userId);
            return Response::json([
                'status' => true,
                'data'   => $userInfo
            ]);
        } catch (Exception $e) {
            return Response::json([
                'status' => false,
                'error'  => $e->getMessage(),
                'data'   => null,
            ]);
        }
    }

    //
    public function getTbaEvalEventOpts(Request $req, TbaService $tbaSrv)
    {
        $userId = auth()->id();

        $contentId       = $req->contentId;
        $groupIds        = $this->get_client_groupIds();
        $groupIds        = is_null($groupIds) ? null : explode(',', $groupIds);
        $evaluateOptions = $req->evaluateOptions;
        $evaluateOptions = is_null($evaluateOptions) ? [] : json_decode($evaluateOptions, true);

        if (is_null($groupIds)) {
            try {
                $this->authorize('viewByContentId', new GroupChannelContent(['content_id' => $contentId, 'content_type' => CmsType::Tba]));
            } catch (Exception $e) {
                if (!$this->exhibitionSrv->checkCmsSet($contentId, CmsType::TbaVideo, [SetType::Top])) {
                    throw $e;
                }
            }
        } else {
            $this->authorize(
                'viewByGroupIds',
                [new GroupChannelContent(['content_id' => $contentId, 'content_type' => CmsType::Tba]), $groupIds]
            );
        }

        $result = $tbaSrv->getTbaEvalEventOpts($contentId, $userId, $evaluateOptions);

        return Response::json([
            'status' => true,
            'data'   => $result
        ]);
    }

    //
    public function getTbaInfo(Request $req, TbaService $tbaSrv)
    {
        $contentId       = $req->contentId;
        $type            = $req->type;
        $meta            = $req->input('meta', null);
        $meta            = is_null($meta) ? null : json_decode($meta, true);
        $groupIds        = $this->get_client_groupIds();
        $groupIds        = is_null($groupIds) ? null : explode(',', $groupIds);
        $evaluateOptions = $req->input('evaluateOptions', null);
        $evaluateOptions = is_null($evaluateOptions) ? null : json_decode($evaluateOptions, true);


        if (is_null($groupIds)) {
            try {
                $this->authorize('viewByContentId', new GroupChannelContent(['content_id' => $contentId, 'content_type' => CmsType::Tba]));
            } catch (Exception $e) {
                if (!$this->exhibitionSrv->checkCmsSet($contentId, CmsType::TbaVideo, [SetType::Top])) {
                    throw $e;
                }
            }
        } else {
            $this->authorize(
                'viewByGroupIds',
                [new GroupChannelContent(['content_id' => $contentId, 'content_type' => CmsType::Tba]), $groupIds]
            );
        }

        $result = $tbaSrv->getTbaInfo($contentId, $type, $meta, $evaluateOptions);

        return Response::json([
            'status' => true,
            'data'   => $result
        ]);
    }

    //
    public function getTbaEventInfo(Request $req, TbaService $tbaSrv)
    {
        $contentId = $req->contentId;
        $eventId   = $req->eventId;
        $type      = $req->type;
        $groupIds  = $this->get_client_groupIds();
        $groupIds  = is_null($groupIds) ? null : explode(',', $groupIds);

        if (is_null($groupIds)) {
            try {
                $this->authorize('viewByContentId', new GroupChannelContent(['content_id' => $contentId, 'content_type' => CmsType::Tba]));
            } catch (Exception $e) {
                if (!$this->exhibitionSrv->checkCmsSet($contentId, CmsType::TbaVideo, [SetType::Top])) {
                    throw $e;
                }
            }
        } else {
            $this->authorize(
                'viewByGroupIds',
                [new GroupChannelContent(['content_id' => $contentId, 'content_type' => CmsType::Tba]), $groupIds]
            );
        }

        $result = $tbaSrv->getTbaEventInfo($contentId, $eventId, $type);

        $event            = $tbaSrv->getTbaEvent($eventId, $type);
        $meta             = [];
        $meta['editable'] = Gate::allows('edit', $event);

        return Response::json([
            'status' => true,
            'data'   => $result,
            'meta'   => $meta,
        ]);
    }

    //
    public function createTbaEvalEvent(Request $req, TbaService $tbaSrv)
    {
        $userId = auth()->id();

        $contentId   = $req->contentId;
        $eventModeId = $req->eventModeId;
        $groupIds    = $this->get_client_groupIds();
        $groupIds    = is_null($groupIds) ? null : explode(',', $groupIds);
        $req->event  = (gettype($req->event) === 'string') ? json_decode($req->event, true) : $req->event;
        $event       = [
            'time_point' => $req->event['time'],
            'text'       => $req->event['text'],
        ];

        if (is_null($groupIds)) {
            $this->authorize('viewByContentId', new GroupChannelContent(['content_id' => $contentId, 'content_type' => CmsType::Tba]));
        } else {
            $this->authorize(
                'viewByGroupIds',
                [new GroupChannelContent(['content_id' => $contentId, 'content_type' => CmsType::Tba]), $groupIds]
            );
        }

        $tba       = $tbaSrv->getTba($contentId);
        $eventMode = $tbaSrv->getTbaEvalEventMode($eventModeId);
        $this->authorize('create', [TbaEvaluateEvent::class, $eventMode, $tba]);

        $files = null;
        if (isset($_FILES['image'])) {
            $files   = [];
            $files[] = [
                'name'      => pathinfo($_FILES['image']['name'], PATHINFO_FILENAME),
                'ext'       => pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION),
                'path'      => $_FILES['image']['tmp_name'],
                'image_url' => null
            ];
        }

        $result = $tbaSrv->createTbaEvalEvent($userId, $contentId, $eventMode, $event, $groupIds[0], $files);

        return Response::json([
            'status' => true,
            'data'   => $result
        ]);
    }

    public function updateTbaEvalEvent(Request $req, TbaService $tbaSrv)
    {
        $eventId    = $req->eventId;
        $req->event = (gettype($req->event) === 'string') ? json_decode($req->event, true) : $req->event;
        $event      = [
            'text' => $req->event['text'],
        ];

        $files = null;
        if (isset($_FILES['image'])) {
            $files   = [];
            $files[] = [
                'name'      => pathinfo($_FILES['image']['name'], PATHINFO_FILENAME),
                'ext'       => pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION),
                'path'      => $_FILES['image']['tmp_name'],
                'image_url' => null
            ];
        }

        $result = $tbaSrv->updateTbaEvalEvent($eventId, $event, $files);

        return Response::json([
            'status' => true,
            'data'   => $result
        ]);
    }

    //
    public function deleteTbaEvalEvent(Request $req, TbaService $tbaSrv)
    {
        $contentId = $req->contentId;
        $eventId   = $req->eventId;

        $event = $tbaSrv->getTbaEvent($eventId, InfoType::EvalEvent);
        $this->authorize('delete', $event);

        $tbaSrv->deleteTbaEvalEvent($eventId);

        return Response::json(['status' => true]);
    }

    //
    public function getVideoInfo(Request $req, VideoService $videoSrv)
    {
        $contentId = $req->contentId;
        $videoId   = $req->videoId;
        $groupIds  = $this->get_client_groupIds();
        $groupIds  = is_null($groupIds) ? null : explode(',', $groupIds);

        if (is_null($groupIds)) {
            try {
                $this->authorize('viewByContentId', new GroupChannelContent(['content_id' => $contentId, 'content_type' => CmsType::Tba]));
            } catch (Exception $e) {
                if (!$this->exhibitionSrv->checkCmsSet($contentId, CmsType::TbaVideo, [SetType::Top])) {
                    throw $e;
                }
            }
        } else {
            $this->authorize(
                'viewByGroupIds',
                [new GroupChannelContent(['content_id' => $contentId, 'content_type' => CmsType::Tba]), $groupIds]
            );
        }

        $exeInfo   = $videoSrv->getExeInfo($videoId);
        $ezStation = $videoSrv->getEzStationInfo($videoId);

        return Response::json([
            'status' => true,
            'data'   => [
                'video'     => $exeInfo,
                'ezStation' => $ezStation,
            ],
        ]);
    }

    private function get_client_groupIds()
    {
        $param = explode("groupIds=", $_SERVER["HTTP_REFERER"]);

        return isset($param[1]) ? $param[1] : null;
    }

    public function goToPlayer(Request $request)
    {
        $contentId = $request->contentId;
        $groupIds  = $request->groupIds;
        $channelId = $request->channelId;
        $start     = $request->start;
        $userId    = auth()->id();

        $member_duty = GroupUser::query()->select('member_duty')->where([
            'user_id'  => $userId,
            'group_id' => $groupIds
        ])->value('member_duty');

        return ($member_duty == DutyType::Expert || $member_duty == DutyType::Admin)
            ? redirect(\url("/group/$groupIds/watch/channel/$channelId/tbavideo?contentId=$contentId&groupIds=$groupIds&channelId=$channelId&start=$start"))
            : redirect(\url("/exhibition/tbavideo/watch?contentId=$contentId&groupIds=$groupIds&channelId=$channelId&start=$start"));
    }

    public function getPlayerSharedURL(Request $request)
    {
        $this->validate($request, [
            'contentId' => 'required',
            'groupIds'  => 'required',
            'channelId' => 'required',
            'start'     => 'required',
        ]);
        $contentId = $request->contentId;
        $groupIds  = $request->groupIds;
        $channelId = $request->channelId;
        $start     = $request->start;
        $to        = base64_encode("/Player?contentId=$contentId&groupIds=$groupIds&channelId=$channelId&start=$start&memberChannel=0");

        $channelName = GroupChannel::query()->where([
            'id' => $channelId
        ])->value('name');

        return response()->json(['status' => true, 'data' => ['url' => (\url("/auth/login?to=$to")), 'channel' => $channelName]], 200);
    }

    public function exportExcel(Request $request)
    {
        Redis::rpush('excel', json_encode($request->params));

        return response()->json(200);
    }

    public function generateExportUrl(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
//        $lang                      = new Lang();
        $data                      = Redis::rpop('excel');
        $json_decode               = json_decode($data, true);
        $user_info                 = (object)$json_decode['user_info'];
        $convertChannelIdToGroupId = GlobalPlatform::convertChannelIdToGroupId($user_info->channelId);
//        $locales_id                = $lang->getConvertByLangString(\App::getLocale());

        $filename = $this->groupService->getGroup($convertChannelIdToGroupId)->name . '.xlsx';

        return Excel::download(new TableExport($json_decode), $filename);
    }

    /**
     * Get content and comment info
     * This controller is currently used in observationForm only
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTbaInfoAndComments(Request $request): \Illuminate\Http\JsonResponse
    {
        $groupChannelId = $request->input('channelId');
        $contentId      = $request->input('contentId');
        $data           = [
            'tbaInfo'     => new TbaCollection($this->tbaService->getObsrvTbaInfo($groupChannelId, $contentId)),
            'commentInfo' => new TbaCommentObsrvCollection($this->commentService->getComments($contentId, 1)),
        ];
        return response()->json($data, 200);
    }
    
    /**
     * Set Default Channel
     * @param Request $request
     * @param UserService $userSrv
     * @return \Illuminate\Http\JsonResponse
     */
    public function setDefaultChannel(Request $request, UserService $userSrv): \Illuminate\Http\JsonResponse
    {
        $userId = auth()->id();
        $isSuccessful = $userSrv->setupDefaultChannelForUser($userId);

        return Response::json([
            'status' => $isSuccessful,
        ]);
    }

}
