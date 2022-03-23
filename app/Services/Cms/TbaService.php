<?php

namespace App\Services\Cms;

use App\Helpers\CoreService\CoreServiceApi;
use App\Helpers\Custom\GlobalPlatform;
use App\Libraries\Azure\Blob;
use App\Http\Resources\TbaCommentObsrvCollection;
use App\Models\Tba;
use App\Models\User;
use App\Notifications\EventChannel;
use App\Repositories\DistrictChannelContentRepository;
use App\Repositories\GroupChannelContentRepository;
use App\Repositories\GroupRepository;
use App\Types\Group\DutyType;
use App\Types\Src\VodType;
use App\Types\Video\Encoder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use InvalidArgumentException;
use Illuminate\Support\Facades\Lang;
use ZipArchive;
use App\Helpers\File\Ext as ExtFile;
use App\Helpers\Path\Tba as TbaPath;
use App\Factories\Src\SrcServiceFactory;
use App\Repositories\ConfigRepository;
use App\Repositories\ResourceRepository;
use App\Repositories\TbaRepository;
use App\Repositories\Tba\AnalysisEventRepository;
use App\Repositories\Tba\AnnexRepository;
use App\Repositories\Tba\EvaluateEventModeRepository;
use App\Repositories\Tba\EvaluateEventRepository;
use App\Repositories\Tba\EvaluateUserRepository;
use App\Repositories\Tba\FavoriteRepository as TbaFavoriteRepository;
use App\Repositories\Tba\HistoryRepository as TbaHistoryRepository;
use App\Repositories\Tba\StatisticRepository;
use App\Services\CommentService;
use App\Types\App\RoleType;
use App\Types\Src\SrcType;
use App\Types\Tba\AnnexType;
use App\Types\Tba\IdentityType;
use App\Types\Tba\InfoType;

class TbaService
{
    use ExtFile, TbaPath, CoreServiceApi;

    private $tbaRepo = null;
    private $analEventRepo = null;
    private $evalEventRepo = null;
    private $evalUserRepo = null;
    private $statRepo = null;
    private $annexRepo = null;
    private $groupRepo = null;
    private $commentService = null;

    //
    /**
     * @var GroupChannelContentRepository
     */
    protected $groupChannelContentRepository;
    /**
     * @var DistrictChannelContentRepository
     */
    protected $districtChannelContentRepository;
    /**
     * @var ResourceRepository
     */
    protected $resourceRepository;

    public function __construct(
        TbaRepository                    $tbaRepo,
        ResourceRepository               $resourceRepository,
        AnalysisEventRepository          $analEventRepo,
        EvaluateEventRepository          $evalEventRepo,
        EvaluateUserRepository           $evalUserRepo,
        StatisticRepository              $statRepo,
        AnnexRepository                  $annexRepo,
        GroupChannelContentRepository    $groupChannelContentRepository,
        DistrictChannelContentRepository $districtChannelContentRepository,
        GroupRepository                  $groupRepo,
        CommentService                   $commentService
    )
    {
        $this->tbaRepo                          = $tbaRepo;
        $this->analEventRepo                    = $analEventRepo;
        $this->evalEventRepo                    = $evalEventRepo;
        $this->evalUserRepo                     = $evalUserRepo;
        $this->statRepo                         = $statRepo;
        $this->annexRepo                        = $annexRepo;
        $this->groupChannelContentRepository    = $groupChannelContentRepository;
        $this->districtChannelContentRepository = $districtChannelContentRepository;
        $this->groupRepo                        = $groupRepo;
        $this->resourceRepository               = $resourceRepository;
        $this->commentService                   = $commentService;
    }

    //
    public function list($userId, $page)
    {
        return $this->tbaRepo->listByUserId($userId, $page);
    }

    //
    public function getTbas($userId, $limit = 10, $orders = [], $conds = [])
    {
        return $this->tbaRepo->getTbasByUserId($userId, $limit, $orders, $conds);
    }

    /**
     * @param $userId
     *
     * @return array|mixed
     * @Description 取得自己所在的頻道
     */
    public function getMyChannelInfo($userId)
    {
        return $this->tbaRepo->getMyChannel($userId)->groups;
    }

    public function getGroupInfo($groupId)
    {
        return $this->tbaRepo->getGroupInfo($groupId);
    }

    public function getGroupChannel($channel, $userId, $paginate = 0)
    {
        $groupId     = GlobalPlatform::convertChannelIdToGroupId($channel);
        $member_Duty = GlobalPlatform::getMemberDuty($groupId, $userId) ?? false;
        if ($member_Duty) {
            //影片狀態
            $content_status = [1];
            // 發布狀態
            $content_public = [1, 0];
            // 群組影片狀態
            $group_status = 1;
        } else {
            //影片狀態
            $content_status = [1];
            // 發布狀態
            $content_public = [1];
            // 群組影片狀態
            $group_status = 1;
        }
        return $this->tbaRepo->getGroupChannel($groupId, $content_status, $content_public, $group_status, $paginate);
    }

