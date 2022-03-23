<?php

namespace App\Http\Controllers\Cms;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Lang;
use App\Http\Controllers\Controller;
use App\Services\Cms\TbaService;
use App\Services\Group\Content\TbaService as TbaContentService;
use App\Services\CommentService as TbaCommentService;
use App\Exports\EvaluateEventsExport;
use App\Exports\TbaCommentsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Tba;

class TbaController extends Controller
{
    private $tbaSrv = null;
    private $tbaContentSrv = null;
    private $tbaCommentSrv = null;
    
    //
    public function __construct(TbaService $tbaSrv, TbaContentService $tbaContentSrv, TbaCommentService $tbaCommentSrv)
    {
        $this->module        = ['cate' => 'Cms', 'app' => 'Tba'];
        $this->tbaSrv        = $tbaSrv;
        $this->tbaContentSrv = $tbaContentSrv;
        $this->tbaCommentSrv = $tbaCommentSrv;
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
        $tbaId = $req->id;
        
        $tba = $this->tbaSrv->getTba($tbaId);
        
        $this->authorize('view', $tba);
        
        $modulePath = $this->parseModulePath($this->module, 'watch');
        $configPath = $this->parseConfigPath($this->module, 'import');
        
        $data = [
                'module'  => $modulePath,
                'imports' => Config::get($configPath)['watch'],
        ];
        
        return view($modulePath, $data);
    }
    
    //
    public function list(Request $req)
    {
        $userId = auth()->id();
        
        $page = $req->input('page', 1);
        
        $result = $this->tbaSrv->list($userId, $page);
        
        return Response::json([
                'status' => true,
                'data'   => $result
        ]);
    }
    
    //
    public function getTbaInfo(Request $req)
    {
        $tbaId = $req->tbaId;
        $type  = $req->type;
        
        $tba = $this->tbaSrv->getTba($tbaId);
        
        $this->authorize('view', $tba);
        
        $this->tbaSrv->getTbaInfo($tba->id, $type);
    }
    
    public function exportTbaEvaluateEvents(Request $req)
    {
        $query                      = Tba::query()->findOrFail($req->contentId);
        $tba_name                   = addslashes($query->toArray()['name']);
        $file_name                  = Lang::get('app/tba/evaluate-event-export')['file-name'];
        $user_id                    = auth()->id();
        $tba_id                     = $req->contentId;
        $group_ids                  = $req->groupIds;
        $tba                        = $this->tbaSrv->getTba($tba_id);
        $data                       = $req->all();
        $data['enableGuestEvents']  = ($this->tbaContentSrv->checkGuestEvaluateEventAuth($tba, explode(',', $group_ids)[0], $user_id)) ? filter_var($req->enableGuestEvents, FILTER_VALIDATE_BOOLEAN) : false;
        $data['enablePersonEvents'] = isset($req->enablePersonEvents) ? filter_var($req->enablePersonEvents, FILTER_VALIDATE_BOOLEAN) : false;
        return Excel::download(new EvaluateEventsExport($data), "{$file_name}_{$tba_name}.xlsx");
    }

    public function exportTbaComments(Request $req)
    {
        // Data for transformation
        $data = [
            'contentId' => $req->contentId,
            'groupIds' => $req->groupIds,
            'channelId' => $req->channelId,
            'tbaComments' => $this->tbaCommentSrv->getTbaCommentsByUser(
                $req->contentId,
                auth()->id(),
                $req->enablePersonEvents,
                $req->enableGuestEvents
            )
        ];

        // File name
        $tbaModel = Tba::query()->findOrFail($req->contentId);
        $tbaName = addslashes($tbaModel->toArray()['name']);
        $fileName = Lang::get('app/tba/evaluate-event-export')['file-name'];

        return Excel::download(new TbaCommentsExport($data), "{$fileName}_{$tbaName}.xlsx");
    }
}
