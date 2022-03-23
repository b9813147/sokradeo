<?php

namespace App\Http\Controllers\Cms;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Controller;
use App\Services\Cms\VideoService;

class VideoController extends Controller
{
    private $videoSrv = null;
    
    //
    public function __construct(VideoService $videoSrv)
    {
        $this->module = ['cate' => 'Cms', 'app' => 'Video'];
        $this->videoSrv = $videoSrv;
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
    public function watch(Request $req)
    {
        $videoId = $req->id;
        
        $video = $this->videoSrv->getVideo($videoId);
        
        $this->authorize('view', $video);
        
        $exeInfo   = $this->videoSrv->getExeInfo($video->id);
        $ezStation = $this->videoSrv->getEzStationInfo($video->id);
        
        $modulePath = $this->parseModulePath($this->module, 'watch');
        
        $data = [
            'module'  => $modulePath,
            'exeInfo' => $exeInfo,
            'globals' => [
                    'ezStation' => $ezStation,
            ],
        ];
        
        return view($modulePath, $data);
    }
    
    //
    public function list(Request $req)
    {
        $userId = auth()->id();
        
        $page = $req->input('page', 1);
        
        $result = $this->videoSrv->list($userId, $page);
        
        return Response::json([
            'status' => true,
            'data'   => $result
        ]);
    }
}
