<?php

namespace App\Services;

use App\Repositories\DistrictSubjectRepository;
use Yish\Generators\Foundation\Service\Service;

class DistrictSubjectService extends Service
{
    protected $repository;

    /**
     * DistrictSubjectService constructor.
     * @param $repository
     */
    public function __construct(DistrictSubjectRepository $repository)
    {
        $this->repository = $repository;
    }


}
