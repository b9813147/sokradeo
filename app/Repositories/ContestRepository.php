<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class ContestRepository
{
    //
    public function __construct()
    {
        
    }

    //
    public function getSubmissionStatus($groupID, $habook)
    {
        return DB::table('group_channel_contents')
            ->where('group_channel_contents.group_id', $groupID)
            ->where('users.habook', $habook)
            ->join('tbas', 'group_channel_contents.content_id', '=', 'tbas.id')
            ->join('users', 'tbas.user_id', '=', 'users.id')
            ->orderBy('group_channel_contents.created_at', 'desc')
            ->select('tbas.name', 'group_channel_contents.created_at', 'group_channel_contents.content_status', 'group_channel_contents.content_id', 'group_channel_contents.group_channel_id')
            ->get();
    }
}
