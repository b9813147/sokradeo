<?php

namespace App\Services;

use App\Repositories\DistrictGroupSubjectRepository;
use Yish\Generators\Foundation\Service\Service;

class DistrictGroupSubjectService extends Service
{
    protected $repository;

    /**
     * DistrictGroupSubjectService constructor.
     * @param $repository
     */
    public function __construct(DistrictGroupSubjectRepository $repository)
    {
        $this->repository = $repository;
    }


}
