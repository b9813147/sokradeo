<?php

namespace App\Repositories;

use App\Models\EducationalStage;

class EducationalStageRepository
{
    //
    public function __construct()
    {
        
    }
    
    //
    public function getEduStages()
    {
        return EducationalStage::all();
    }
}
