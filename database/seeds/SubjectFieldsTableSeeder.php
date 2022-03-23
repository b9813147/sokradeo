<?php

use Illuminate\Database\Seeder;

class SubjectFieldsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('subject_fields')->insert([
            // Base
            ['id' => 1, 'type' => 'Language'          ],
            ['id' => 2, 'type' => 'Mathematics'       ],
            ['id' => 3, 'type' => 'Social-Humanities' ],
            ['id' => 4, 'type' => 'Science-Technology'],
            ['id' => 5, 'type' => 'Arts'              ],
            ['id' => 6, 'type' => 'Physical'          ],
        ]);
    }
}
