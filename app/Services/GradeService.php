<?php

namespace App\Services;

use App\Repositories\GradeRepository;
use Yish\Generators\Foundation\Service\Service;

class GradeService extends Service
{
    protected $repository;
    //

    /**
     * GradeFieldsService constructor.
     * @param GradeRepository $gradeFieldsRepository
     */
    public function __construct(GradeRepository $gradeFieldsRepository)
    {
        $this->repository = $gradeFieldsRepository;
    }
}
