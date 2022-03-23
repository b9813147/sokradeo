<?php

namespace App\Services\App;

use App\Repositories\SubjectFieldRepository;

class SubjectFieldService
{
    private $subjectFieldRepo = null;
    
    //
    public function __construct(SubjectFieldRepository $subjectFieldRepo)
    {
        $this->subjectFieldRepo = $subjectFieldRepo;
    }
    
    //
    public function getSubjectFields()
    {
        return $this->subjectFieldRepo->getSubjectFields();
    }
}
