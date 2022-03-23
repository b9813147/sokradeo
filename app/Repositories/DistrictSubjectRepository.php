<?php

namespace App\Repositories;

use App\Models\DistrictSubject;
use Yish\Generators\Foundation\Repository\Repository;

class DistrictSubjectRepository
{
    protected $model;

    /**
     * DistrictSubjectRepository constructor.
     * @param $model
     */
    public function __construct(DistrictSubject $model)
    {
        $this->model = $model;
    }


}
