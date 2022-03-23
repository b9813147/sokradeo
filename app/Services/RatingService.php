<?php

namespace App\Services;

use App\Repositories\RatingRepository;
use Yish\Generators\Foundation\Service\Service;

class RatingService extends BaseService
{
    protected $repository;

    /**
     * RatingService constructor.
     * @param RatingRepository $repository
     */
    public function __construct(RatingRepository $repository)
    {
        $this->repository = $repository;
    }


}
