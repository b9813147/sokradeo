<?php

namespace App\Repositories;

use App\Models\Rating;
use Yish\Generators\Foundation\Repository\Repository;

class RatingRepository extends BaseRepository
{
    protected $model;

    /**
     * RatingRepository constructor.
     * @param Rating $model
     */
    public function __construct(Rating $model)
    {
        $this->model = $model;
    }


}
