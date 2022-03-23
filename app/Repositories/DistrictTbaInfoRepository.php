<?php


namespace App\Repositories;


use App\Models\DistrictTbaInfo;
use Yish\Generators\Foundation\Repository\Repository;

class DistrictTbaInfoRepository extends Repository
{
    protected $model;

    /**
     * DistrictTbaInfoRepository constructor.
     * @param DistrictTbaInfo $districtTbaInfo
     */
    public function __construct(DistrictTbaInfo $districtTbaInfo)
    {
        $this->model = $districtTbaInfo;
    }
}
