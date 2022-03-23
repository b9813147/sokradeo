<?php

namespace App\Http\Controllers\Management;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Controller;
use App\Services\Management\ModuleService;

class ModuleController extends Controller
{
    private $moduleSrv = null;
    
    //
    //
    public function __construct(ModuleService $moduleSrv)
    {
        $this->module = ['cate' => 'Management', 'app' => 'Module'];
        $this->permitModule($this->module);
        $this->moduleSrv = $moduleSrv;
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
    public function info()
    {
        $modulePath = $this->parseModulePath($this->module, 'info');
        
        $data = [
            'module' => $modulePath
        ];
        
        return view($modulePath, $data);
    }
    
    //
    public function manage()
    {
        $modulePath = $this->parseModulePath($this->module, 'manage');
        
        $data = [
            'module' => $modulePath
        ];
        
        return view($modulePath, $data);
    }
    
    //
    public function list(Request $req)
    {
        $result = $this->moduleSrv->list();
        
        return Response::json([
            'status' => true,
            'data'   => $result
        ]);
    }
}
