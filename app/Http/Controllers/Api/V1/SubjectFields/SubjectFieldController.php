<?php

namespace App\Http\Controllers\Api\V1\SubjectFields;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
use App\Services\App\SubjectFieldService;

class SubjectFieldController extends Controller
{
    private $subjectFieldSrv = null;
    
    //
    public function __construct(SubjectFieldService $subjectFieldSrv)
    {
        $this->module = ['cate' => 'SubjectFields', 'app' => 'SubjectField'];
        $this->subjectFieldSrv = $subjectFieldSrv;
    }
    
    //
    public function index()
    {
        
        return $this->subjectFieldSrv->getSubjectFields();
        
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
