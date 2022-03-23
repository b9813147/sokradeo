<?php

namespace App\Http\Controllers\Api\V1\Locales;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
use App\Services\App\LocaleService;

class LocaleController extends Controller
{
    private $localeSrv = null;
    
    //
    public function __construct(LocaleService $localeSrv)
    {
        $this->module = ['cate' => 'Locales', 'app' => 'Locale'];
        $this->localeSrv = $localeSrv;
    }
    
    //
    public function index()
    {
        
        return $this->localeSrv->getLocales();
        
    }
    
    //
    public function store(Request $req)
    {
        
        return $this->success(['message' => 'store']);
        
    }
    
    //
    public function show()
    {
    
        return $this->success(['message' => 'show']);
    
    }
    
    //
    public function update()
    {
    
        return $this->success(['message' => 'update']);
    
    }
    
    //
    public function destroy()
    {
    
        return $this->success(['message' => 'destroy']);
    
    }
}