    /**
     * @param $abbr
     * @param integer $paginate
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    //todo 待修改
    public function getDistrictGroupChannel($abbr, int $paginate = 0)
    {
        $groupIds = GlobalPlatform::convertDistrictToGroupId($abbr);

        return $this->tbaRepo->getGroupChannel($groupIds, null, null, null, $paginate, true);
    }

    //
    public function getTbasInChannel($channelId, $conditions = [])
    {
        return $this->tbaRepo->getTbasInChannel($channelId, $conditions);
    }

    /**
     * 頻道內篩選
     *
     * @param $channel
     * @param $userId
     * @param $resultFilter
     * @param integer $paginate
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getGroupByFilter($channel, $userId, $resultFilter, int $paginate = 0)
    {
        $groupId    = GlobalPlatform::convertChannelIdToGroupId($channel);
        $userExists = (bool)GlobalPlatform::getMemberDuty($groupId, $userId);

        if (!$userExists) {
            //影片狀態
            $content_status = [1];
            // 發布狀態
            $content_public = [1];
            // 群組影片狀態
            $group_status = 1;

            return $this->tbaRepo->getGroupByFilter($groupId, $content_status, $content_public, $group_status, $resultFilter, $paginate);
        }

        $member_Duty = GlobalPlatform::getMemberDuty($groupId, $userId);
        if ($member_Duty == DutyType::Admin || $member_Duty == DutyType::Expert || $member_Duty == DutyType::General) {
            //影片狀態
            $content_status = [1];
            // 發布狀態
            $content_public = [1, 0];
            // 群組影片狀態
            $group_status = 1;

        }
        return $this->tbaRepo->getGroupByFilter($groupId, $content_status, $content_public, $group_status, $resultFilter, $paginate);
    }

    /**
     * @param $channel
     * @return array
     */
    public function getGroupChannelTotalByCount($channel)
    {
        return $this->tbaRepo->getChannelByCount($channel);
    }

    /**
     * @return \App\Models\Tba[]|\Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     * @Description 取得自己的影片
     */
    public function getMyMovies($userId, $paginate)
    {
        // $group_status=1;
        // return $this->tbaRepo->getMyChannel($userId,$content_status, $content_public, $group_status);
        return $this->tbaRepo->getMyMovies($userId, [], null, null, $paginate);
    }

    /**
     * @param int $userId
     * @param int $contentId
     * @return \App\Models\Tba[]
     * @Description Get a single movie
     */
    public function getMyMovie(int $userId, int $contentId)
    {
        return $this->tbaRepo->getMyMovie($userId, $contentId);
    }

    /**
     * @param array $contentIds
     * @param int $paginate
     * @return Tba[]|\Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|Collection
     * @Description Get movies from content ids
     */
    public function getFilterMovie(array $contentIds, int $paginate)
    {
        return $this->tbaRepo->getFilterMovie($contentIds, $paginate);
    }

    /**
     * Get My Observed Movies (the videos that have this user's comments)
     * @param int $userId
     * @param int $paginate
     * @return Tba[]|\Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|Collection
     */
    public function getMyObservedMovies(int $userId, int $paginate)
    {
        return $this->tbaRepo->getMyObservedMovies($userId, $paginate);
    }

