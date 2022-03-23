<?php

namespace Tests\Feature;

use App\Models\TbaAnalysisAutopedaType;
use Tests\TestCase;

class tba_analysis_autopeda_typeTest extends TestCase
{
    public function testBasic()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testRunMakeSeeder()
    {
        $data = ['LTK', 'SCD', 'WCT', 'WCI', 'DFI'];
        foreach ($data as $item) {
            TbaAnalysisAutopedaType::query()->create([
                'ptype' => $item,
            ]);
        }

    }
}
