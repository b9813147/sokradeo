<?php

use Illuminate\Database\Seeder;

class TbaAnalysisEventModesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tba_analysis_event_modes')->insert([
            ['id' => 1,  'event' => 'PickupResult', 'mode' => 'PickupRange',      'type' => 0, 'color' => '#0000FF', 'style' => 0,    'order' => 1 ],
            ['id' => 2,  'event' => 'PickupResult', 'mode' => 'PickupNameLst',    'type' => 0, 'color' => '#8A2BE2', 'style' => 0,    'order' => 1 ],
            ['id' => 3,  'event' => 'PickupResult', 'mode' => 'PickupGrp',        'type' => 0, 'color' => '#8B2323', 'style' => 0,    'order' => 1 ],
            ['id' => 4,  'event' => 'PickupResult', 'mode' => 'PickupEachGrp',    'type' => 0, 'color' => '#008B00', 'style' => 0,    'order' => 1 ],
            ['id' => 5,  'event' => 'PickupResult', 'mode' => 'PickupNthGrp',     'type' => 0, 'color' => '#EE30A7', 'style' => 0,    'order' => 1 ],
            ['id' => 6,  'event' => 'PickupResult', 'mode' => 'PickupRight',      'type' => 0, 'color' => '#27408B', 'style' => 0,    'order' => 1 ],
            ['id' => 7,  'event' => 'PickupResult', 'mode' => 'PickupWrong',      'type' => 0, 'color' => '#9ACD32', 'style' => 0,    'order' => 1 ],
            ['id' => 8,  'event' => 'PickupResult', 'mode' => 'PickupOption',     'type' => 0, 'color' => '#8B4513', 'style' => 0,    'order' => 1 ],
            ['id' => 9,  'event' => 'PickupResult', 'mode' => 'PickupDiff',       'type' => 0, 'color' => '#EE9A00', 'style' => 0,    'order' => 1 ],
            ['id' => 10, 'event' => 'PickupResult', 'mode' => 'PickupNoDiff',     'type' => 0, 'color' => '#CD0000', 'style' => 0,    'order' => 1 ],
            ['id' => 11, 'event' => 'TimerResult',  'mode' => null,               'type' => 1, 'color' => '#00BFFF', 'style' => 1,    'order' => 2 ],
            ['id' => 12, 'event' => 'ScoBrd',       'mode' => null,               'type' => 1, 'color' => '#EE7AE9', 'style' => 1,    'order' => 3 ],
            ['id' => 13, 'event' => 'IRS',          'mode' => null,               'type' => 1, 'color' => '#4876FF', 'style' => 1,    'order' => 4 ],
            ['id' => 14, 'event' => 'PopQues',      'mode' => null,               'type' => 1, 'color' => '#7CCD7C', 'style' => 1,    'order' => 5 ],
            ['id' => 15, 'event' => 'ReAtmpAns',    'mode' => null,               'type' => 1, 'color' => '#CD6839', 'style' => 1,    'order' => 6 ],
            ['id' => 16, 'event' => 'ShowAns',      'mode' => null,               'type' => 1, 'color' => '#FF82AB', 'style' => 1,    'order' => 8 ],
            ['id' => 17, 'event' => 'BuzrLoad',     'mode' => null,               'type' => 1, 'color' => '#CDAD00', 'style' => 1,    'order' => 9 ],
            ['id' => 18, 'event' => 'StatChart',    'mode' => 'BarChart',         'type' => 1, 'color' => '#1874CD', 'style' => 1,    'order' => 7 ],
            ['id' => 19, 'event' => 'StatChart',    'mode' => 'PieChart',         'type' => 1, 'color' => '#228B22', 'style' => 1,    'order' => 7 ],
            ['id' => 20, 'event' => 'StatChart',    'mode' => 'GrpBarChart',      'type' => 1, 'color' => '#CD2626', 'style' => 1,    'order' => 7 ],
            ['id' => 21, 'event' => 'StatChart',    'mode' => 'GrpPieChart',      'type' => 1, 'color' => '#8B668B', 'style' => 1,    'order' => 7 ],
            ['id' => 22, 'event' => 'StatChart',    'mode' => 'GrpVerStackChart', 'type' => 1, 'color' => '#CDCD00', 'style' => 1,    'order' => 7 ],
            ['id' => 23, 'event' => 'StatChart',    'mode' => 'GrpCorrBarChart',  'type' => 1, 'color' => '#8B3626', 'style' => 1,    'order' => 7 ],
            ['id' => 24, 'event' => 'PgPush',       'mode' => null,               'type' => 1, 'color' => '#00CD00', 'style' => null, 'order' => 14],
            ['id' => 25, 'event' => 'PgPush',       'mode' => 'FastPgPush',       'type' => 0, 'color' => '#9F79EE', 'style' => 1,    'order' => 14],
            ['id' => 26, 'event' => 'PgPush',       'mode' => 'IndMember',        'type' => 0, 'color' => '#7A8B8B', 'style' => 1,    'order' => 14],
            ['id' => 27, 'event' => 'PgPush',       'mode' => 'GrpMember',        'type' => 0, 'color' => '#EE6AA7', 'style' => 1,    'order' => 14],
            ['id' => 28, 'event' => 'PgPush',       'mode' => 'InGrpMember',      'type' => 0, 'color' => '#4F94CD', 'style' => 1,    'order' => 14],
            ['id' => 29, 'event' => 'PgPush',       'mode' => 'AllMember',        'type' => 0, 'color' => '#EE9A49', 'style' => 1,    'order' => 14],
            ['id' => 30, 'event' => 'FdbkColl',     'mode' => null,               'type' => 1, 'color' => '#668B8B', 'style' => 1,    'order' => 10],
            ['id' => 31, 'event' => 'PgRcv',        'mode' => null,               'type' => 0, 'color' => '#473C8B', 'style' => 0,    'order' => 16],
            ['id' => 32, 'event' => 'BeamPg',       'mode' => null,               'type' => 1, 'color' => '#EE3A8C', 'style' => 1,    'order' => 15],
            ['id' => 33, 'event' => 'HitaLive',     'mode' => null,               'type' => 1, 'color' => '#00E5EE', 'style' => 1,    'order' => 11],
            ['id' => 34, 'event' => 'HitaSendVdo',  'mode' => null,               'type' => 0, 'color' => '#FF4500', 'style' => 0,    'order' => 12],
            ['id' => 35, 'event' => 'PlayVdo',      'mode' => null,               'type' => 0, 'color' => '#008B45', 'style' => 0,    'order' => 13],
            ['id' => 36, 'event' => 'WrkCmp',       'mode' => 'BeamPageCmp',      'type' => 0, 'color' => '#FF4040', 'style' => 0,    'order' => 17],
            ['id' => 37, 'event' => 'WrkCmp',       'mode' => 'WrkSpaceCmp',      'type' => 0, 'color' => '#CD853F', 'style' => 0,    'order' => 17],
            ['id' => 38, 'event' => 'WrkCmp',       'mode' => 'PrsnlPgCmp',       'type' => 0, 'color' => '#7A378B', 'style' => 0,    'order' => 17],
            ['id' => 39, 'event' => 'WrkCmp',       'mode' => 'HitaClientCmp',    'type' => 0, 'color' => '#43CD80', 'style' => 0,    'order' => 17],
            ['id' => 40, 'event' => 'WrkDis',       'mode' => null,               'type' => 0, 'color' => '#191970', 'style' => 0,    'order' => 18],
            ['id' => 41, 'event' => 'PickupResult', 'mode' => 'PickupRtoW',       'type' => 0, 'color' => '#00B2EE', 'style' => 0,    'order' => 1 ],
            ['id' => 42, 'event' => 'PickupResult', 'mode' => 'PickupWtoR',       'type' => 0, 'color' => '#8B7500', 'style' => 0,    'order' => 1 ],
        ]);
    }
}
