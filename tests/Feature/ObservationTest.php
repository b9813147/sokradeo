<?php

namespace Tests\Feature;

use App\Models\GroupChannelContent;
use App\Models\Tba;
use Tests\TestCase;

class ObservationTest extends TestCase
{
    public function testBasic()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testTbaInfo()
    {
        $groupChannelId = 82;
        $contentId      = 4108;
        $contentId      = GroupChannelContent::query()->where('group_channel_id', $groupChannelId)->where('content_id', $contentId)->pluck('content_id');
//        dd($contentId);
        $tbaInfo = Tba::query()->with([
            'tbaEvaluateEvents' => function ($q) {
                $q->with([
                    'tbaEvaluateEventMode'  => function ($q) {
                        $q->select('mode', 'event', 'id');
                    },
                    'tbaEvaluateUser'       => function ($q) {
                        $q->with([
                            'user' => function ($q) {
                                $q->select('name', 'id');
                            }
                        ]);
                    },
                    'tbaEvaluateEventFiles' => function ($q) {
                        $q->select('image_url', 'tba_evaluate_event_id');
                    }
                ])->selectRaw("text, FROM_UNIXTIME(time_point,'%i:%s') time , tba_id, tba_evaluate_event_mode_id, tba_evaluate_user_id");
            },
            'user'              => function ($q) {
                $q->select('name', 'id');
            },
            'tbaAnnexs'         => function ($q) {
                $q->select('type', 'tba_id');
            }
        ])->where('id', $contentId)->get();

        dd($tbaInfo->toArray());

    }
}
