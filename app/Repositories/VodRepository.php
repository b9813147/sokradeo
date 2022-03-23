<?php

namespace App\Repositories;

use LogicException;
use App\Models\Vod;
use Illuminate\Support\Facades\DB;

class VodRepository
{
    //
    public function __construct()
    {
        
    }
    
    //
    public function list($page = 1)
    {
        return Vod::paginate(null, ['*'], 'page', $page);
    }
    
    //
    public function listByUserId($userId, $page = 1)
    {
        // 待實作
    }
    
    //
    public function getVod($vodId)
    {
        return Vod::findOrFail($vodId);
    }

    //
    public function getThumbnailByResourceId($resourceId)
    {
        $tba = DB::table('resources')
            ->join('videos', 'resources.id', 'videos.resource_id')
            ->join('tba_video', 'videos.id', 'tba_video.video_id')
            ->where('resources.id', $resourceId)
            ->get();
        
        return '//' . $_SERVER['SERVER_NAME'] . '/storage/tba/'. $tba[0]->tba_id . '/' . $tba[0]->thumbnail;
    }
    
    //
    public function createVod($resrcId, $vod)
    {
        /*
        if (Vod::where('resource_id', $resrcId)->exists()) {
            throw new LogicException('resrc of vod is already exist');
        }
        */
        $vod['resource_id'] = $resrcId;
        return Vod::create($vod);
    }

    //
    public function updateVod($resrcId, $data)
    {
        return Vod::where('resource_id', '=', $resrcId)->update($data);
    }
    
}
