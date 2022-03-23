<?php

use Illuminate\Database\Seeder;

class ConfigParametersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('config_parameters')->insert([
            // Habook
            ['cate' => 'Habook', 'attr' => 'ApiAuthorization', 'val' => null, 'name' => 'HABOOK Api Authorization', 'description' => null],
            // Vod
            ['cate' => 'Vod', 'attr' => 'Type', 'val' => null, 'name' => 'VOD Type', 'description' => null],
            // Msr
            ['cate' => 'Msr', 'attr' => 'Protocol', 'val' => 'http', 'name' => 'Protocol',   'description' => null],
            ['cate' => 'Msr', 'attr' => 'Dn',       'val' => null,   'name' => 'DomainName', 'description' => null],
            // AliyunVod
            ['cate' => 'AliyunVod', 'attr' => 'Protocol',        'val' => 'http', 'name' => 'Protocol',          'description' => null],
            ['cate' => 'AliyunVod', 'attr' => 'Dn',              'val' => null,   'name' => 'Domain Name',       'description' => null],
            ['cate' => 'AliyunVod', 'attr' => 'RegionId',        'val' => null,   'name' => 'Region ID',         'description' => null],
            ['cate' => 'AliyunVod', 'attr' => 'AccessKeyId',     'val' => null,   'name' => 'Access Key ID',     'description' => null],
            ['cate' => 'AliyunVod', 'attr' => 'AccessKeySecret', 'val' => null,   'name' => 'Access Key Secret', 'description' => null],
            ['cate' => 'AliyunVod', 'attr' => 'CategoryId',      'val' => null,   'name' => 'Category ID',       'description' => null],
        ]);
    }
}
