<?php

use App\Models\GlobalNormRef;
use Illuminate\Database\Seeder;

class GlobalNormRefsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Add 2020 Global Norm Refs Data
        GlobalNormRef::query()->insert(
            [
                'year'          => 2020,
                'p1'            => 37.3,
                'p2'            => 59.3,
                'p3'            => 40.9,
                'p4'            => 8.9,
                'p5'            => 17.4,
                'p6'            => 59.2,
                'freq'          => 6.9,
                'tech_interact' => 82.8,
                'peda_app'      => 70.8,
                'feedback_avg'  => 3.9,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s')
            ]
        );
    }
}
