<?php

namespace Tests\Feature;

use App\Models\GroupChannel;
use App\Models\Score;
use App\Models\Tba;
use App\Types\Tba\ScoreType;
use Tests\TestCase;

class ScoreTest extends TestCase
{
    public function testBasic()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testUpdate()
    {
        $score  = Score::query()->select('score_data')->where(['tba_id' => 61, 'flag' => ScoreType::Valid])->get()->pluck('score_data');
        $result = collect([
            'inno'     => 0,
            'tApp'     => 0,
            'tDesign'  => 0,
            'tEffect'  => 0,
            'tProcess' => 0,
        ]);
        $score->each(function ($q) use ($result) {
            $result['inno']     += json_decode($q)->inno;
            $result['tApp']     += json_decode($q)->tApp;
            $result['tDesign']  += json_decode($q)->tDesign;
            $result['tEffect']  += json_decode($q)->tEffect;
            $result['tProcess'] += json_decode($q)->tProcess;
        });

        if ($score->count() > 1) {
            $result = $result->map(function ($q) {
                return $q / 2;
            });
        }


        GroupChannel::query()->firstWhere('group_id', 7)->tbas()->updateExistingPivot(61, ['c_score' => $result->toJson()]);
//            dd(GroupChannel::query()->firstWhere('group_id', 7)->tbas->where('id',61));
//        dd();
        dd($result->toJson());
//        $this->scoreService->findWhere(['tba_id' => $tba_id, 'flag' => ScoreType::Valid]);
    }
}
