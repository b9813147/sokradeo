<?php

namespace Tests\Feature;

use App\Models\GroupChannelContent;
use App\Models\Tba;
use Tests\TestCase;

class TbaBackupTest extends TestCase
{
    public function testBasic()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testBackup()
    {
        $tbaIds               = Tba::query()->pluck('id');
        $groupChannelContents = Tba::query()->leftJoin('group_channel_contents', 'tbas.id', 'group_channel_contents.content_id')
            ->whereNull('group_channel_contents.content_id')->pluck('id');

        Tba::query()->whereIn('id', $groupChannelContents)->delete();
    }
}
