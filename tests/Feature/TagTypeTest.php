<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\TagType;
use App\Models\TbaTag;
use Tests\TestCase;

class TagTypeTest extends TestCase
{
    public function testBasic()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_baseData()
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Tag::query()->truncate();
        TagType::query()->truncate();

        $baseData = collect(json_decode(file_get_contents(public_path('/baseTagAndTypeNew.json'))));
        $baseData->each(function ($q) {
            TagType::query()->create([
                'id'      => $q->typeId,
                'order'   => $q->order,
                'content' => json_encode($q->typeName),
            ]);
            foreach ($q->tagList as $item) {
                Tag::query()->create([
                    'id'      => $item->id,
                    'content' => json_encode(['name' => $item->name, 'description' => $item->description]),
                    'type_id' => $q->typeId,
                ]);
            }
        });
        \DB::statement('SET FOREIGN_KEY_CHECKS=1');
        $this->assertTrue(true);
    }
}
