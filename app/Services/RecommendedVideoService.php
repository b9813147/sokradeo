<?php

namespace App\Services;

use App\Repositories\RecommendedVideoRepository;
use Yish\Generators\Foundation\Service\Service;

class RecommendedVideoService extends Service
{
    protected $repository;

    /**
     * RecommendedVideoService constructor.
     * @param $repository
     */
    public function __construct(RecommendedVideoRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get All Recommended Videos (Count)
     * @return int
     */
    public function getAllRecommendedVideosCount()
    {
        return $this->repository->getAllRecommendedVideos()->count();
    }

    //
    public function getPlatformRecommendedVideos($limit)
    {
        return $this->repository->getPlatformRecommendedVideos($limit);
    }

    /**
     * Get Recommendation Videos (paginated)
     * @param int $pagination
     * @return mixed
     */
    public function getRecommendationVideos($pagination = 10)
    {
        return $this->repository->getRecommendationVideos($pagination);
    }
}
