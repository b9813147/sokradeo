<?php

namespace App\Http\Controllers\Api\V1\TbaVideos;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
use App\Models\Tba;
use App\Services\Cms\TbaVideoService;

class TbaVideoController extends Controller
{
    private $tbaVideoSrv = null;
    
    //
    public function __construct(TbaVideoService $tbaVideoSrv)
    {
        $this->module      = ['cate' => 'TbaVideos', 'app' => 'TbaVideo'];
        $this->tbaVideoSrv = $tbaVideoSrv;
    }
    
    //
    public function index()
    {
        
        return $this->success(['message' => 'index']);
        
    }
    
    //
    public function store(Request $req)
    {
        $this->validate($req, [
                'tba'    => 'required',
                'videos' => 'required',
                'maps'   => 'required',
        ]);
        
        $this->authorize('create', Tba::class);
        
        // 待實作:此功能可由其他功能組合而成 若有需求請實作之
        
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
