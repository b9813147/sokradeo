<?php

namespace Tests\Feature;

use App\Helpers\Custom\GlobalPlatform;
use App\Services\App\UserService;
use App\Models\Group;
use App\Models\GroupChannelContent;
use App\Models\GroupUser;
use App\Models\Tba;
use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function testBasic()
    {
        $this->markTestSkipped('Skip testBasic');

        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testUserCount()
    {
        $this->markTestSkipped('Skip testUserCount');

        $user = User::query()->findOrFail(948);
        $tbas = Tba::query()->where('habook_id', $user->habook);

        $contentIds = $tbas->pluck('id');
        // 全頻道公開
        $publicTotal = $this->getGroupChannelContentTotal($contentIds, [1], [1])->count();
        //  影片總數
        $total = $this->getGroupChannelContentTotal($contentIds, [0, 1, 2], [1, 0])->count();

        $mapTba = Tba::query()->whereIn('id', $contentIds)->with([
            'tbaPlaylistTracks' => function ($q) {
                $q->select('tba_playlist_tracks.id', 'tba_playlist_tracks.tba_id', 'tba_playlist_tracks.ref_tba_id')
                    ->orderBy('tba_playlist_tracks.list_order');
            },
            'tbaStatistics'     => function ($q) {
                $q->selectRaw('MAX(CASE WHEN type = 47 THEN CAST(tba_statistics.idx AS signed) ELSE 0 END) AS T,MAX(CASE WHEN type = 48 THEN CAST(tba_statistics.idx AS signed) ELSE 0 END) AS P,tba_statistics.tba_id,MAX(CASE WHEN type = 55 THEN CAST(idx AS signed) ELSE 0 END) AS C')
                    ->groupBy('tba_statistics.tba_id');
            }
        ])->orderBy('lecture_date', 'DESC');
//
        $doubleGreenLightTotal = $mapTba->get()->filter(function ($tba) {
            if ($tba->tbaStatistics->isNotEmpty()) {
                if ((int)$tba->tbaStatistics->first()->T >= 70 && (int)$tba->tbaStatistics->first()->P >= 70) {
                    return $tba->tbaStatistics;
                }
            }
        })->count();
        $result                = [
            'public_total'           => number_format($publicTotal),
            'hits_total'             => number_format((int)$mapTba->sum('hits')),
            'all_total'              => number_format($total),
            'doubleGreenLight_total' => number_format($doubleGreenLightTotal),
        ];
        dd($result);

    }

    public function testEncode()
    {
        $this->markTestSkipped('Skip testEncode');
        $encryptString = \Crypt::encryptString('948');
        dd($encryptString);
    }

    /**
     * @param \Illuminate\Support\Collection $tbaIds
     * @param $content_public
     * @param $content_status
     * @return GroupChannelContent|\Illuminate\Database\Eloquent\Builder
     */
    private function getGroupChannelContentTotal(\Illuminate\Support\Collection $tbaIds, $content_public, $content_status)
    {
        $this->markTestSkipped('Skip getGroupChannelContentTotal');
        return GroupChannelContent::query()->whereIn('content_id', $tbaIds)->whereIn('content_public', $content_public)->whereIn('content_status', $content_status);
    }

    public function testUserByGroup()
    {
        $this->markTestSkipped('Skip testUserByGroup');
        $model = User::query()->with('groups')->findOrFail(948);
        $model->groups()->attach(142, ['member_status' => 1, 'member_duty' => 'Admin']);
//        $model->groups()->updateExistingPivot(142, ['member_duty' => 'Admin']);

    }
    
    /**
     * Test isAllowedToOperateObsrvClass
     */
    public function testIsAllowedToOperateObsrvClass()
    {
        $srv = app(UserService::class);

        // Nuttaphat Arunoprayoch (Logan)
        $userId = 3271;
        $isAllowed = $srv->isAllowedToOperateObsrvClass($userId);
        $this->assertIsBool($isAllowed);

        // Empty User
        $userId = 0;
        $isAllowed = $srv->isAllowedToOperateObsrvClass($userId);
        $this->assertFalse($isAllowed);
    }
}
