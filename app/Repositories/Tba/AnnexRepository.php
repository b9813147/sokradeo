<?php

namespace App\Repositories\Tba;

use Illuminate\Support\Facades\DB;
use App\Models\TbaAnnex;
use App\Models\Resource;

class AnnexRepository
{
    //
    public function __construct()
    {
        
    }
    
    //
    public function getAnnexes($tbaId, $conds = [])
    {
        return TbaAnnex::where('tba_id', $tbaId)->where($conds)->get();
    }
    
    //
    public function getAnnex($annexId)
    {
        return TbaAnnex::with('resource')->findOrFail($annexId);
    }
    
    //
    public function getAnnexResrcs($tbaId, $conds = [])
    {
        return DB::table('tba_annexes')
            ->where('tba_id', $tbaId)
            ->join('resources', 'tba_annexes.resource_id', '=', 'resources.id')
            ->where($conds)
            ->select('tba_annexes.*', 'resources.user_id', 'resources.src_type', 'resources.name')
            ->get();
    }
    
    //
    public function getAnnexResrc($annexId)
    {
        return DB::table('tba_annexes')
            ->where('tba_annexes.id', $annexId)
            ->where('tba_id', $tbaId)
            ->join('resources', 'tba_annexes.resource_id', '=', 'resources.id')
            ->where($conds)
            ->select('tba_annexes.*', 'resources.user_id', 'resources.src_type', 'resources.name')
            ->get();
    }
    
    //
    public function createAnnex($tbaId, & $annex)
    {
        $annex['tba_id'] = $tbaId;
        return TbaAnnex::create($annex);
    }
    
    //
    public function getResrc($annexId)
    {
        return TbaAnnex::findOrFail($annexId)->resource;
    }
    
}
