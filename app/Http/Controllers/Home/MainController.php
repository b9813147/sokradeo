<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MainController extends Controller
{
    //
    public function __construct()
    {
        $this->module = ['cate' => 'Home', 'app' => 'Main'];
    }
    
    //
    public function index()
    {
        $modulePath = $this->parseModulePath($this->module);
        
        $data = [
            'module' => $modulePath
        ];
        
        return view($modulePath, $data);
    }
}
