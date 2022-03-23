<?php

namespace App\Services;

use App\Repositories\GradeLangRepository;
use Yish\Generators\Foundation\Service\Service;

class GradeLangService extends Service
{
    protected $repository;

    /**
     * GradeLangService constructor.
     * @param $repository
     */
    public function __construct(GradeLangRepository $repository)
    {
        $this->repository = $repository;
    }


}
