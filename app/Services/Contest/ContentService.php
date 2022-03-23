<?php

namespace App\Services\Contest;

use LogicException;
use App\Repositories\ContestRepository;

class ContentService
{
    private $contestRepo = null;
    
    //
    public function __construct(ContestRepository $contestRepo)
    {
        $this->contestRepo = $contestRepo;
    }
    
    //
    public function getSubmissionStatus($groupID, $habook)
    {
        return $this->contestRepo->getSubmissionStatus($groupID, $habook);
    }
    
}