    /**
     * @param int $channelId
     *
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getChannel($channelId)
    {
        return $this->tbaRepo->getChannelInfo($channelId);
    }

    //
    public function getTba($tbaId)
    {
        return $this->tbaRepo->getTba($tbaId);
    }

    //
    public function hitTba($tbaId, $userId = null)
    {
        $this->tbaRepo->hitTba($tbaId);
        if (is_null($userId)) {
            return;
        }
        (new TbaHistoryRepository())->createHist($userId, $tbaId);
    }

    //
    public function createTba($userId, $tba, $anal, $eval, $stat, $annex)
    {
        $thumFile         = isset($tba['thum']) ? $tba['thum'] : null;
        $thumName         = is_null($thumFile) ? null : 'thum.' . $thumFile->getClientOriginalExtension();
        $tba['thumbnail'] = $thumName;
        $group_id         = GlobalPlatform::convertChannelIdToGroupId($tba['channel_id']);
        $tba              = $this->tbaRepo->createTba($userId, $tba);

        if (!is_null($thumFile)) {

            $this->uploadThum($tba->id, $thumFile, $thumName);
        }

        $this->analEventRepo->createEventGroups($tba->id, $anal['events']);

        if (isset($eval['eventFile']) && !is_null($eval['eventFile'])) {

            $this->uploadCompressedFile($tba->id, $eval['eventFile']);
        }

        $this->evalEventRepo->createUsersEventGroups($tba->id, $eval['users']);

        if (isset($stat['img']) && !is_null($stat['img'])) {

            $this->uploadStatImg($tba->id, $stat['img']);
        }

        $this->statRepo->createStats($tba->id, $stat['list']);

        $annex['file'] = (isset($annex['file']) && !is_null($annex['file'])) ? $annex['file'] : null;
        $this->createTbaAnnexes($userId, $tba->id, $annex['list'], $annex['file']);

        $this->commentService->convertCreateComment($tba->id, $group_id);
        return $tba;
    }

    //
    public function updateTba($tbaId, $tba, $thum, $anal, $eval, $stat, $annex, $channel_id)
    {
        $thumFile         = isset($thum) ? $thum : null;
        $thumName         = is_null($thumFile) ? null : 'thum.' . $thumFile->getClientOriginalExtension();
        $tba['thumbnail'] = $thumName;
        $group_id         = GlobalPlatform::convertChannelIdToGroupId($channel_id);

        $this->tbaRepo->updateTba($tbaId, $tba);

        if (!is_null($thumFile)) {

            $this->uploadThum($tbaId, $thumFile, $thumName);
        }

        $this->analEventRepo->deleteEvents($tbaId);
        $this->analEventRepo->createEventGroups($tbaId, $anal['events']);

        if (isset($eval['eventFile']) && !is_null($eval['eventFile'])) {

            $this->uploadCompressedFile($tbaId, $eval['eventFile']);
        }

        $this->evalEventRepo->deleteUsersEvent($tbaId);
        $this->evalEventRepo->createUsersEventGroups($tbaId, $eval['users']);

        if (isset($stat['img']) && !is_null($stat['img'])) {

            $this->uploadStatImg($tbaId, $stat['img']);
        }

        $this->statRepo->deleteStats($tbaId);
        $this->statRepo->createStats($tbaId, $stat['list']);

        $annex['file'] = (isset($annex['file']) && !is_null($annex['file'])) ? $annex['file'] : null;
        $this->updateTbaAnnexes($tba['user_id'], $tbaId, $annex['list'], $annex['file']);
        $this->commentService->convertCreateComment($tbaId, $group_id);
        return $tba;
    }

    //
    public function getTbaAnnex($annexId)
    {
        return $this->annexRepo->getAnnex($annexId);
    }

    //
    public function createTbaAnnexes($userId, $tbaId, &$annexes, $annexFile = null)
    {
        if (!is_null($annexFile)) {

            $this->uploadCompressedFile($tbaId, $annexFile);
        }

        $srcSrvFty = new SrcServiceFactory(new ConfigRepository());
        $resrcRepo = app(ResourceRepository::class);
        $tmpPath   = $this->pathTba($tbaId, 'tmp');

        foreach ($annexes as $annex) {
            $annex = collect($annex);

            $srcType = AnnexType::getSrcType($annex->get('type'));
            if (!$srcType) {
                continue;
            }

            if ($srcType === SrcType::File) {
                $data         = $annex->get('data');
                $data['file'] = $tmpPath . $data['path'];
                $annex        = $annex->merge(['data' => $data]);
            }

            $resrc  = ['src_type' => $srcType, 'name' => $annex->get('name')];
            $resrc  = $resrcRepo->createResrc($userId, $resrc);
            $srcSrv = $srcSrvFty->create($resrc->src_type);
            $src    = $srcSrv->createSrc($resrc->id, $annex->get('data'));
            $resrcRepo->setResrc($resrc->id, ['status' => 1]);
            $annex = ['resource_id' => $resrc->id, 'type' => $annex->get('type')];
            $this->annexRepo->createAnnex($tbaId, $annex);
        }
    }

    //
    public function updateTbaAnnexes($userId, $tbaId, &$annexes, $annexFile = null)
    {
        if (!is_null($annexFile)) {

            $this->uploadCompressedFile($tbaId, $annexFile);
        }

        $srcSrvFty = new SrcServiceFactory(new ConfigRepository());
        $resrcRepo = app(ResourceRepository::class);
        $annexRepo = app(AnnexRepository::class);
        $tmpPath   = $this->pathTba($tbaId, 'tmp');

        foreach ($annexes as $annex) {
            $annex = collect($annex);

            $srcType = AnnexType::getSrcType($annex->get('type'));
            if (!$srcType) {
                continue;
            }

            if ($srcType === SrcType::File) {
                $data         = $annex->get('data');
                $data['file'] = $tmpPath . $data['path'];
                $annex        = $annex->merge(['data' => $data]);
            }

            $tbaAnnex = $annexRepo->getAnnexResrcs($tbaId, ['tba_annexes.type' => $annex->get('type'), 'resources.status' => 1])->toArray();
            if (!empty($tbaAnnex)) {
                $resrcRepo->setResrc($tbaAnnex[0]->resource_id, ['status' => 0]);
            }
            $resrc  = ['src_type' => $srcType, 'name' => $annex->get('name')];
            $resrc  = $resrcRepo->createResrc($userId, $resrc);
            $srcSrv = $srcSrvFty->create($resrc->src_type);
            $src    = $srcSrv->createSrc($resrc->id, $annex->get('data'));
            $resrcRepo->setResrc($resrc->id, ['status' => 1]);
            $annex = ['resource_id' => $resrc->id, 'type' => $annex->get('type')];
            $this->annexRepo->createAnnex($tbaId, $annex);
        }
    }

    //
    public function getTbaFavs($userId)
    {
        return (new TbaFavoriteRepository())->getFavs($userId);
    }

    //
    public function getTbaHists($userId)
    {
        return (new TbaHistoryRepository())->getHists($userId);
    }

    /**
     * @param \App\Models\Tba $tba
     * @return \Illuminate\Support\Collection
     */
    public function getTbaIdentityEvalEventModesSet($tba, $groupId, $userId)
    {
        $roles = session()->get('roles');


        $evalEventModeRepo = new EvaluateEventModeRepository();

        $set = collect([]);

        // 個人
        $set->push($evalEventModeRepo->getIdentityEventModes(IdentityType::User));

        // 授課
        if ($tba->playlisted === 0 && $userId === $tba->user_id) {
            $set->push($evalEventModeRepo->getIdentityEventModes(IdentityType::Teacher));
        }

        // 專家
        if (in_array(RoleType::Expert, $roles)) {
            $set->push($evalEventModeRepo->getIdentityEventModes(IdentityType::Expert));
        }

        if (!is_null($groupId)) {
            try {
                $group = $this->groupRepo->getGroup($groupId);
                $user  = $this->groupRepo->getMember($groupId, $userId);
                // 成員
                if (in_array($user->pivot->member_duty, [DutyType::General]) && $group->public_note_status === 1) {
                    $set->push($evalEventModeRepo->getIdentityEventModes(IdentityType::Visitor));
                }
            } catch (ModelNotFoundException $e) {
            }
        }

        return $set;
    }

