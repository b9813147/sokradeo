<?php

namespace App\Http\Controllers\Management;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Controller;
use App\Services\Management\RoleService;
use App\Services\Management\UserService;

class UserController extends Controller
{
    private $userSrv = null;
    
    //
    public function __construct(UserService $userSrv)
    {
        $this->module = ['cate' => 'Management', 'app' => 'User'];
        $this->permitModule($this->module);
        $this->userSrv = $userSrv;
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
    public function manage(RoleService $roleSrv)
    {
        $modulePath = $this->parseModulePath($this->module, 'manage');
        $roles      = $roleSrv->getRoles();
        
        $data = [
            'module' => $modulePath,
            'roles'  => $roles,
        ];
        
        return view($modulePath, $data);
    }
    
    //
    public function list(Request $req)
    {
        $page = $req->input('page', 1);
        
        $result = $this->userSrv->list($page);
        
        return Response::json([
            'status' => true,
            'data'   => $result
        ]);
    }
    
    //
    public function getUser(Request $req)
    {
        $userId = $req->userId;
        
        $result = $this->userSrv->getUser($userId);
        
        return Response::json([
            'status' => true,
            'data'   => $result
        ]);
    }
    
    //
    public function setUser(Request $req)
    {
        if(! $req->filled('id')) {
            return Response::json([
                'status' => false
            ]);
        }
        
        $userId   = $req->id;
        $userData = $req->only(['name']);
        $roles    = $req->roles;
        
        $this->userSrv->setUser($userId, $userData, $roles);
        $result = $this->userSrv->getUser($userId);
        
        return Response::json([
            'status' => true,
            'data'   => $result
        ]);
    }
}
