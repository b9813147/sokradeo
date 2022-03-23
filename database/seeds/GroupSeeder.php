<?php

use App\Models\Group;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    public function run()
    {
        $this->add_abbr_to_group();
    }

    public function add_abbr_to_group()
    {
        $data = collect(json_decode(file_get_contents(public_path('school_code.json'))));
        $data->each(function ($q) {
            Group::query()->where('school_code', $q->SchoolCode)->update(['abbr' => $q->Abbr]);
        });
    }
}
