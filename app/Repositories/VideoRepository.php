<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Video;

class VideoRepository
{
    //
    public function __construct()
    {
        
    }
    
    //
    public function list($page = 1)
    {
        return Video::paginate(null, ['*'], 'page', $page);
    }
    
    //
    public function listByUserId($userId, $page = 1)
    {
        return User::findOrFail($userId)->videos()->paginate(null, ['*'], 'page', $page);
    }
    
    //
    public function getVideo($videoId)
    {
        return Video::findOrFail($videoId);
    }
    
    //
    public function hitVideo($videoId)
    {
        $video = Video::findOrFail($videoId);
        $video->hits++;
        $video->save();
    }
    
    //
    public function createVideo($userId, $video)
    {
        $video['user_id'] = $userId;
        return Video::create($video);
    }
    
    //
    public function getResrc($videoId)
    {
        return Video::findOrFail($videoId)->resource;
    }
    
}
