<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    protected $module = null; //['cate' => null, 'app' => null];
    
    protected function permitModule($module) // 待修正成api controller request可用
    {
        if(is_null($module)) {
            return;
        }
        
        $this->middleware('permit-module:'.$module['cate'].','.$module['app']);
    }
    
    protected function parseModulePath($module, $extra = null)
    {
        if(is_null($module)) {
            return false;
        }
        
        return strtolower($module['cate'].'/'.$module['app'].(is_null($extra) ? '' : '/'.$extra));
    }
    
    protected function parseConfigPath($module, $extra = null)
    {
        if(is_null($module)) {
            return false;
        }
        
        return 'apps.'.strtolower($module['cate'].'.'.$module['app'].(is_null($extra) ? '' : '.'.$extra));
    }
}
