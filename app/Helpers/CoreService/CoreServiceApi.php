<?php

namespace App\Helpers\CoreService;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait CoreServiceApi
{
    /**
     * 確認用戶是否存在
     * @param string $haBook
     * @return bool
     */
    public function isExist(string $haBook): bool
    {
        try {
            $response = collect(Http::post(getenv('HABOOK_2_API_URL') . 'oauth2/GetUserInfos', [
                $haBook
            ])->json())->isNotEmpty();
        } catch (\Exception $exception) {
            Log::error('GetUserInfos', [$exception->getMessage()]);
            $response = false;
        }

        return $response;
    }

    /**
     * Get Token from ticket
     * @param string $ticket
     * @return array
     */
    public function getToken(string $ticket): array
    {
        $url        = getenv('HABOOK_2_API_URL') . 'oauth2/token';
        $grant_type = 'authorization_code';
        try {
            return Http::post($url, [
                'grant_type' => $grant_type,
                'client_id'  => config(sprintf('srvs.habook.core2.%s.client_id', getenv('APP_STATION'))),
                'code'       => $ticket,
            ])->json();
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * Get IdToken from ticket
     * @param string $ticket
     * @return string
     */
    public function getIdToken(string $ticket): string
    {
        $url        = getenv('HABOOK_2_API_URL') . 'oauth2/token';
        $grant_type = 'authorization_code';
        try {
            return Http::post($url, [
                'grant_type' => $grant_type,
                'client_id'  => config(sprintf('srvs.habook.core2.%s.client_id', getenv('APP_STATION'))),
                'code'       => $ticket,
            ])->json()['id_token'];
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * @param string $id_token
     * @return string
     */
    public function getSso(string $id_token): string
    {
        $url        = getenv('HABOOK_2_API_URL') . 'oauth2/login';
        $grant_type = 'code';
        $nonce      = '0';
        try {
            return Http::post($url, [
                'grant_type' => $grant_type,
                'client_id'  => config(sprintf('srvs.habook.core2.%s.client_id', getenv('APP_STATION'))),
                'nonce'      => $nonce,
                'id_token'   => $id_token,
            ])->json()['code'];
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * get user information
     * @param string $id_token
     * @param string $access_token
     * @return array|mixed|null
     */
    public function getUserInformation(string $id_token, string $access_token)
    {
        $url  = getenv('HABOOK_2_API_URL') . 'oauth2/usersetting';
        $body = [
            'grant_type' => 'get',
            'id_token'   => $id_token,
            'product'    => 'sokapp'
        ];
        try {
            $response = Http::withHeaders(
                [
                    'Content-Type'  => 'application/json',
                    'Authorization' => sprintf('Bearer %s', $access_token)
                ]
            )->post($url, $body)->json();
        } catch (\Exception $exception) {
            $response = null;
        }
        return $response;
    }

    /**
     * type  notice | msg | info
     * body
     * json_encode([
     *     "content" => "2021創新獎開始收件,歡迎智慧教師報名參加\n請於110年7月31日前將課例作品提交, 敬請請報名",
     *     "action"  => [
     *         [
     *             "type"          => "click",
     *             "label"         => "我要報名",
     *             "url"           => "https://sokrates.teammodel.org/exhibition/tbavideo#/activity-channel/534",
     *             "tokenbindtype" => 1,
     *         ],
     *     ],
     * ])
     * @param array $teamMode_ids
     * @param string $body
     * @param string $label
     * @param string $type
     * @param string $form
     * @return bool
     */
    public function sendNotify(array $teamMode_ids, string $body, string $label, string $type = 'msg', string $form = 'Sokrates'): bool
    {
        $isSuccessFul = true;
        $url          = getenv('HABOOK_2_API_URL') . '/service/sendnotification';
        $date         = Carbon::now()->utc()->addDay(100)->timestamp;
        $parameter    = [
            'hubName' => 'hita',
            'type'    => $type,
            'from'    => $form,
            'to'      => $teamMode_ids,
            'label'   => $label,
            'body'    => $body,
            'expires' => $date,
        ];

        try {
            Log::info('App Notify Message', [
                $this->client()->post($url, $parameter)->json(),
                $teamMode_ids,
            ]);
        } catch (\Exception $exception) {
            Log::info('notify', ['status' => 0, 'message' => $exception->getMessage()]);
            $isSuccessFul = false;
        }

        return $isSuccessFul;
    }

    /**
     * 增加 access_token
     * @return \Illuminate\Http\Client\PendingRequest
     */
    private function client(): \Illuminate\Http\Client\PendingRequest
    {
        return Http::withHeaders([
            'content-type'  => 'application/json',
            'authorization' => sprintf('Bearer %s', $this->accessToken()),
        ]);
    }


    /**
     * @return mixed|null
     */
    protected function accessToken()
    {
        $isSuccessFul = null;
        try {
            $url          = getenv('HABOOK_2_API_URL') . 'oauth2/token';
            $json         = Http::post($url, [
                'grant_type'    => 'device',
                'client_id'     => config(sprintf('srvs.habook.core2.%s.client_id', getenv('APP_STATION'))),
                'client_secret' => config(sprintf('srvs.habook.core2.%s.client_secret', getenv('APP_STATION'))),
            ])->json();
            $collect      = collect($json)->get('access_token');
            $isSuccessFul = $collect;
        } catch (\Exception $exception) {
            Log::info('access_token', ['status' => 0, 'message' => $exception->getMessage()]);
        }
        return $isSuccessFul;
    }
}
