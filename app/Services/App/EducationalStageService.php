<?php

namespace App\Services\App;

use App\Repositories\EducationalStageRepository;

class EducationalStageService
{
    private $eduStageRepo = null;
    
    //
    public function __construct(EducationalStageRepository $eduStageRepo)
    {
        $this->eduStageRepo = $eduStageRepo;
    }
    
    //
    public function getEduStages()
    {
        return $this->eduStageRepo->getEduStages();
    }
}
