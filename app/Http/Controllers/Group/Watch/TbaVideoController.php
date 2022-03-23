<?php

namespace App\Http\Controllers\Group\Watch;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Controller;
use App\Models\TbaEvaluateEvent;
use App\Services\Cms\TbaService;
use App\Services\Cms\TbaVideoService;
use App\Services\Cms\VideoService;
use App\Services\Group\ContentService;
use App\Services\Group\Content\TbaService as TbaContentService;
use App\Services\CommentService; 
use App\Types\Cms\CmsType;
use App\Types\Tba\InfoType;

class TbaVideoController extends Controller
{
    private $contentSrv = null;
    private $tbaContentSrv = null;
    private $commentService = null;

    //
    public function __construct(ContentService $contentSrv, TbaContentService $tbaContentSrv, CommentService $commentService)
    {
        $this->module = ['cate' => 'Group', 'app' => 'Watch'];
        $this->permitModule($this->module);
        $this->contentSrv    = $contentSrv;
        $this->tbaContentSrv = $tbaContentSrv;
        $this->commentService = $commentService;
    }

    //
    public function index(Request $req, TbaVideoService $tbaVideoSrv)
    {
        $userId = auth()->id();

        $groupId        = $req->groupId;
        $channelId      = $req->channelId;
        $contentId      = $req->contentId;
        $appointedStart = $req->start;

        $content = $this->contentSrv->getContent($channelId, $contentId, CmsType::Tba);
        $this->authorize('view', $content);
        $url      = urldecode($req->getUri());
        $tbaVideo = $tbaVideoSrv->getTbaVideo($contentId);
        $tbaVideoSrv->hitTbaVideo($contentId, $userId, $url);

        $hasGuestComments = $this->commentService->hasGuestComments($contentId);
        $tbaPlayerInfo = [
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

        $identityEventModesSet = $this->tbaContentSrv->getTbaIdentityEvalEventModesSet($tbaVideo['tba'], $groupId, $userId);

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

        $modulePath = $this->parseModulePath($this->module, 'tbavideo');
        $configPath = $this->parseConfigPath($this->module, 'tbavideo.import');

        $data = [
            'module'  => $modulePath,
            'imports' => Config::get($configPath)['index'],
            'globals' => [
                'tbaPlayerInfo' => $tbaPlayerInfo,
                'tbaPlayerOpts' => $tbaPlayerOpts,
                'path'          => [
                    'tbaPlayerApiSrv' => $req->url() . '/',
                ],
            ],
        ];

        return view($modulePath, $data);
    }

    //
    public function getTbaVideoSectMap(Request $req, TbaVideoService $tbaVideoSrv)
    {
        $groupId   = $req->groupId;
        $channelId = $req->channelId;
        $contentId = $req->contentId;

        $content = $this->contentSrv->getContent($channelId, $contentId, CmsType::Tba);
        $this->authorize('view', $content);

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

    //
    public function getTbaEvalEventOpts(Request $req, TbaService $tbaSrv)
    {
        $userId = auth()->id();

        $groupId         = $req->groupId;
        $channelId       = $req->channelId;
        $contentId       = $req->contentId;
        $evaluateOptions = $req->evaluateOptions;
        $evaluateOptions = is_null($evaluateOptions) ? [] : json_decode($evaluateOptions, true);

        $content = $this->contentSrv->getContent($channelId, $contentId, CmsType::Tba);
        $this->authorize('view', $content);

        $result = $tbaSrv->getTbaEvalEventOpts($contentId, $userId, $evaluateOptions);

        return Response::json([
            'status' => true,
            'data'   => $result
        ]);
    }

    //
    public function getTbaInfo(Request $req, TbaService $tbaSrv)
    {
        $groupId         = $req->groupId;
        $channelId       = $req->channelId;
        $contentId       = $req->contentId;
        $type            = $req->type;
        $meta            = $req->input('meta', null);
        $evaluateOptions = $req->input('evaluateOptions', null);
        $meta            = is_null($meta) ? null : json_decode($meta, true);
        $evaluateOptions = is_null($evaluateOptions) ? null : json_decode($evaluateOptions, true);

        $content = $this->contentSrv->getContent($channelId, $contentId, CmsType::Tba);
        $this->authorize('view', $content);

        $result = $tbaSrv->getTbaInfo($contentId, $type, $meta, $evaluateOptions);

        return Response::json([
            'status' => true,
            'data'   => $result
        ]);
    }

    //
    public function getTbaEventInfo(Request $req, TbaService $tbaSrv)
    {
        $groupId   = $req->groupId;
        $channelId = $req->channelId;
        $contentId = $req->contentId;
        $eventId   = $req->eventId;
        $type      = $req->type;

        $content = $this->contentSrv->getContent($channelId, $contentId, CmsType::Tba);
        $this->authorize('view', $content);

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

    // create Evaluation event
    public function createTbaEvalEvent(Request $req, TbaService $tbaSrv)
    {
        $userId = auth()->id();

        $groupId     = $req->groupId;
        $channelId   = $req->channelId;
        $contentId   = $req->contentId;
        $eventModeId = $req->eventModeId;
        $req->event  = (gettype($req->event) === 'string') ? json_decode($req->event, true) : $req->event;
        $event       = [
            'time_point' => $req->event['time'],
            'text'       => $req->event['text'],
        ];

        $content = $this->contentSrv->getContent($channelId, $contentId, CmsType::Tba);
        $this->authorize('view', $content);

        $tba       = $content->content;
        $eventMode = $tbaSrv->getTbaEvalEventMode($eventModeId);
        $this->authorize('createInGroup', [TbaEvaluateEvent::class, $eventMode, $tba, $groupId]);

        $files = null;
        if (isset($_FILES['fileUpload'])) {
            $files   = [];
            $files[] = [
                'name'      => pathinfo($_FILES['fileUpload']['name'], PATHINFO_FILENAME),
                'ext'       => pathinfo($_FILES['fileUpload']['name'], PATHINFO_EXTENSION),
                'path'      => $_FILES['fileUpload']['tmp_name'],
                'image_url' => null,
                'media_url' => null
            ];
        }

        $result = $tbaSrv->createTbaEvalEvent($userId, $contentId, $eventMode, $event, $groupId, $files);

        return Response::json([
            'status' => true,
            'data'   => $result
        ]);
    }

    // update Evaluation event
    public function updateTbaEvalEvent(Request $req, TbaService $tbaSrv)
    {
        $eventId    = $req->eventId;
        $req->event = (gettype($req->event) === 'string') ? json_decode($req->event, true) : $req->event;
        $event      = [
            'text' => $req->event['text'],
        ];

        $files = null;
        if (isset($_FILES['fileUpload'])) {
            $files   = [];
            $files[] = [
                'name'      => pathinfo($_FILES['fileUpload']['name'], PATHINFO_FILENAME),
                'ext'       => pathinfo($_FILES['fileUpload']['name'], PATHINFO_EXTENSION),
                'path'      => $_FILES['fileUpload']['tmp_name'],
                'image_url' => null,
                'media_url' => null
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
        $groupId   = $req->groupId;
        $channelId = $req->channelId;
        $contentId = $req->contentId;
        $videoId   = $req->videoId;

        $content = $this->contentSrv->getContent($channelId, $contentId, CmsType::Tba);
        $this->authorize('view', $content);

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
}
