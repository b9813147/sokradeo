<?php

namespace App\Repositories;

use Yish\Generators\Foundation\Repository\Repository;

class NotificationRepository extends Repository
{
    protected $model;

    /**
     * NotificationRepository constructor.
     * @param $model
     */
    public function __construct($model)
    {
        $this->model = $model;
    }


}