    /**
     * Get User Duties from userId and groupIds
     * @param int $userId
     * @param array $groupIds
     */
    public function getUserDuties(int $userId, array $groupIds)
    {
        if (empty($userId) || empty($groupIds)) return [];
        return $this->groupRepo->getDutiesByUserIdAndGroupIds($userId, $groupIds);
    }

    //
    public function getTbaEvalEventOpts($tbaId, $userId = null, $evaluateOptions = [])
    {
        $mapIdentity       = Lang::get('app/tba/identity');
        $enableGuestEvents = isset($evaluateOptions['enableGuestEvents']) ? $evaluateOptions['enableGuestEvents'] : false;

        $opts = collect();
        $opts->push([]);
        $total = [
            'type'  => 'total',
            'value' => [],
            'text'  => $mapIdentity['Summary'],
            'note'  => $mapIdentity['Total'],
        ];
        if (!is_null($userId)) {
            $userInfo = $this->evalUserRepo->getUserByUserID(auth()->id());
            $opts->push([
                'type'  => 'self',
                'value' => $userId,
                'text'  => $mapIdentity['U'],
                'note'  => $mapIdentity['U'],
            ]);
        }
        $this->evalUserRepo->getUsers($tbaId, $enableGuestEvents)->each(function ($v) use ($opts, &$total, $mapIdentity) {
            $opts->push([
                'type'  => 'user',
                'value' => $v->id,
                'text'  => $v->name,
                'note'  => $mapIdentity[$v->identity],
            ]);
            array_push($total['value'], $v->id);
        });
        $opts[0] = $total;

        return $opts;
    }

    //
    public function getTbaInfo($tbaId, $type, $meta = null, $evaluateOptions = [])
    {
        $info = null;
        switch ($type) {
            case InfoType::AnalEvent:
                $info = $this->analEventRepo->getEvents($tbaId);
                break;
            case InfoType::EvalEvent:
                $enableGuestEvents  = isset($evaluateOptions['enableGuestEvents']) ? $evaluateOptions['enableGuestEvents'] : false;
                $enablePersonEvents = isset($evaluateOptions['enablePersonEvents']) ? $evaluateOptions['enablePersonEvents'] : false;
                $evalUsers          = $this->evalUserRepo->getUserByEvalUserIds($tbaId, $meta['evalUserIds'], $meta['evalModes'], $enableGuestEvents);
                $info               = $this->evalEventRepo->getEvents($tbaId, ['tba_evaluate_events.user_id' => NULL], $evalUsers);
                if ($enablePersonEvents && in_array(0, $meta['evalUserIds'])) {
                    $user     = $this->evalUserRepo->getUserByUserID(auth()->id());
                    $userInfo = $this->evalEventRepo->getEvents($tbaId, ['tba_evaluate_events.user_id' => auth()->id()], $user);
                    if (!empty($userInfo)) {
                        $userInfo[1]['user']['name'] = Lang::get('app/tba/identity')['U'];
                        $info[]                      = $userInfo[1];
                    }
                }
                break;
            case InfoType::EvalEventBySelf:
                $user = $this->evalUserRepo->getUserByUserID(auth()->id());
                $info = $this->evalEventRepo->getEvents($tbaId, ['tba_evaluate_events.user_id' => auth()->id()], $user);
                break;
            case InfoType::EvalEventByTotal:
                $enableGuestEvents  = isset($evaluateOptions['enableGuestEvents']) ? $evaluateOptions['enableGuestEvents'] : false;
                $enablePersonEvents = isset($evaluateOptions['enablePersonEvents']) ? $evaluateOptions['enablePersonEvents'] : false;
                $evalUsers          = $this->evalUserRepo->getUsers($tbaId, $enableGuestEvents);
                $info               = $this->evalEventRepo->getEvents($tbaId, ['tba_evaluate_events.user_id' => NULL], $evalUsers);
                if ($enablePersonEvents) {
                    $user     = $this->evalUserRepo->getUserByUserID(auth()->id());
                    $userInfo = $this->evalEventRepo->getEvents($tbaId, ['tba_evaluate_events.user_id' => auth()->id()], $user);
                    if (!empty($userInfo)) {
                        $userInfo[1]['user']['name'] = Lang::get('app/tba/identity')['U'];
                        $info[]                      = $userInfo[1];
                    }
                }
                break;
            case InfoType::TbaComment:
                $enablePersonEvents = isset($evaluateOptions['enablePersonEvents']) ? $evaluateOptions['enablePersonEvents'] : false; // my private comments
                $enableGuestEvents  = isset($evaluateOptions['enableGuestEvents']) ? $evaluateOptions['enableGuestEvents'] : true; // guests (showed by default)
                $info               = auth()->id()
                    ? $this->commentService->getTbaCommentsByUser($tbaId, auth()->id(), $enablePersonEvents, $enableGuestEvents)
                    : $this->commentService->getTbaComments($tbaId); // watch-as-open
                break;
            case InfoType::TechFuns:
                $info = $this->statRepo->getTechFuns($tbaId);
                break;
            /* 並未使用, 僅供參考, 依需求實作之
            case InfoType::FreqOfTechUsage:
                $info = Storage::get($this->pathTbaStatistic($tbaId, 'freq-of-tech-usage.png')); // 現階段資料來源是圖片未來會存取資料庫表格
                $info = 'data:image/png;base64,'.base64_encode($info);
                break;
            case InfoType::AccumTimeOnTechUsage:
                $info = Storage::get($this->pathTbaStatistic($tbaId, 'accum-time-on-tech-usage.png')); // 現階段資料來源是圖片未來會存取資料庫表格
                $info = 'data:image/png;base64,'.base64_encode($info);
                break;
            */
            case InfoType::TechInteractIdx:
                $info = $this->statRepo->getTechInteractIdx($tbaId);
                break;
            case InfoType::MethodAnal:
                $info = $this->statRepo->getMethodAnal($tbaId);
                break;
            case InfoType::ContentIdx:
                $info = $this->statRepo->getContentIdx($tbaId);
                break;
            default:
                throw new InvalidArgumentException('tba info type is wrong');
        }
        return $info;
    }

