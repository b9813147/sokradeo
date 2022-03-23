<?php

namespace App\Http\Controllers\Management;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Controller;
use App\Services\Management\RoleService;

class RoleController extends Controller
{
    private $roleSrv = null;
    
    //
    public function __construct(RoleService $roleSrv)
    {
        $this->module = ['cate' => 'Management', 'app' => 'Role'];
        $this->permitModule($this->module);
        $this->roleSrv = $roleSrv;
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
        $page = $req->input('page', 1);
        
        $result = $this->roleSrv->list($page);
        
        return Response::json([
            'status' => true,
            'data'   => $result
        ]);
    }
    
    //
    public function getRole(Request $req)
    {
        $roleId = $req->roleId;
        
        $result = $this->roleSrv->getRole($roleId);
        
        return Response::json([
            'status' => true,
            'data'   => $result
        ]);
    }
    
    //
    public function createRole(Request $req)
    {
        $params = ['type', 'name', 'description'];
        
        if(! $req->filled($params)) {
            return Response::json([
                'status' => false
            ]);
        }
        
        $role = $req->only($params);
        
        $result = $this->roleSrv->createRole($role);
        
        return Response::json([
            'status' => true,
            'data'   => $result
        ]);
    }
}
