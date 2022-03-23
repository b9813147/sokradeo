<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CoreServiceTest extends TestCase
{
    public function testBasic()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    // 裝置或服務取得金鑰及刷新金鑰
    public function testGetToken()
    {
        //post
        $url           = 'https://api2.teammodel.net/oauth2/token';
        $grant_type    = 'string    必須	"refresh_token":刷新金鑰、"authorization_code":驗證Code授權、"device":單純裝置拿授權';
        $client_id     = 'string    必須	產品服務識別碼，請依照所在數據中心位置，填入對應識別碼';
        $client_secret = 'string	僅提供grant_type為driver時必須傳入，產品服務識別碼對應的密鑰';
        $code          = 'string	僅提供grant_type為authorization_code時必須傳入';
        $access_token  = 'string	僅提供grant_type為refresh_token時必須傳入';

        $body = Http::post($url, [
            'grant_type' => 'authorization_code',
            'client_id'  => 'd7193896-9b12-4046-ad44-c991dd48cc39',
            'code'       => 'Code41ebb3d6-8814-45f4-93ee-884a909c5071',
        ])->body();

        dd($body);

    }

}
