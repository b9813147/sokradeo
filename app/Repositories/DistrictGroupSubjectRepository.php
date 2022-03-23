<?php

namespace App\Repositories;

use App\Models\DistrictGroupSubject;
use Yish\Generators\Foundation\Repository\Repository;

class DistrictGroupSubjectRepository extends Repository
{
    protected $model;

    /**
     * DistrictGroupSubjectRepository constructor.
     * @param $model
     */
    public function __construct(DistrictGroupSubject $model)
    {
        $this->model = $model;
    }


}
