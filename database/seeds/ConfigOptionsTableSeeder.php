<?php

use Illuminate\Database\Seeder;

class ConfigOptionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('config_options')->insert([
            // Vod:Type
            ['cate' => 'Vod', 'attr' => 'Type', 'param' => 'Msr',       'name' => 'MSR',    'description' => null],
            ['cate' => 'Vod', 'attr' => 'Type', 'param' => 'AliyunVod', 'name' => 'Aliyun', 'description' => null],
        ]);
    }
}
