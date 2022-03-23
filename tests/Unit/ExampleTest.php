<?php

namespace Tests\Unit;

use App\Models\Tba;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $builder = Tba::query()->where(["DB::rayyear(lecture_date)" => 2019])->get();
        dd($builder);
        $this->assertTrue(true);
    }
}
