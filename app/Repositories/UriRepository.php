<?php

namespace App\Repositories;

use LogicException;
use Illuminate\Support\Facades\DB;
use App\Models\Uri;
use App\Models\Resource;
use App\Helpers\Path\Tba as TbaPath;

class UriRepository
{
    use TbaPath;
    //
    public function __construct()
    {
        
    }
    
    //
    public function list($page = 1)
    {
        return Uri::paginate(null, ['*'], 'page', $page);
    }
    
    //
    public function listByUserId($userId, $page = 1)
    {
        // 待實作
    }
    
    //
    public function getUri($uriId)
    {
        return Uri::findOrFail($uriId);
    }

    //
    public function getUriByResourceId($resourceId)
    {
        $tba = DB::table('resources')
            ->join('videos', 'resources.id', 'videos.resource_id')
            ->join('tba_video', 'videos.id', 'tba_video.video_id')
            ->where('resources.id', $resourceId)
            ->get();
        $data = Resource::with(['uri'])->where('id', $resourceId)->firstOrFail();
        $data->uri->thumbnail = '//' . $_SERVER['SERVER_NAME'] . '/storage/tba/'. $tba[0]->tba_id . '/' . $tba[0]->thumbnail;
        return $data;
    }
    
    //
    public function createUri($resrcId, $uri)
    {
        /*
        if (Uri::where('resource_id', $resrcId)->exists()) {
            throw new LogicException('resrc of uri is already exist');
        }
        */
        $uri['resource_id'] = $resrcId;
        return Uri::create($uri);
    }
    
}
