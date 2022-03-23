<?php

namespace App\Libraries\Aliyun;

require_once (base_path('third_party/aliyun-php-sdk/aliyun-php-sdk-core/Config.php'));

use Exception;
use vod\Request\V20170321 as AliyunVod;

class Vod
{
    private $regionId        = '';
    private $accessKeyId     = '';
    private $accessKeySecret = '';
    private $cateId          = '';
    private $vodClient       = null;
    
    //
    public function __construct($params)
    {
        $this->regionId        = $params['regionId'];
        $this->accessKeyId     = $params['accessKeyId'];
        $this->accessKeySecret = $params['accessKeySecret'];
        $this->cateId          = $params['cateId'];
        $this->initVodClient();
    }
    
    //
    private function initVodClient()
    {
        $profile = \DefaultProfile::getProfile($this->regionId, $this->accessKeyId, $this->accessKeySecret);
        $this->vodClient = new \DefaultAcsClient($profile);
    }
    
    //
    public function getPlayAuth($videoId)
    {
        $request = new AliyunVod\GetVideoPlayAuthRequest();
        $request->setRegionId($this->regionId);
        $request->setVideoId($videoId);
        $request->setAuthInfoTimeout(3600); // 播放凭证过期时间，默认为100秒，取值范围100~3600；注意：播放凭证用来传给播放器自动换取播放地址，凭证过期时间不是播放地址的过期时间
        $request->setAcceptFormat('JSON');
        return $this->vodClient->getAcsResponse($request);
    }
    
    //
    public function getPlayInfo($videoId)
    {
        $request = new AliyunVod\GetPlayInfoRequest();
        $request->setRegionId($this->regionId);
        $request->setVideoId($videoId);
        $request->setAuthTimeout(3600*24); // 播放地址过期时间（只有开启了URL鉴权才生效），默认为3600秒，支持设置最小值为3600秒
        $request->setAcceptFormat('JSON');
        return $this->vodClient->getAcsResponse($request);
    }
}
