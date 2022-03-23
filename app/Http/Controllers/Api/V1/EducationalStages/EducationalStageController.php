<?php

namespace App\Http\Controllers\Api\V1\EducationalStages;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
use App\Services\App\EducationalStageService;

class EducationalStageController extends Controller
{
    private $eduStageSrv = null;
    
    //
    public function __construct(EducationalStageService $eduStageSrv)
    {
        $this->module = ['cate' => 'EducationalStages', 'app' => 'EducationalStage'];
        $this->eduStageSrv = $eduStageSrv;
    }
    
    //
    public function index()
    {
        
        return $this->eduStageSrv->getEduStages();
        
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
