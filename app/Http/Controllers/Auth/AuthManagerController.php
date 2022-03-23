<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\Controller;

class AuthManagerController extends Controller
{
    //
    public function __construct()
    {
        
    }
    
    //
    public function index(Request $req)
    {
        $userId = $req->input('id');
        
        auth()->loginUsingId($userId);
        $modules = Config::get('module_role.Manager');
        $req->session()->put('modules', $modules);
        $test = $req->session()->get('modules');
        return 'Manager authed: ID is '.$userId;
    }
}
