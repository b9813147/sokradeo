<?php

namespace App\Repositories\Tba;

use App\Models\TbaFavorite;

class FavoriteRepository
{
    //
    public function __construct()
    {
        
    }
    
    //
    public function list($page = 1)
    {
        return TbaFavorite::with('tba')->paginate(null, ['*'], 'page', $page);
    }
    
    //
    public function listByUserId($userId, $page = 1)
    {
        return TbaFavorite::where('user_id', $userId)->with('tba')->paginate(null, ['*'], 'page', $page);
    }
    
    /**
     * Get Favorite Videos by User
     * @param int $userId
     * @param int $paginate
     * @return TbaFavorite[]|\Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|Collection
     */
    public function getFavs($userId, $paginate = 100)
    {
        return TbaFavorite::where('user_id', $userId)
            ->with('tba')
            ->orderBy('updated_at', 'desc')
            ->paginate($paginate);
    }
    
    //
    public function getFav($userId, $tbaId)
    {
        return TbaFavorite::where(['user_id' => $userId, 'tba_id' => $tbaId])->with('tba')->firstOrFail();
    }
    
    /**
     * Add a tba video to Favorite
     * @param int $userId
     * @param int $channelId
     * @param int $groupId
     * @param int $tbaId
     * @return TbaFavorite
     */
    public function updateOrCreateFav($userId, $channelId, $groupId, $tbaId)
    {
        $now = date('Y-m-d H:i:s');
        
        $conds = [
            'user_id' => $userId,
            'channel_id' => $channelId,
            'group_id' => $groupId,
            'tba_id' => $tbaId
        ];
        
        if (TbaFavorite::where($conds)->exists()) {
            TbaFavorite::where($conds)->update(['updated_at' => $now]);
        } else {
            TbaFavorite::create($conds);
        }
        return TbaFavorite::where($conds)->get();
    }

    /**
     * Delete a tba video from Favorite
     * @param int $userId
     * @param int $channelId
     * @param int $groupId
     * @param int $tbaId
     * @return Bool
     */
    public function deleteFav($userId, $channelId, $groupId, $tbaId)
    {
        $isSuccessful = false;
        $conds = [
            'user_id' => $userId,
            'channel_id' => $channelId,
            'group_id' => $groupId,
            'tba_id' => $tbaId
        ];
        if (TbaFavorite::where($conds)->exists()) {
            TbaFavorite::where($conds)->delete();
            $isSuccessful = true;
        }
        return $isSuccessful;
    }
}
