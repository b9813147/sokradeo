<?php

use Illuminate\Database\Seeder;

class TbaFeaturesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tba_features')->insert([
                // Base
                ['id' => 1, 'type' => 'Reanswer-Question'         ],
                ['id' => 2, 'type' => 'Differentiated-Instruction'],
                ['id' => 3, 'type' => 'Distance-Instruction'      ],
                ['id' => 4, 'type' => 'Tbl'                       ],
                ['id' => 5, 'type' => 'Peer-Instruction'          ],
                ['id' => 6, 'type' => 'Live-Demo'                 ],
        ]);
    }
}