    //
    public function getTbaEventInfo($tbaId, $eventId, $type)
    {
        $info = null;
        switch ($type) {
            case InfoType::AnalEvent:
                //$info = $this->analEventRepo->getEvent($eventId);
                break;
            case InfoType::EvalEvent:
                $info = $this->evalEventRepo->getEvent($eventId);
                // 現階段資料來源均假設是圖片
//                $info['imgs'] = [];
//                $imgs         = $info['files']->filter(function ($v) {
//                    return $this->checkImgExt($v->ext);
//                });
//                foreach ($imgs as $img) {
//                    $ext = strtolower($img->ext);
//                    $ext = ($ext === 'jpg') ? 'jpeg' : $ext;
//                    $ctx = Storage::get($this->pathTbaEvalEventFile($tbaId) . $img->id);
//                    $ctx = 'data:image/' . $ext . ';base64,' . base64_encode($ctx);
//                    array_push($info['imgs'], $ctx);
//                }
                break;
            default:
                throw new InvalidArgumentException('tba event info type is wrong');
        }
        return $info;
    }

    //
    public function getTbaEvalEventMode($eventModeId)
    {
        return (new EvaluateEventModeRepository())->getEventMode($eventModeId);
    }

    //
    public function getTbaEvent($eventId, $type)
    {
        $event = null;
        switch ($type) {
            case InfoType::AnalEvent:
                $event = $this->tbaRepo->getAnalEvent($eventId);
                break;
            case InfoType::EvalEvent:
                $event = $this->tbaRepo->getEvalEvent($eventId);
                break;
            default:
                throw new InvalidArgumentException('tba event info type is wrong');
        }
        return $event;
    }

    //
    public function createTbaEvalEvent($userId, $tbaId, $eventMode, $event, $groupId = null, $files = null)
    {
        $event['group_id']                   = $groupId;
        $event['tba_evaluate_event_mode_id'] = $eventMode->id;

        if ($eventMode->identity === IdentityType::User) {
            $event['user_id'] = $userId;
        } else {
            $evalUser                      = $this->evalUserRepo->getUserOrCreate($tbaId, $userId, $eventMode->identity);
            $event['tba_evaluate_user_id'] = $evalUser->id;
        }
        return $this->evalEventRepo->createEvent($tbaId, $event, $files);
    }

    //
    public function updateTbaEvalEvent($eventId, $event, $files = null)
    {
        return $this->evalEventRepo->updateEvent($eventId, $event, $files);
    }

    //
    public function deleteTbaEvalEvent($eventId)
    {
        $this->evalEventRepo->deleteEvent($eventId);
    }

    //
    private function uploadThum($tbaId, $file, $fileName)
    {
        $path = $this->pathPublicTba($tbaId, null, false);
        return $file->storeAs($path, $fileName);
        /*
        $path = $this->pathTba($tbaId, null, false);
        $path = rtrim($path, '/'); // 補償:Laravel檔案儲存回傳路徑錯誤
        $path = $file->store($path);
        $path = storage_path($path);
        $info = pathinfo($path);
        $this->tbaRepo->setTba($tbaId, ['thumbnail' => $info['basename']]);
        */
    }

    //
    private function uploadCompressedFile($tbaId, $file)
    {
        $tarPath = $this->pathTba($tbaId, 'tmp', true);
        $zipper  = new ZipArchive;
        if ($zipper->open($file->path()) === TRUE) {
            $zipper->extractTo($tarPath);
            $zipper->close();
        }
    }

