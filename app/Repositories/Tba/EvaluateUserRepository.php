<?php

namespace App\Repositories\Tba;

use Illuminate\Support\Facades\DB;
use App\Models\TbaEvaluateUser;

class EvaluateUserRepository
{
    //
    public function __construct()
    {
        
    }
    
    //
    public function getUsers($tbaId, $enableGuestEvents)
    {
        $db = DB::table('tba_evaluate_users')
            ->where('tba_id', $tbaId)
            ->join('users', 'tba_evaluate_users.user_id', '=', 'users.id')
            ->orderBy('identity', 'asc')
            ->select('tba_evaluate_users.id', 'tba_evaluate_users.identity', 'tba_evaluate_users.user_id', 'users.name');
        
        if ($enableGuestEvents !== true) {
            $db->where('identity', '!=', 'G');
        }
        
        return $db->get();
    }
    
    //
    public function getUserOrCreate($tbaId, $userId, $identity)
    {
        return TbaEvaluateUser::firstOrCreate([
                'tba_id'   => $tbaId,
                'user_id'  => $userId,
                'identity' => $identity,
        ]);
    }

    //
    public function getUserByEvalUserId($tbaId, $evalUserID)
    {
        return DB::table('tba_evaluate_users')
            ->where('tba_id', $tbaId)
            ->where('tba_evaluate_users.id', $evalUserID)
            ->join('users', 'tba_evaluate_users.user_id', '=', 'users.id')
            ->orderBy('identity', 'asc')
            ->select('tba_evaluate_users.id', 'tba_evaluate_users.identity', 'tba_evaluate_users.user_id', 'users.name')
            ->get();
    }

    //
    public function getUserByEvalUserIds($tbaId, $evalUserIDs, $evalModes, $enableGuestEvents)
    {
        $db = DB::table('tba_evaluate_users')
            ->where('tba_evaluate_users.tba_id', $tbaId)
            ->join('users', 'tba_evaluate_users.user_id', '=', 'users.id')
            ->join('tba_evaluate_events', 'tba_evaluate_users.id', '=', 'tba_evaluate_events.tba_evaluate_user_id')
            ->join('tba_evaluate_event_modes', 'tba_evaluate_events.tba_evaluate_event_mode_id', '=', 'tba_evaluate_event_modes.id')
            ->groupBy('tba_evaluate_users.user_id')
            ->orderBy('tba_evaluate_users.identity', 'asc')
            ->select('tba_evaluate_users.id', 'tba_evaluate_users.identity', 'tba_evaluate_users.user_id', 'users.name');
        
        if ($enableGuestEvents !== true) {
            $db->where('tba_evaluate_users.identity', '!=', 'G');
        }
        if (!empty($evalUserIDs)) {
            $db->whereIn('tba_evaluate_users.id', $evalUserIDs);
        }
        if (!empty($evalModes)) {
            $db->whereIn('tba_evaluate_event_modes.mode', $evalModes);
        }
            
        return $db->get();
    }

    //
    public function getUserByUserID($userID)
    {
        return DB::table('users')
            ->where('id', $userID)
            ->select('id', 'name', 'id as user_id')
            ->get();
    }
}
