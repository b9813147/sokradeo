<?php

namespace App\Libraries\Habook;

use App\Helpers\Api\JsonRpcII;
use App\Helpers\Code\AlphaNumeric;

class Api
{
    use JsonRpcII, AlphaNumeric;

    private $baseUrl = '';

    //
    public function __construct($params)
    {
        $this->baseUrl = $params['url'];
    }

    /*
     * service
     * */

    //
    public function getServiceRegist($params)
    {
        return $this->response('service', 'Regist', $params);
    }

    /*
     * account
     * */

    //
    public function getAccountUserInfoManage($params)
    {
        return $this->response('account', 'UserInfoManage', $params);
    }

    public function GetQuickLoginTicket($params)
    {
        return $this->response('account', 'GetQuickLoginTicket', $params);
    }

    //
    private function response($fun, $method, $params)
    {
        $id   = $this->generateAlphaNum();
        $data = $this->toJsonRpcIIReq($id, $method, $params);
        return [
            'id'   => $id,
            'url'  => $this->baseUrl . $fun,
            'data' => $data,
        ];
    }
}
