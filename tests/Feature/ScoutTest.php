<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\Tba;
use Tests\TestCase;

class ScoutTest extends TestCase
{
    public function testBasic()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testSearch()
    {
        $collection = Group::query()->limit(30)->get();
        dd($collection->toArray());
    }

}

