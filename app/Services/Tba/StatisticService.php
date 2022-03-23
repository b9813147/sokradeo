<?php

namespace App\Services\Tba;

use App\Repositories\Tba\StatisticRepository;
use App\Services\BaseService;

class StatisticService extends BaseService
{
    protected $repository;

    /**
     * TbaStatisticService constructor.
     * @param StatisticRepository $statisticRepository
     */
    public function __construct(StatisticRepository $statisticRepository)
    {
        $this->repository = $statisticRepository;
    }

    /**
     * @param int $tba_id
     * @param array $type
     * @return bool
     */
    public function checkTPNumber(int $tba_id, array $type): bool
    {
        return $this->repository->checkTPNumber($tba_id, $type);
    }
}
