<?php

namespace Tests\Feature;

use App\Models\Tba;
use App\Models\User;
use App\Notifications\EventChannel;
use App\Services\Library\BbService;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BbTest extends TestCase
{
    public function testBasic()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testGetHiTeachLicense()
    {
        $apiKey = \Config::get('srvs.habook.api.key');

        $hash256 = hash_hmac('sha256', $apiKey, Carbon::now()->format('Y-m-d'));


        $http = new Client();
//        $url       = \config()->get('srvs.Azure.bb.url');
        $url = 'http://192.168.0.171/habb/apicommu/order/trialSerislOrder'; // test
//        $url ='192.168.68.157/api/test';
        $EventName = 'test';
        $contents  = $http->request('put', $url, [
            'headers'     => [
                'X-Auth-Hash' => $hash256
            ],
            'form_params' => [
                'event' => $EventName,
                'data'  => [
                    [
                        "id"          => "0934161322#4130", //醍摩豆帳號
                        "name"        => "Ares test",       //姓名
                        "productCode" => "J223IZ6M",        //產品八碼
                        "trialDay"    => 21,                //申請試用天數
                        "email"       => ''               //email]
                    ]
                ]
            ],
        ])->getBody()->getContents();
        $result    = json_decode($contents);
        if ($result->status === 0) {
//            if ($result->data->"0934161322#4130")

        }
        dd(json_decode($contents));
//        return $contents;
//        dd($contents);
    }

    public function testTbaUpdate()
    {
        $result = json_decode(file_get_contents(public_path('student_data.json')));
        foreach ($result as $item) {
            Tba::query()->where(['mark' => $item->mark, 'locale_id' => $item->locale])->update(['irs_count' => $item->irs_count, 'student_count' => $item->student_count]);
        }

    }


    public function testGetLicense()
    {

        auth()->loginUsingId(948);
        $app      = app(BbService::class);
        $trialDay = Carbon::create(2021, 07, 31);
        $current  = Carbon::now();
        // 天數
        $d = $trialDay->diff($current)->days;

        $LicenseData = [
            "id"          => auth()->user()->habook,               //醍摩豆帳號
            "name"        => auth()->user()->name,                 //姓名
            "productCode" => config('srvs.Azure.bb.productCode'),  //產品八碼
            "trialDay"    => $d,                                   //申請試用天數
            "email"       => $userInfo->email ?? null,             //email
        ];
        $licence     = $app->getLicence('ares-test', $LicenseData);
        $message     = [
            'title'   => 'AI Sokrates智慧課堂培訓課程授權序號',
            'content' => "'歡迎參加培訓課程' . 'test' .'。提供一組互動教室軟體序號，以應培訓課程實作任務所需。'
                                              '授權序號：'.$licence->serial ?? null
                                              '授權期限：'.$trialDay"
        ];
        User::query()->find(948)->notify(new EventChannel($message));
        dd();
//        auth()->user()->notify(new EventChannel($message));
//        dd(auth()->user()->notify(new EventChannel($message)));
    }
}
