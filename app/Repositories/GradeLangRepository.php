<?php

namespace App\Repositories;

use App\Models\GradeLang;
use Yish\Generators\Foundation\Repository\Repository;

class GradeLangRepository extends Repository
{
    protected $model;

    /**
     * GroupLangRepository constructor.
     * @param $model
     */
    public function __construct(GradeLang $model)
    {
        $this->model = $model;
    }


}
