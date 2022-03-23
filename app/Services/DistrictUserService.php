<?php

namespace App\Services;

use App\Repositories\DistrictUserRepository;
use Yish\Generators\Foundation\Service\Service;

class DistrictUserService extends Service
{
    protected $repository;

    /**
     * DistrictUserService constructor.
     * @param $repository
     */
    public function __construct(DistrictUserRepository $repository)
    {
        $this->repository = $repository;
    }


}
