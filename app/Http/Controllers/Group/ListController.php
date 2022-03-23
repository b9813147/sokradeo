<?php

namespace App\Http\Controllers\Group;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Controller;
use App\Services\Group\GroupService;

class ListController extends Controller
{
    private $groupSrv = null;
    
    //
    public function __construct(GroupService $groupSrv)
    {
        $this->module = ['cate' => 'Group', 'app' => 'List'];
        $this->permitModule($this->module);
        $this->groupSrv = $groupSrv;
    }
    
    //
    public function index(Request $req)
    {
        $modulePath = $this->parseModulePath($this->module);
        
        $data = [
                'module' => $modulePath
        ];
        
        return view($modulePath.'/index', $data);
    }
    
    //
    public function list(Request $req)
    {
        $userId = auth()->id();
        
        $page = $req->input('page', 1);
        
        $result = $this->groupSrv->list($userId, $page);
        
        return Response::json([
                'status' => true,
                'data'   => $result
        ]);
    }
    
}
