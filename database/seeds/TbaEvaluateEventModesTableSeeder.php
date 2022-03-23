<?php

use Illuminate\Database\Seeder;

class TbaEvaluateEventModesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tba_evaluate_event_modes')->insert([
            ['id' => 1,  'identity' => 'E', 'event' => 'EgPos',  'mode' => 'EgNew',    'type' => 0, 'color' => '#FF0000', 'style' => null, 'order' => 1],
            ['id' => 2,  'identity' => 'E', 'event' => 'EgPos',  'mode' => 'EgAgree',  'type' => 0, 'color' => '#FD6767', 'style' => null, 'order' => 1],
            ['id' => 3,  'identity' => 'E', 'event' => 'EgNeg',  'mode' => 'EgDisAgr', 'type' => 0, 'color' => '#4949FD', 'style' => null, 'order' => 2],
            ['id' => 4,  'identity' => 'E', 'event' => 'EgNeg',  'mode' => 'EgQues',   'type' => 0, 'color' => '#46b8da', 'style' => null, 'order' => 2],
            ['id' => 5,  'identity' => 'E', 'event' => 'EgNeu',  'mode' => 'EgOther',  'type' => 0, 'color' => '#FF9900', 'style' => null, 'order' => 3],
            ['id' => 6,  'identity' => 'E', 'event' => 'EgNeu',  'mode' => 'EgTkPic',  'type' => 0, 'color' => '#66CC00', 'style' => null, 'order' => 3],
            ['id' => 7,  'identity' => 'V', 'event' => 'VgPos',  'mode' => 'VgLearn',  'type' => 0, 'color' => '#FF0000', 'style' => null, 'order' => 1],
            ['id' => 8,  'identity' => 'V', 'event' => 'VgPos',  'mode' => 'VgAgree',  'type' => 0, 'color' => '#FD6767', 'style' => null, 'order' => 1],
            ['id' => 9,  'identity' => 'V', 'event' => 'VgNeg',  'mode' => 'VgDisAgr', 'type' => 0, 'color' => '#4949FD', 'style' => null, 'order' => 2],
            ['id' => 10, 'identity' => 'V', 'event' => 'VgNeg',  'mode' => 'VgQues',   'type' => 0, 'color' => '#46b8da', 'style' => null, 'order' => 2],
            ['id' => 11, 'identity' => 'V', 'event' => 'VgNeu',  'mode' => 'VgOther',  'type' => 0, 'color' => '#FF9900', 'style' => null, 'order' => 3],
            ['id' => 12, 'identity' => 'V', 'event' => 'VgNeu',  'mode' => 'VgTkPic',  'type' => 0, 'color' => '#66CC00', 'style' => null, 'order' => 3],
            ['id' => 13, 'identity' => 'T', 'event' => 'TgNote', 'mode' => null,       'type' => 0, 'color' => '#0000FF', 'style' => null, 'order' => 1],
            ['id' => 14, 'identity' => 'U', 'event' => 'UgPos',  'mode' => 'UgLearn',  'type' => 0, 'color' => '#FF0000', 'style' => null, 'order' => 1],
            ['id' => 15, 'identity' => 'U', 'event' => 'UgPos',  'mode' => 'UgAgree',  'type' => 0, 'color' => '#FD6767', 'style' => null, 'order' => 1],
            ['id' => 16, 'identity' => 'U', 'event' => 'UgNeg',  'mode' => 'UgDisAgr', 'type' => 0, 'color' => '#4949FD', 'style' => null, 'order' => 2],
            ['id' => 17, 'identity' => 'U', 'event' => 'UgNeg',  'mode' => 'UgQues',   'type' => 0, 'color' => '#46b8da', 'style' => null, 'order' => 2],
            ['id' => 18, 'identity' => 'U', 'event' => 'UgNeu',  'mode' => 'UgOther',  'type' => 0, 'color' => '#FF9900', 'style' => null, 'order' => 3],
                
        ]);
    }
}
