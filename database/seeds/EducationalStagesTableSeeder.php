<?php

use Illuminate\Database\Seeder;

class EducationalStagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('educational_stages')->insert([
                // Base
                ['id' => 1, 'type' => 'Kindergarten',    'total_grade' => 3],
                ['id' => 2, 'type' => 'Elementary',      'total_grade' => 6],
                ['id' => 3, 'type' => 'Junior-High',     'total_grade' => 3],
                ['id' => 4, 'type' => 'High',            'total_grade' => 3],
                ['id' => 5, 'type' => 'Vocational-High', 'total_grade' => 3],
                ['id' => 6, 'type' => 'University',      'total_grade' => 4],
        ]);
    }
}
