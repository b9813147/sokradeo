<?php

namespace App\Services;

use App\Repositories\DesignatedVideoRepository;
use Yish\Generators\Foundation\Service\Service;

class DesignatedVideoService extends Service
{
    protected $repository;

    /**
     * DesignatedVideoService constructor.
     * @param $repository
     */
    public function __construct(DesignatedVideoRepository $repository)
    {
        $this->repository = $repository;
    }
    
    //
    public function getDesignatedInfo($groupId, $tbaId, $teamModelId)
    {
        return $this->repository->getDesignatedInfo($groupId, $tbaId, $teamModelId);
    }
}
