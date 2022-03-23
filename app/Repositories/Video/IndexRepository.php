<?php

namespace App\Repositories\Video;

use Illuminate\Support\Facades\Storage;
use App\Helpers\Path\Video as VideoPath;
use App\Models\VideoIndex;

class IndexRepository
{
    use VideoPath;
    
    //
    public function __construct()
    {
        
    }
    
    //
    public function getIdxes($videoId)
    {
        return VideoIndex::where('video_id', $videoId)
                ->orderBy('time', 'asc')
                ->get();
    }
    
    //
    public function getIdx($idxId)
    {
        // 待實作
    }
    
    //
    public function createIdxes($videoId, $idxes)
    {
        $timestamp = date('Y-m-d H:i:s');
        $time      = time();
        
        $tmpPath  = $this->pathPublicVideoIdx($videoId, 'tmp');
        $filePath = $this->pathPublicVideoIdx($videoId);
        
        foreach ($idxes as $i => $v) {
            $idxes[$i]['video_id'  ] = $videoId;
            $idxes[$i]['created_at'] = $timestamp;
            $idxes[$i]['updated_at'] = $timestamp;
            
            if (empty($v['thumbnail'])) {
                continue;
            }
            
            $thum = strval($time).'-'.$v['thumbnail'];
            
            $ori = $tmpPath.$v['thumbnail'];
            if (! Storage::exists($ori)) {
                continue;
            }
            $tar = $filePath.$thum;
            Storage::move($ori, $tar);
            
            $idxes[$i]['thumbnail'] = $thum;
        }
        
        VideoIndex::insert($idxes);
    }
    
}
