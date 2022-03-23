<?php

use App\Models\Application;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class ApplicationSeeder extends Seeder
{
    public function run()
    {
        $defaults = collect([
            [
                'id'          => Str::uuid()->toString(),
                'type'        => 'HiTeach',
                'expire_date' => Carbon::now()->addYear(1),
            ], [
                'id'          => Str::uuid()->toString(),
                'type'        => 'Mobile',
                'expire_date' => Carbon::now()->addYear(1),
            ]
        ]);
        $defaults->each(function ($q) {
            Application::query()->create($q);
        });

    }
}
