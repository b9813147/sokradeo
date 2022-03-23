<?php

namespace App\Services\Management;

use App\Repositories\ModuleRepository;

class ModuleService
{
    private $moduleRepo = null;
    
    //
    public function __construct(ModuleRepository $moduleRepo)
    {
        $this->moduleRepo = $moduleRepo;
    }
    
    //
    public function list()
    {
        return $this->moduleRepo->list();
    }
}
