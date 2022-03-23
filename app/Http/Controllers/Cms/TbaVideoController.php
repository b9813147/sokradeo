<?php

namespace App\Http\Controllers\Cms;

use App\Services\Group\Content\TbaService as TbaContentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Controller;
use App\Models\TbaEvaluateEvent;
use App\Services\Cms\TbaService;
use App\Services\Cms\TbaVideoService;
use App\Services\Cms\VideoService;
use App\Types\Tba\InfoType;

class TbaVideoController extends Controller
{
    private $tbaVideoSrv = null;
    private $tbaContentSrv = null;

    //
    public function __construct(TbaVideoService $tbaVideoSrv, TbaContentService $tbaContentSrv)
    {
        $this->module      = ['cate' => 'Cms', 'app' => 'TbaVideo'];
        $this->tbaVideoSrv = $tbaVideoSrv;
        $this->tbaContentSrv = $tbaContentSrv;
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
    public function watch(Request $req, TbaService $tbaSrv)
    {
        $userId = auth()->id();

        $tbaId          = $req->id;
        $appointedStart = $req->start;

        $tbaVideo = $this->tbaVideoSrv->getTbaVideo($tbaId);

        $this->authorize('view', $tbaVideo['tba']);

        $tbaPlayerInfo = [
            'info'          => $tbaVideo['tba'],
            'playlist'      => $this->tbaVideoSrv->getTbaVideoPlaylist($tbaVideo),
            'appointedTime' => ['start' => intval($appointedStart)],
            'evaluateOptions'   => [
                'hasGuestEventAuth'  => false,
                'hasPersonEventAuth' => isset($userId) ? true : false,
                'enableGuestEvents'  => false,
                'enablePersonEvents' => false
            ]
        ];

        $identityEventModesSet = $tbaSrv->getTbaIdentityEvalEventModesSet($tbaVideo['tba'], $userId);

        $tbaPlayerOpts = [
            'cpnts' => [
                    'tools'          => true,
                    'videoPlayer'    => [
                            'ezStation' => [
                                    'hiddens' => ['starMark', 'hardMark'],
                            ],
                    ],
                    'chartEvalEvent' => [
                            'identities'     => $identityEventModesSet->pluck('identity'  ),
                            'eventModesList' => $identityEventModesSet->pluck('eventModes'),
                    ],
            ],
        ];

        $modulePath = $this->parseModulePath($this->module, 'watch');
        $configPath = $this->parseConfigPath($this->module, 'import');

        // 本地播放範例
        /* 本地播放範例
        $tbaVideo['videos'] = [
            [
                'status'    => 'normal',
                'duration'  => 2462.802,
                'thumbnail' => 'http://vod.habook.com.cn/snapshot/c9131aba3740455d960d32eadcb26aee00005.jpg',
                'list'      => [
                    [
                        'format' => 'mp4',
                        'width'  => 640,
                        'height' => 360,
                        'label'  => 'LOCAL',
                        'mime'   => 'video/mp4',
                        'size'   => 164033572,
                        'url'    => 'http://seenew.com.tw/movie.mp4',
                    ]
                ],
            ]
        ];
        $tbaVideo['videoSrcType'] = 'local';
        */
        //----------------------------------

        $data = [
                'module'  => $modulePath,
                'imports' => Config::get($configPath)['watch'],
                'globals' => [
                        'tbaPlayerInfo' => $tbaPlayerInfo,
                        'tbaPlayerOpts' => $tbaPlayerOpts,
                ],
        ];

        return view($modulePath, $data);
    }

    //---test
    // 蘇格拉底播放器:開發函式庫初期測試使用, 已停止維護
    public function watchByCdn(Request $req)
    {
        // 蘇格拉底播放器:開發函式庫初期測試使用, 已停止維護
        //return '蘇格拉底播放器:開發函式庫初期測試使用, 已停止維護';
        //------------------------------------------

        $tbaId = $req->id;
        $appointedStart = $req->start;

        $tbaVideo = $this->tbaVideoSrv->getTbaVideo($tbaId);

        $this->authorize('view', $tbaVideo['tba']);

        $tbaPlayerInfo = [
            'info'     => $tbaVideo['tba'],
            'playlist' => $this->tbaVideoSrv->getTbaVideoPlaylist($tbaVideo),
            'appointedTime' => ['start' => intval($appointedStart)],
            'evaluateOptions'   => [
                'hasGuestEventAuth'  => false,
                'hasPersonEventAuth' => isset($userId) ? true : false,
                'enableGuestEvents'  => false,
                'enablePersonEvents' => false
            ]
        ];

        $tbaPlayerOpts = [
                'cpnts' => [
                        'tools'          => true,
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

        $modulePath = $this->parseModulePath($this->module, 'watchByCdn');
        $configPath = $this->parseConfigPath($this->module, 'import');

        $data = [
                'module'  => $modulePath,
                'imports' => Config::get($configPath)['watchByCdn'],
                'globals' => [
                        'tbaPlayerInfo' => $tbaPlayerInfo,
                        'tbaPlayerOpts' => $tbaPlayerOpts,
                ],
        ];

        return view($modulePath, $data);
    }
    //---test

    //
    public function list(Request $req)
    {
        $userId = auth()->id();

        $page = $req->input('page', 1);

        $result = $this->tbaVideoSrv->listByUserId($userId, $page);

        return Response::json([
                'status' => true,
                'data'   => $result
        ]);
    }

    //
    public function getTbaVideoSectMap(Request $req)
    {
        $tbaId = $req->tbaId;

        $tbaVideo = $this->tbaVideoSrv->getTbaVideo($tbaId);

        $this->authorize('view', $tbaVideo['tba']);

        $result = $this->tbaVideoSrv->getTbaVideoSectMap($tbaVideo['tba']->id);

        return Response::json([
                'status' => true,
                'data'   => $result
        ]);
    }

    //
    public function setTbaVideoMaps(Request $req)
    {
        $this->validate($req, [
                'tbaId' => 'required',
                'maps'  => 'required',
        ]);

        $tbaId = $req->tbaId;
        $maps  = $req->maps;

        $tbaVideo = $this->tbaVideoSrv->getTbaVideo($tbaId);

        $this->authorize('update', $tbaVideo['tba']);

        $this->tbaVideoSrv->setTbaVideoMaps($tbaVideo['tba']->id, $maps);
        $result = $this->tbaVideoSrv->getTbaVideoMaps($tbaVideo['tba']->id);

        return Response::json([
                'status' => true,
                'data'   => $result
        ]);
    }

    //
    public function getTbaEvalEventOpts(Request $req, TbaService $tbaSrv)
    {
        $userId = auth()->id();

        $tbaId = $req->tbaId;

        $tba = $tbaSrv->getTba($tbaId);

        $this->authorize('view', $tba);

        $result = $tbaSrv->getTbaEvalEventOpts($tba->id, $userId);

        return Response::json([
                'status' => true,
                'data'   => $result
        ]);
    }

    //
    public function getTbaInfo(Request $req, TbaService $tbaSrv)
    {
        $tbaId = $req->tbaId;
        $type  = $req->type;
        $meta  = $req->input('meta', null);
        $meta  = is_null($meta) ? null : json_decode($meta, true);

        $tba = $tbaSrv->getTba($tbaId);

        $this->authorize('view', $tba);

        $result = $tbaSrv->getTbaInfo($tba->id, $type, $meta);

        return Response::json([
                'status' => true,
                'data'   => $result
        ]);
    }

    //
    public function getTbaEventInfo(Request $req, TbaService $tbaSrv)
    {
        $tbaId   = $req->tbaId;
        $eventId = $req->eventId;
        $type    = $req->type;

        $tba = $tbaSrv->getTba($tbaId);

        $this->authorize('view', $tba);

        $result = $tbaSrv->getTbaEventInfo($tba->id, $eventId, $type);

        $event = $tbaSrv->getTbaEvent($eventId, $type);
        $meta  = [];
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

        $tbaId       = $req->tbaId;
        $eventModeId = $req->eventModeId;
        $req->event  = (gettype($req->event) === 'string') ? json_decode($req->event, true) : $req->event;
        $event = [
                'time_point' => $req->event['time'],
                'text'       => $req->event['text'],
        ];

        $tba = $tbaSrv->getTba($tbaId);
        $this->authorize('view', $tba);

        $eventMode = $tbaSrv->getTbaEvalEventMode($eventModeId);
        $this->authorize('create', [TbaEvaluateEvent::class, $eventMode, $tba]);

        $files = null;
        if (isset($_FILES['image'])) {
            $files = [];
            $files[] = [
                'name'      => pathinfo($_FILES['image']['name'], PATHINFO_FILENAME),
                'ext'       => pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION),
                'path'      => $_FILES['image']['tmp_name'],
                'image_url' => null
            ];
        }

        $result = $tbaSrv->createTbaEvalEvent($userId, $tbaId, $eventMode, $event, $files);

        return Response::json([
                'status' => true,
                'data'   => $result
        ]);
    }

    public function updateTbaEvalEvent(Request $req, TbaService $tbaSrv)
    {
        $eventId     = $req->eventId;
        $req->event  = (gettype($req->event) === 'string') ? json_decode($req->event, true) : $req->event;
        $event       = [
            'text' => $req->event['text'],
        ];

        $files = null;
        if (isset($_FILES['image'])) {
            $files = [];
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
        $tbaId   = $req->tbaId;
        $eventId = $req->eventId;

        $event = $tbaSrv->getTbaEvent($eventId, InfoType::EvalEvent);
        $this->authorize('delete', $event);

        $tbaSrv->deleteTbaEvalEvent($eventId);

        return Response::json(['status' => true]);
    }

    //
    public function getVideoInfo(Request $req, TbaService $tbaSrv, VideoService $videoSrv)
    {
        $tbaId   = $req->tbaId;
        $videoId = $req->videoId;

        $tba = $tbaSrv->getTba($tbaId);

        $this->authorize('view', $tba);

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
