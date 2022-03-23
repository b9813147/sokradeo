<?php

namespace App\Repositories;

use App\Models\Grade;
use Yish\Generators\Foundation\Repository\Repository;

class GradeRepository extends Repository
{
    protected $model;

    //

    /**
     * GradeFieldsRepository constructor.
     * @param Grade $grade
     */
    public function __construct(Grade $grade)
    {
        $this->model = $grade;
    }
}
