<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;

class PermitModule
{
    private $illegalRoute = 'home.main';

    //
    public function handle($request, Closure $next, $cate, $app)
    {
        $modules = Config::get('modules', false);
        if (!$modules) {
            return 'Error';
        }

        if (!isset($modules[$cate][$app]) || !$modules[$cate][$app]) {
            return redirect()->route($this->illegalRoute);
        }
        
        $modules = $request->session()->get('modules', false);
        if (!$modules || !isset($modules[$cate])) {
            return redirect()->route($this->illegalRoute);
        }

        if (is_array($modules[$cate])) {
            $cates = collect($modules[$cate]);
            if (!$cates->contains($app)) {
                return redirect()->route($this->illegalRoute);
            }
        } else if (!$modules[$cate]) { // boolean
            return redirect()->route($this->illegalRoute);
        }

        return $next($request);
    }
}