    //
    private function uploadStatImg($tbaId, $file)
    {
        $tarPath = $this->pathPublicTba($tbaId, null, true);
        $zipper  = new ZipArchive;
        if ($zipper->open($file->path()) === TRUE) {
            $zipper->extractTo($tarPath);
            $zipper->close();
        }
    }

    /**
     * 學科統計
     *
     * @param int $channelId
     * @param int $userId
     * @param null $ratingId
     * @return mixed
     */
    public function getSubjectCount(int $channelId, int $userId, $ratingId = null)
    {
        return $this->groupChannelContentRepository->getSubjectCount($channelId, $userId, $ratingId);
    }

    /**
     * 年級統計
     *
     * @param int $channelId
     * @param int $userId
     * @param null $group_subject_fields_id
     * @param null $ratingId
     * @return mixed
     */
    public function getGradeCount(int $channelId, int $userId, $group_subject_fields_id = null, $ratingId = null)
    {
        return $this->groupChannelContentRepository->getGradeCount($channelId, $userId, $group_subject_fields_id, $ratingId);
    }

    /**
     * @param int $channelId
     * @param int $userId
     * @return mixed
     */
    public function getRatingCount(int $channelId, int $userId)
    {
        return $this->groupChannelContentRepository->getRatingCount($channelId, $userId);
    }

    /**
     * 學區學科統計
     *
     * @param string $abbr
     * @param null $ratingId
     * @return mixed
     */
    public function getDistrictSubjectCount(string $abbr, $ratingId = null)
    {
        $groupIds     = GlobalPlatform::convertDistrictToGroupId($abbr);
        $districtInfo = GlobalPlatform::convertAbbrToDistrictInfo($abbr);

        return $this->districtChannelContentRepository->getSubjectCount($groupIds, $ratingId, $districtInfo->id);
    }

    /**
     * 學區年級統計
     *
     * @param string $abbr
     * @param null $group_subject_fields_id
     * @param null $ratingId
     * @return mixed
     */
    public function getDistrictGradeCount(string $abbr, $group_subject_fields_id = null, $ratingId = null)
    {

        $groupIds = GlobalPlatform::convertDistrictToGroupId($abbr);;
        $districtInfo = GlobalPlatform::convertAbbrToDistrictInfo($abbr);

        return $this->districtChannelContentRepository->getGradeCount($groupIds, $group_subject_fields_id, $ratingId, $districtInfo->id);
    }

    /**
     * 學區教研
     * @param string $abbr
     * @return mixed
     */
    public function getDistrictRatingCount(string $abbr)
    {
        $groupIds     = GlobalPlatform::convertDistrictToGroupId($abbr);
        $districtInfo = GlobalPlatform::convertAbbrToDistrictInfo($abbr);

        return $this->districtChannelContentRepository->getRatingCount($groupIds, $districtInfo->id);
    }

    /**
     * 學區篩選
     *
     * @param string $abbr
     * @param $resultFilter
     * @param bool $paginate
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getDistrictGroupByFilter(string $abbr, $resultFilter, $paginate = 0): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $groupIds = GlobalPlatform::convertDistrictToGroupId($abbr);;


        return $this->tbaRepo->getGroupByFilter($groupIds, null, null, null, $resultFilter, $paginate, true);

    }

    /**
     * @param string $abbr
     * @return array
     */
    public function getDistrictGroupChannelTotalByCount(string $abbr)
    {

        return $this->tbaRepo->getDistrictChannelByCount($abbr);
    }

    /**
     * TbaInfo for Observation
     * @param int $groupChannelId
     * @param int $contentId
     * @return \App\Models\Tba[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getObsrvTbaInfo(int $groupChannelId, int $contentId)
    {
        return $this->tbaRepo->getTbaInfo($groupChannelId, $contentId);
    }

    /**
     * 新增 頻道課例
     * @param int $tba_id
     * @param int $channel_id
     * @param array $attributes
     * @return bool
     */
    public function createGroupChannelContent(int $tba_id, int $channel_id, array $attributes): bool
    {
        return $this->tbaRepo->createGroupChannelContent($tba_id, $channel_id, $attributes);
    }

    /**
     * 刪除頻道課例
     * @param int $tba_id
     * @param int $channel_id
     * @return bool
     */
    public function deleteGroupChannelContent(int $tba_id, int $channel_id): bool
    {
        return $this->tbaRepo->deleteGroupChannelContent($tba_id, $channel_id);
    }

    /**
     * Delete private tba content
     * @param int $channelId
     * @param int $contentId
     * @return bool
     */
    public function deletePrivateGroupChannelContent(int $channelId, int $contentId): bool
    {
        return $this->tbaRepo->deletePrivateGroupChannelContent($channelId, $contentId);
    }

    /**
     * 取得頻道內 Video
     * @param int $channel_id
     * @param string $habook_id
     * @return \App\Models\Tba[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|string
     */
    public function getShareVideo(int $channel_id, string $habook_id)
    {
        return $this->tbaRepo->getShareVideos($channel_id, $habook_id);
    }

