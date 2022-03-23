<?php

namespace App\Repositories;

use App\Models\RecommendedVideo;
use Yish\Generators\Foundation\Repository\Repository;

class RecommendedVideoRepository extends Repository
{
    protected $model;

    /**
     * @param RecommendedVideo $model
     */
    public function __construct(RecommendedVideo $model)
    {
        $this->model = $model;
    }

    /**
     * Get All Recommended Videos
     */
    public function getAllRecommendedVideos()
    {
        return $this->model->all();
    }

    //
    public function getPlatformRecommendedVideos($limit = 1)
    {
        return RecommendedVideo::query()->with([
            'tba' => function ($q) {
                $q->with('groupChannels');
            }
        ])->limit($limit)->orderBy('order')->get();
    }

    /**
     * Get Recommendation Videos (paginated)
     * @param int $pagination
     */
    public function getRecommendationVideos($pagination = 10)
    {
        return $this->model::query()->with([
            'tba' => function ($q) {
                $q->with('groupChannels');
            }
        ])->paginate($pagination);
    }
}
