<?php

namespace App\Services\Tba;

use App\Repositories\Tba\FavoriteRepository;

class FavoriteService
{
    protected $repository;

    /**
     * FavoriteService constructor.
     * @param FavoriteRepository $favoriteRepository
     */
    public function __construct(FavoriteRepository $favoriteRepository)
    {
        $this->repository = $favoriteRepository;
    }

    /**
     * Get Favorite Videos by User
     * @param int $userId
     * @param int $paginate
     * @return FavoriteRepository[]|\Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getTbaVideoFavs($userId, $paginate)
    {
        return $this->repository->getFavs($userId, $paginate);
    }

    /**
     * Add a tba video to Favorite
     * @param int $userId
     * @param int $channelId
     * @param int $groupId
     * @param int $tbaId
     * @return TbaFavorite
     */
    public function updateOrCreateTbaVideoFav($userId, $channelId, $groupId, $tbaId)
    {
        return $this->repository->updateOrCreateFav($userId, $channelId, $groupId, $tbaId);
    }

    /**
     * deleteTbaVideoFav a tba video from Favorite
     * @param int $userId
     * @param int $channelId
     * @param int $groupId
     * @param int $tbaId
     * @return Bool
     */
    public function deleteTbaVideoFav($userId, $channelId, $groupId, $tbaId)
    {
        return $this->repository->deleteFav($userId, $channelId, $groupId, $tbaId);
    }
}
