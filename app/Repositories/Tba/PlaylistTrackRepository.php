<?php

namespace App\Repositories\Tba;

use App\Models\TbaPlaylistTrack;

class PlaylistTrackRepository
{
    //
    public function __construct()
    {
        
    }
    
    //
    public function getPlaylistTracks($tbaId)
    {
        return TbaPlaylistTrack::where('tba_id', $tbaId)->orderBy('list_order', 'asc')->get();
    }
}