    /**
     * 上傳傳統影片
     * @param int $user_id
     * @param array $tbaData
     * @param array $resourceData
     * @param string $fileName
     * @param object $file
     * @param array $groupChannelDate
     * @return TbaRepository|false
     */
    public function uploadVideo(int $user_id, array $tbaData, array $resourceData, string $fileName, object $file, array $groupChannelDate)
    {

        try {
            $duration     = GlobalPlatform::getDuration($file);
            $tbaInfo      = $this->tbaRepo->createTba($user_id, $tbaData);
            $resourceInfo = $this->resourceRepository->createResrc($user_id, $resourceData);

            $rid = $tbaInfo->id . '/' . $tbaInfo->id . '.mp4'; //ex 9000/9000.mp4 (assume it's mp4)

            $rdata = [
                'source'    => getenv('BLOB_SOURCE'),
                'blob'      => $rid, //ex $rid
                'container' => getenv('BLOB_VIDEO_CONTAINER'),
                'file_size' => $file->getSize(), //ex 影片大小
                'duration'  => $duration, //ex 影片長度
            ];
            // Create Vod
            $resourceInfo->vod()->create([
                'type'    => VodType::AzureFile,
                'rid'     => $rid,
                'rstatus' => 'Normal',
                'rdata'   => json_encode($rdata),
            ]);

            // Create Videos
            $video = $tbaInfo->videos()->create([
                'user_id'        => $user_id,
                'resource_id'    => $resourceInfo->id,
                'name'           => $fileName,
                'description'    => null,
                'encoder'        => Encoder::FILE_UPLOAD,
                'tbavideo_order' => 1
            ]);

            // Update tba_video
            $tbaInfo->videos()->first()->pivot->update(['tbavideo_order' => 1]);

            // create tbaStatistics
            $tbaInfo->tbaStatistics()->create([
                'type' => 47,
                'idx'  => 0,
            ]);
            $tbaInfo->tbaStatistics()->create([
                'type' => 48,
                'idx'  => 0,
            ]);
            // Create tbaVideoMaps
            $tbaInfo->tbaVideoMaps()->create([
                'video_id'  => $video->id,
                'tba_start' => 0,
                'tba_end'   => $duration
            ]);


            $channelId = GlobalPlatform::convertGroupIdToChannelId($groupChannelDate['group_id']);
            $blob      = new Blob(getenv('BLOB_ACCOUNT'), getenv('BLOB_KEY'), getenv('ENDPOINT'));
            $blob->update($rid, getenv('BLOB_VIDEO_CONTAINER'), $file);
            $tbaInfo->groupChannels()->attach($channelId, $groupChannelDate);
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return false;
        }
        return $tbaInfo;


    }

    /**
     * Create message and send notification
     * Ex.) msg: array
     *  "channel_id": 4,
     *  "title": "2018-02-24 New video notification: : 4grade 語文 聽說讀寫，有策略！ (陳欣希)",
     *  "content": "远距同步公益智慧课堂 has a new video:\nLesson name：聽說讀寫，有策略！\nTeacher：陳欣希 (1537863264)\nSubject：語文\nGrade：4\nStudents：26\nStudent feedback：574 (22.1 per/person)\nLesson date：2018-02-24\n\nObservation data：\nObservers：1 person\nComments：4\n\nThe link of Sokrates lesson observation form :",
     *  "url": "http://192.168.0.178:5090/exhibition/tbavideo#/content/1698?groupIds=4&channelId=4",
     *  "isOperating": false,
     *  "top": false
     * @param int $tba_id
     * @param int $channel_id
     * @return bool
     */
    public function sendNotifyForCustom(int $tba_id, int $channel_id): bool
    {
        $isSuccessFul = true;
        try {
            // Get groupInfo
            $group_id  = GlobalPlatform::convertChannelIdToGroupId($channel_id);
            $groupInfo = $this->groupRepo->find($group_id);
            switch ($groupInfo->country_code) {
                case 886:
                    \App::setLocale('tw');
                    break;
                case 86:
                    \App::setLocale('cn');
                    break;
                default:
                    \App::setLocale('en');
            }

            // Create Message
            $tbaInfo     = $this->getObsrvTbaInfo($channel_id, $tba_id)->first();
            $commentInfo = collect(new TbaCommentObsrvCollection($this->commentService->getComments($tba_id, 1)));
            $message     = [
                'channel_id'  => $channel_id,
                'title'       => null,
                'content'     => null,
                'url'         => null,
                'isOperating' => false,
                'top'         => false,
            ];

            $message['title'] = __('app/video-upload-message.title', [
                'lecture_date' => $tbaInfo->lecture_date,
                'grade'        => $tbaInfo->grade,
                'subject'      => $tbaInfo->subject,
                'name'         => $tbaInfo->name,
                'teacher'      => $tbaInfo->teacher
            ]);

            $irsAvg             = !empty($tbaInfo->student_count) ? number_format(($tbaInfo->irs_count / $tbaInfo->student_count), 1) : 0;
            $observerCount      = count($commentInfo['observers']);
            $commentCount       = count($commentInfo['observerComments']);
            $message['content'] = __('app/video-upload-message.content', [
                'group_name'    => $groupInfo->name,
                'name'          => $tbaInfo->name,
                'teacher'       => $tbaInfo->teacher,
                'user'          => $tbaInfo->user,
                'subject'       => $tbaInfo->subject,
                'grade'         => $tbaInfo->grade,
                'student_count' => $tbaInfo->student_count,
                'irs_count'     => $tbaInfo->irs_count,
                'lecture_date'  => $tbaInfo->lecture_date,
                'observerCount' => $observerCount,
                'commentCount'  => $commentCount,
                'habook'        => $tbaInfo->user->habook,
                'irsAvg'        => $irsAvg
            ]);

            $message['url'] = url(getenv('APP_URL') . "/exhibition/tbavideo#/content/$tba_id?groupIds=$group_id&channelId=$channel_id");

            // For app notify
            $hiTeachMessage = json_encode([
                'content' => $message['content'],
                'action'  => [
                    [
                        'type'          => 'click',
                        'label'         => __('app/video-upload-message.click'),
                        'url'           => getenv('APP_URL') . '/exhibition/tbavideo/check-with-habook/?to=' . base64_encode($message['url']) . '&ticket=',
                        'tokenbindtype' => 1
                    ]
                ],
            ]);
            // TeamModel ID
            $haBook  = array_map(function ($item) {
                return $item['habook'];
            }, $commentInfo['observers']);
            $haBooks = array_filter(collect(array_merge($haBook, $groupInfo->users->pluck('habook')->toArray()))->unique()->values()->all());
            if ((int)$groupInfo->public === 0 && (int)$groupInfo->review_status === 0) {
                User::query()->whereIn('habook', $haBooks)
                    ->each(function ($user) use ($message) {
                        $user->notify(new EventChannel($message));
                    });
                // send app notify
                $this->sendNotify($haBooks, $hiTeachMessage, $message['title']);
            }
        } catch (\Exception $exception) {
            \Log::debug('notify', [$exception->getMessage()]);
            $isSuccessFul = false;
        }

        return $isSuccessFul;
    }

