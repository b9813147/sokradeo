<?php

namespace App\Http\Controllers\Api\V1\TbaVideos;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
use App\Http\Resources\Api\V1\TbaVideo\SectMapResource;
use App\Http\Transformers\Api\V1\TbaVideo\SectMapTransformer;
use App\Services\Cms\TbaService;
use App\Services\Cms\TbaVideoService;

class SectMapController extends Controller
{
    private $tbaVideoSrv = null;
    
    //
    public function __construct(TbaVideoService $tbaVideoSrv)
    {
        $this->module      = ['cate' => 'TbaVideos', 'app' => 'SectMap'];
        $this->tbaVideoSrv = $tbaVideoSrv;
    }
    
    //
    public function index()
    {
        
        return $this->success(['message' => 'index']);
        
    }
    
    //
    public function store(Request $req, TbaService $tbaSrv)
    {
        $this->validate($req, [
                'sectMap' => 'required',
        ]);

        $tbaId = $req->tbaId;
        
        $tba = $tbaSrv->getTba($tbaId);
        
//        $this->authorize('update', $tba); // 說明:暫時使用此規則驗證, 因為沒時間修正
        
        //$this->authorize('updateTbaVideo', [$tbaVideo['tba'], $tbaVideo['videos']]); // 說明:正確應該使用此規則驗證, 因為沒時間修正
        
        (new SectMapTransformer($req))->execute();
        
        $this->tbaVideoSrv->createTbaVideoSectMap($tbaId, $req->sectMap);
        $sectMap = $this->tbaVideoSrv->getTbaVideoSectMap($tbaId);
        
        return $this->success(new SectMapResource($sectMap));
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
