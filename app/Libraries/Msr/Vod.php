<?php

namespace App\Libraries\Msr;

use App\Exceptions\Exception\Service\ServiceException;
use Ixudra\Curl\Facades\Curl;

class Vod
{
    private $authorization = 'token 28437d983577ae69aad671c662668d3155761d6b'; // 待增加模組開關設定
    private $baseApi       = 'api';
    private $baseUrl       = '';
    private $cateId        = '';
    
    //
    public function __construct($params)
    {
        $this->baseUrl = $params['protocol'].'://'.$params['dn'].'/'.$this->baseApi.'/';
        $this->cateId  = $params['cateId'];
    }
    
    //
    public function getVideo($videoId)
    {
        $url = $this->composeSubApi('videos', $videoId, null);
        $resp = Curl::to($url)
            ->withHeader('Authorization: '.$this->authorization)
            ->withHeader('Content-Type: application/json')
            ->returnResponseObject()
            ->get();
        
        if($resp->status !== 200) {
            throw new ServiceException('msr api error');
        }
        
        return json_decode($resp->content);
    }
    
    //
    private function composeSubApi($resrc, $id = null, $feature = null)
    {
        return $this->baseUrl.$resrc.(empty($id) ? '' : '/'.$id).(empty($feature) ? '' : '/'.$feature);
    }
}
