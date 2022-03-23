<?php

namespace App\Repositories\Video;

use Exception;
use App\Models\VideoHistory;

class HistoryRepository
{
    //
    public function __construct()
    {
        
    }
    
    //
    public function list($page = 1)
    {
        return VideoHistory::with('video')->paginate(null, ['*'], 'page', $page);
    }
    
    //
    public function listByUserId($userId, $page = 1)
    {
        return VideoHistory::where('user_id', $userId)->with('video')->paginate(null, ['*'], 'page', $page);
    }
    
    //
    public function getHists($userId, $limit = 5)
    {
        return VideoHistory::where('user_id', $userId)->with('video')
            ->orderBy('updated_at', 'desc')->limit($limit)
            ->get();
    }
    
    //
    public function getHist($userId, $videoId)
    {
        return VideoHistory::where(['user_id' => $userId, 'video_id' => $videoId])->with('video')->firstOrFail();
    }
    
    //
    public function createHist($userId, $videoId)
    {
        $now = date('Y-m-d H:i:s');
        
        $conds = ['user_id' => $userId, 'video_id' => $videoId];
        
        if (VideoHistory::where($conds)->exists()) {
            VideoHistory::where($conds)->update(['updated_at' => $now]);
        } else {
            $conds['updated_at'] = $now;
            VideoHistory::create($conds);
        }
    }
    
}