    /**
     * 更新傳統影片
     * @param int $user_id
     * @param int $tba_id
     * @param array $resourceData
     * @param string $fileName
     * @param object $file
     * @return false|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|Tba|Tba[]|null
     */
    public function updateVideo(int $user_id, int $tba_id, array $resourceData, string $fileName, object $file)
    {
        try {
            $duration     = GlobalPlatform::getDuration($file);
            $tbaInfo      = $this->tbaRepo->getTba($tba_id);
            $resourceInfo = $tbaInfo->videos()->first()->resource;
            $this->resourceRepository->update($resourceInfo->id, $resourceData);
            $rid = $tbaInfo->id . '/' . $tbaInfo->id . '.mp4'; //ex 9000/9000.mp4 (assume it's mp4)

            $rdata = [
                'source'    => getenv('BLOB_SOURCE'),
                'blob'      => $rid, //ex $rid
                'container' => getenv('BLOB_VIDEO_CONTAINER'),
                'file_size' => $file->getSize(), //ex 影片大小
                'duration'  => $duration, //ex 影片長度
            ];
            // Create Vod
            $resourceInfo->vod()->updateOrCreate([
                'resource_id' => $resourceInfo->id,
            ], [
                'type'    => VodType::AzureFile,
                'rid'     => $rid,
                'rstatus' => 'Normal',
                'rdata'   => json_encode($rdata),
            ]);
            // Create Videos
            $video = $tbaInfo->videos()->updateOrCreate([
                'user_id'     => $user_id,
                'resource_id' => $resourceInfo->id,
            ],
                [
                    'name'           => $fileName,
                    'description'    => null,
                    'encoder'        => Encoder::FILE_UPLOAD,
                    'tbavideo_order' => 1
                ]
            );
            // Update tba_video
            $tbaInfo->videos()->first()->pivot->update(['tbavideo_order' => 1]);
            // create tbaStatistics
            $tbaInfo->tbaStatistics()->updateOrCreate([
                'type' => 47,
            ],
                [
                    'idx' => 0,
                ]
            );
            $tbaInfo->tbaStatistics()->updateOrCreate([
                'type' => 48,
            ],
                [
                    'idx' => 0,
                ]
            );
            // Create tbaVideoMaps
            $tbaInfo->tbaVideoMaps()->updateOrCreate([
                'video_id' => $video->id,
            ], [
                'tba_start' => 0,
                'tba_end'   => $duration
            ]);

            $blob = new Blob(getenv('BLOB_ACCOUNT'), getenv('BLOB_KEY'), getenv('ENDPOINT'));
            $blob->update($rid, getenv('BLOB_VIDEO_CONTAINER'), $file);
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return false;
        }
        return $tbaInfo;
    }

    /**
     * Update Tba TimePoints based on the given tba id
     * Desc: update time_points on tba_comments, tba_evaluate_events
     * @param int $tbaId
     * @param int $timePoint
     * @param string $mode
     * @return bool
     */
    public function updateTbaTimePoints(int $tbaId, int $timePoint, string $mode = 'inc'): bool
    {
        $isSuccessful = false;

        try {
            if (!is_numeric($timePoint)) return $isSuccessful;
            if (!in_array($mode, ['inc', 'dec'])) return $isSuccessful;
            $isSuccessful = $this->commentService->updateTbaCommentTimePoints($tbaId, $timePoint, $mode);
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
        }

        return $isSuccessful;
    }
}
