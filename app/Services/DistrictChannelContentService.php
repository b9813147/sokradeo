<?php

namespace App\Services;

use App\Repositories\DistrictChannelContentRepository;
use Yish\Generators\Foundation\Service\Service;

class DistrictChannelContentService extends Service
{
    protected $repository;

    /**
     * DistrictChannelContentService constructor.
     * @param $repository
     */
    public function __construct(DistrictChannelContentRepository $repository)
    {
        $this->repository = $repository;
    }


}
