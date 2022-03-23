<?php

namespace App\Repositories\Tba;

use Exception;
use App\Models\TbaHistory;

class HistoryRepository
{
    //
    public function __construct()
    {
    }

    //
    public function list($page = 1)
    {
        return TbaHistory::with('tba')->paginate(null, ['*'], 'page', $page);
    }

    //
    public function listByUserId($userId, $page = 1)
    {
        return TbaHistory::where('user_id', $userId)->with('tba')->paginate(null, ['*'], 'page', $page);
    }

    /**
     * Get Watch History
     * @param int $userId
     * @param int $paginate
     * @return TbaHistory[]|\Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|Collection
     */
    public function getHists($userId, $paginate = 100)
    {
        return TbaHistory::where('user_id', $userId)
            ->with('tba')
            ->where('url', '!=', null)
            ->orderBy('updated_at', 'desc')
            ->paginate($paginate);
    }

    //
    public function getHist($userId, $tbaId)
    {
        return TbaHistory::where(['user_id' => $userId, 'tba_id' => $tbaId])->with('tba')->firstOrFail();
    }

    //
    public function createHist($userId, $tbaId, $url = null)
    {
        $now = date('Y-m-d H:i:s');

        $conds = ['user_id' => $userId, 'tba_id' => $tbaId, 'url' => $url];

        if (TbaHistory::where($conds)->exists()) {
            TbaHistory::where($conds)->update(['updated_at' => $now]);
        } else {
            $conds['updated_at'] = $now;
            TbaHistory::create($conds);
        }
    }

    /**
     * Delete all Watch History from a user
     * @param int $userId
     * @return Bool
     */
    public function deleteHists($userId)
    {
        $isSuccessful = false;
        $cond = ['user_id' => $userId];
        if (TbaHistory::where($cond)->exists()) {
            TbaHistory::where($cond)->delete();
            $isSuccessful = true;
        }
        return $isSuccessful;
    }
}
