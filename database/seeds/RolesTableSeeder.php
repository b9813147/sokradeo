<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            // Base
            ['id' => 1, 'type' => 'Root',    'name' => 'Root',    'description' => 'Root'   ],
            ['id' => 2, 'type' => 'Admin',   'name' => 'Admin',   'description' => 'Admin'  ],
            ['id' => 3, 'type' => 'Manager', 'name' => 'Manager', 'description' => 'Manager'],
            ['id' => 4, 'type' => 'Test',    'name' => 'Test',    'description' => 'Test'   ],
            ['id' => 5, 'type' => 'Expert',  'name' => 'Expert',  'description' => 'Expert' ],
            ['id' => 6, 'type' => 'Teacher', 'name' => 'Teacher', 'description' => 'Teacher'],
            ['id' => 7, 'type' => 'Parent',  'name' => 'Parent',  'description' => 'Parent' ],
            ['id' => 8, 'type' => 'Student', 'name' => 'Student', 'description' => 'Student'],
        ]);
    }
}
