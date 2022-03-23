<?php

namespace App\Services\Cms;

use ZipArchive;
use App\Helpers\Path\Video as VideoPath;
use App\Helpers\Url\Video as VideoUrl;
use App\Factories\Src\SrcServiceFactory;
use App\Repositories\ResourceRepository;
use App\Repositories\VideoRepository;
use App\Repositories\Video\HistoryRepository as VideoHistoryRepository;
use App\Repositories\Video\IndexRepository as VideoIndexRepository;
use App\Repositories\Video\MarkStatisticRepository as VideoMarkStatisticRepository;
use App\Types\Video\MarkType;

class VideoService
{
    use VideoPath, VideoUrl;
    
    private $srcSrvFty     = null;
    private $resrcRepo     = null;
    private $videoRepo     = null;
    private $videoHistRepo = null;
    
    //
    public function __construct(
            SrcServiceFactory      $srcSrvFty,
            ResourceRepository     $resrcRepo,
            VideoRepository        $videoRepo,
            VideoHistoryRepository $videoHistRepo
    )
    {
        $this->srcSrvFty     = $srcSrvFty;
        $this->resrcRepo     = $resrcRepo;
        $this->videoRepo     = $videoRepo;
        $this->videoHistRepo = $videoHistRepo;
    }
    
    //
    public function list($userId, $page)
    {
        return $this->videoRepo->listByUserId($userId, $page);
    }
    
    //
    public function getVideo($videoId)
    {
        return $this->videoRepo->getVideo($videoId);
    }
    
    //
    public function hitVideo($videoId, $userId = null)
    {
        $this->videoRepo->hitVideo($videoId);
        if (is_null($userId)) {
            return;
        }
        $this->videoHistRepo->createHist($userId, $videoId);
    }
    
    //
    public function createVideo($userId, $video, $resrc, $idxInfo = null, $markStatInfo = null, $vodType = null)
    {
        $thumFile = isset($video['thum']) ? $video['thum'] : null;
        $thumName = is_null($thumFile) ? null : 'thum.'.$thumFile->getClientOriginalExtension();
        $video['thumbnail'] = $thumName;
        
        $resrc  = collect($resrc);
        $src    = $resrc->get('data');
        $resrc  = $resrc->except(['data'])->toArray();
        $resrc  = $this->resrcRepo->createResrc($userId, $resrc);
        $params = [];
        if (! is_null($vodType)) {
            $params['vodType'] = $vodType;
        }
        $srcSrv = $this->srcSrvFty->create($resrc->src_type, $params);
        $src    = $srcSrv->createSrc($resrc->id, $src);
        $this->resrcRepo->setResrc($resrc->id, ['status' => 1]);
        $video['resource_id'] = $resrc->id;
        $video = $this->videoRepo->createVideo($userId, $video);
        
        if (! is_null($thumFile)) {
        
            $this->uploadThum($video->id, $thumFile, $thumName);
        }
        
        // video indices
        if (!is_null($idxInfo) && !is_null($idxInfo['list'])) {
            
            $idxThumFile = isset($idxInfo['thumFile']) ? $idxInfo['thumFile'] : null;
            if (! is_null($idxThumFile)) {
                $this->uploadIdxThumFile($video->id, $idxThumFile);
            }
            
            (new VideoIndexRepository())->createIdxes($video->id, $idxInfo['list']);
        }
        
        // video mark statistics
        if (! is_null($markStatInfo)) {
            
            $markStatRepo = new VideoMarkStatisticRepository();
            foreach ($markStatInfo as $markType => & $markStats) {
                $markStatRepo->createMarkStatsByMarkType($video->id, $markType, $markStats);
            }
        }
        
        return $video;
    }
    
    //
    public function getVideoHists($userId)
    {
        return $this->videoHistRepo->getHists($userId);
    }
    
