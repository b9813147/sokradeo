<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AboutController extends Controller
{
    //
    public function __construct()
    {
        $this->module = ['cate' => 'Home', 'app' => 'About'];
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
