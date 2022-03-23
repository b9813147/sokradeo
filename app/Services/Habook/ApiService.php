<?php

namespace App\Services\Habook;

use App\Exceptions\Exception\Service\ServiceException;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;
use Ixudra\Curl\Facades\Curl;
use App\Libraries\Habook\Api as HabookApi;
use App\Repositories\ConfigRepository;

class ApiService
{
    private $configRepo = null;
    private $habookApi = null;
    private $habookConfig = null;

    //
    public function __construct(ConfigRepository $configRepo)
    {
        $apiSrvConfig       = Config::get('srvs.habook.api');
        $this->habookConfig = $configRepo->getParamsByCate('Habook');
        $this->configRepo   = $configRepo;
        $this->habookApi    = new HabookApi(['url' => $apiSrvConfig['url']]);
    }

    public function getApiAuthorization()
    {
        $coreSrvConfig = Config::get('srvs.habook.core');

        $info = $this->habookApi->getServiceRegist([
            'clientId'            => $coreSrvConfig['clientId'],
            'verificationCode'    => $coreSrvConfig['verificationCode'],
            'verificationCodeVer' => $coreSrvConfig['verificationCodeVer'],
            'productCode'         => $coreSrvConfig['productCode'],
        ]);

        $resp = Curl::to($info['url'])
            ->withContentType('application/json')
            ->withData($info['data'])
            ->asJson()
            ->returnResponseObject()
            ->post();

        if ($resp->status !== 200 || is_null($resp->content->result)) {
            throw new ServiceException('habook api is error');
        }

        $authorization = $resp->content->result->token;
        $this->configRepo->setParamVal('HaBook', 'ApiAuthorization', $authorization);
        $this->habookConfig = $this->configRepo->getParamsByCate('Habook');
        return $authorization;
    }

    //
    public function getUserInfo($ticket)
    {
        $info = $this->habookApi->getAccountUserInfoManage([
            'idToken' => $ticket,
            'method'  => 'get',
            'option'  => 'userInfo',
        ]);
        return $this->execute($info['url'], $info['data']);
    }

    //
    public function execute($url, $data)
    {
        $authorization = $this->habookConfig['ApiAuthorization']->val;

        if (is_null($authorization)) {

            $authorization = $this->getApiAuthorization();
        }

        try {

            $resp = Curl::to($url)
                ->withHeader('Authorization: ' . $authorization)
                ->withContentType('application/json')
                ->withData($data)
                ->asJson()
                ->returnResponseObject()
                ->post();

            if ($resp->status !== 200 || is_null($resp->content->result)) {
                throw new ServiceException('habook api is error');
            }

            return $resp->content->result;

        } catch (ServiceException $e) {

            $authorization = $this->getApiAuthorization();

            $resp = Curl::to($url)
                ->withHeader('Authorization: ' . $authorization)
                ->withContentType('application/json')
                ->withData($data)
                ->asJson()
                ->returnResponseObject()
                ->post();

            if ($resp->status !== 200 || is_null($resp->content->result)) {
                throw new ServiceException('habook api is error');
            }

            return $resp->content->result;
        }
    }

    public function getTicket($ticket)
    {

        $info = $this->habookApi->GetQuickLoginTicket([
            'idToken' => $ticket,
            'method'  => 'GetQuickLoginTicket',
            'option'  => 'userInfo',
        ]);

        return $this->execute($info['url'], $info['data']);
    }

    public function getUserTicket()
    {
        $authorization = $this->habookConfig['ApiAuthorization']->val;

        if (is_null($authorization)) {

            $authorization = $this->getApiAuthorization();
        }

        $api    = new Client();
        $params = [
            "jsonrpc" => "2.0",
            "method"  => "GetQuickLoginTicket",
            "params"  =>
                [
                    "idToken" => session()->get('idToken')
                ],
            "id"      => 1
        ];

        $data = $api->request('post', config('srvs.habook.api.url').'account', [
            'headers' => [
                'Authorization' => $authorization,
                'Content-Type'  => 'application/json',
            ],
            'json'    => $params
        ]);

        $contents = $data->getBody()->getContents();
        $contents = \GuzzleHttp\json_decode($contents);

        return $contents;
    }
}