    /*
     * Rice:被迫破壞設計(因為發表會展示被迫破壞抽象化, 敬請見諒, 已違背當初設計理念, 以後請勿參考, 即便如此也不是真正的多台串流媒體支援, 是假的)
     *
     * */
    //
    public function getExeInfo($videoId)
    {
        // videoId -> resrcId
        $resrc = $this->videoRepo->getResrc($videoId);
        
        $srcSrv = null;
        // src:
        $src = $resrc->src();
        // (1)src is Vod
        if ($resrc->src_type === \App\Types\Src\SrcType::Vod) {
            
            $configRepo = new \App\Repositories\ConfigRepository();
            
            switch ($src->type) {
                case \App\Types\Src\VodType::Msr:
                    $params = $configRepo->getParamsByCate(\App\Types\Src\VodType::Msr);
                    $srcSrv = new \App\Services\Src\Vod\MsrVodService(new \App\Repositories\VodRepository(), $params);
                    break;
                    
                case \App\Types\Src\VodType::AliyunVod:
                    $params = $configRepo->getParamsByCate(\App\Types\Src\VodType::AliyunVod);
                    $srcSrv = new \App\Services\Src\Vod\AliyunVodVodService(new \App\Repositories\VodRepository(), $params);
                    break;

                case \App\Types\Src\VodType::AzureFile:
                    $srcSrv = new \App\Services\Src\Vod\AzureFileVodService(new \App\Repositories\VodRepository());
                    break;
            }
        }
        // (2)src is other
        else {
            $srcSrv = $this->srcSrvFty->create($resrc->src_type);
        }
        
        $detail = $srcSrv->getDetail($src);
        foreach ($detail['list'] as $i => $v) {
            switch($v['format']) {
                case 'mp4' : $detail['list'][$i]['mime'] = 'video/mp4';             break;
                case 'm3u8': $detail['list'][$i]['mime'] = 'application/x-mpegURL'; break;
                default:
                    break;
            }
        }
        return $detail;
    }
    
    /*
     * Rice:原始正規設計(因為發表會展示被迫破壞抽象化, 敬請見諒)
     * 
     * */
    //
    public function getExeInfo_ori_design($videoId)
    {
        $resrc  = $this->videoRepo->getResrc($videoId);
        $srcSrv = $this->srcSrvFty->create($resrc->src_type);
        $detail = $srcSrv->getDetail($resrc->src());
        foreach ($detail['list'] as $i => $v) {
            switch($v['format']) {
                case 'mp4' : $detail['list'][$i]['mime'] = 'video/mp4';             break;
                case 'm3u8': $detail['list'][$i]['mime'] = 'application/x-mpegURL'; break;
                default:
                    break;
            }
        }
        return $detail;
    }
    
    //
    public function getEzStationInfo($videoId)
    {
        $idxRepo      = new VideoIndexRepository();
        $markStatRepo = new VideoMarkStatisticRepository();
        
        $video = $this->videoRepo->getVideo($videoId);
        $idxes = $idxRepo->getIdxes($videoId)->transform(function ($v) {
            $v['thum'] = is_null($v['thumbnail']) ? null : $this->urlVideoIndex($v['video_id'], $v['thumbnail']);
            return $v;
        });
        
        $hards = $markStatRepo->getMarkStatsByMarkType($videoId, MarkType::Hard);
        $stars = $markStatRepo->getMarkStatsByMarkType($videoId, MarkType::Star);
        
        return [
                'intro' => [
                        'title'     => $video->name,
                        'desc'      => $video->description,
                        'author'    => $video->author,
                        'copyright' => $video->copyright,
                ],
                'idxes' => $idxes,
                'marks' => [
                        'hard' => $hards,
                        'star' => $stars,
                ],
        ];
    }

    //
    private function uploadThum($videoId, $file, $fileName) {
        $path = $this->pathPublicVideo($videoId, null, false);
        return $file->storeAs($path, $fileName);
    }
    
    private function uploadIdxThumFile($videoId, $file) {
        $tarPath = $this->pathPublicVideoIdx($videoId, 'tmp', true);
        $zipper  = new ZipArchive;
        if ($zipper->open($file->path()) === TRUE) {
            $zipper->extractTo($tarPath);
            $zipper->close();
        }
    }
    
}
