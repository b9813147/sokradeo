<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\GroupSubjectFields;
use App\Models\Oauth2Member;
use App\Models\Rating;
use App\Models\User;
use App\Services\Cms\TbaService;
use App\Types\Tba\AnnexType;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Tests\TestCase;

class OauthTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
//        $token    = $this->generateToken();
//        $userInfo = $this->getUserInfo($token, '2008456584');

//        dd($userInfo);
//        $this->assertTrue(true);
    }

    /**
     *  白名單  帳號建立
     */
    public function testWhitelist()
    {
        $json_decode = json_decode(file_get_contents(public_path('nxeduyun.json')), true);

        foreach ($json_decode as $item) {
            $item = (object)$item;

            if (!empty($item->teammodelid)) {
                $user = User::query()->where('habook', $item->teammodelid);
                if ($user->exists()) {
                    $user->update([
                        'name'             => $item->name,
                        'group_channel_id' => 281
                    ]);
                    Oauth2Member::query()->create([
                        'users_id'       => $user->first()->id,
                        'oauth2_account' => $item->nxeduyun,
                        'sso_server'     => 'NXEDUYUN'
                    ]);
                } else {
                    $user = User::query()->create([
                        'name'             => $item->name,
                        'habook'           => $item->teammodelid,
                        'group_channel_id' => 281
                    ]);
                    Oauth2Member::query()->create([
                        'users_id'       => $user->id,
                        'oauth2_account' => $item->nxeduyun,
                        'sso_server'     => 'NXEDUYUN'
                    ]);
                }
            }
            $user = User::query()->create([
                'name'             => $item->name,
                'habook'           => null,
                'group_channel_id' => 281
            ]);
            Oauth2Member::query()->create([
                'users_id'       => $user->id,
                'oauth2_account' => $item->nxeduyun,
                'sso_server'     => 'NXEDUYUN'
            ]);
        }


    }

    public function OtherLogIn()
    {
        $account = '2095379837';
        $userId  = Oauth2Member::query()->where('oauth2_account', $account)->pluck('users_id')->first();

        $user = User::query()->findOrFail($userId);
        auth()->loginUsingId($user->id);
    }

    public function generateToken()
    {
        $appId  = '4E28C6F06EDA495491FA7943C143E534';
        $appKey = '3B83808EE00E42DFBBEB7502331CF1D8';
        $url    = 'https://user.nxeduyun.com/restapi/jwtToken/getAccessToken';
        $time   = Carbon::now()->timestamp;
        $result = $appId . $appKey . $time;
        $sha1   = Str::upper(sha1($result));

        $client      = new Client();
        $response    = $client->request('POST', $url, [
            'json' => [
                "appId"     => $appId,
                "keyInfo"   => $sha1,
                "timestamp" => $time,
            ],

        ]);
        $contents    = $response->getBody()->getContents();
        $json_decode = json_decode($contents, true);

        return $json_decode['retData']['token'];

    }

    public function getUserInfo($token, $userId)
    {
        $url = "https://user.nxeduyun.com/restapi/userAccount/getAccount/$userId";

        $client   = new Client();
        $response = $client->request('GET', $url, [
            'headers' => [
                'token' => $token,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);

    }

    public function testSimple()
    {
        $str = 'test';
        Group::query()->find('');
    }

    public function testExcel()
    {
        $tbaService    = app(TbaService::class);
        $groupChannels = null;
        $user_info     = (object)[
            'channelId' => 7,
            'id'        => 948
        ];
        $filter        = collect([]);
        if ($filter->isNotEmpty()) {
            $groupChannels = $tbaService->getGroupByFilter($user_info->channelId, $user_info->id, $filter, false);
        } elseif ($filter->isEmpty()) {
            $groupChannels = $tbaService->getGroupChannel($user_info->channelId, $user_info->id, false);
        }
        $groupChannels = null;

        if ($filter->isNotEmpty()) {
            $groupChannels = $tbaService->getGroupByFilter($user_info->channelId, $user_info->id, $filter, false);
        } elseif ($filter->isEmpty()) {
            $groupChannels = $tbaService->getGroupChannel($user_info->channelId, $user_info->id, false);
        }

        $groupChannels->each(function ($v, $key) use (&$content) {
            $lessonPlan = $v->tbaAnnexs->filter(function ($tbaAnnex) {
                return $tbaAnnex->type === AnnexType::LessonPlan;
            });
            $material   = $v->tbaAnnexs->filter(function ($tbaAnnex) {
                return $tbaAnnex->type === AnnexType::Material;
            });
//            dd($v->groupChannelContent->first()->groupSubjectFields->first(),$v->groupChannelContent->first()->groupRatingFields->first() instanceof Rating ? $v->groupChannelContent->first()->groupRatingFields()->first()->name : 'x');


            $content[] = [
                'rating'    => $v->groupChannelContent->first()->groupRatingFields instanceof Rating ? $v->groupChannelContent->first()->groupRatingFields->first()->name : 'x',
                'subject'   => $v->groupChannelContent->first()->groupSubjectFields instanceof GroupSubjectFields ? $v->groupChannelContent->first()->groupSubjectFields()->first()->subject : __('app/subject-field.Other'),
                'rating_2'  => !empty($v->groupChannelContent()->first()->groupRatingFields()->first()->name) ? $v->groupChannelContent()->first()->groupRatingFields()->first()->name : 'x',
                'subject_2' => !empty($v->groupChannelContent()->first()->groupSubjectFields()->first()->subject) ? $v->groupChannelContent()->first()->groupSubjectFields()->first()->subject : __('app/subject-field.Other'),
            ];
        });
        dd($content);
    }
}
