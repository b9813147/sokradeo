<?php

namespace App\Repositories;

use App\Models\DistrictUser;
use Yish\Generators\Foundation\Repository\Repository;

class DistrictUserRepository
{
    protected $model;

    /**
     * DistrictUserRepository constructor.
     * @param $model
     */
    public function __construct(DistrictUser $model)
    {
        $this->model = $model;
    }


}
