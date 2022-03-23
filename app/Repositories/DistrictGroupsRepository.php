<?php


namespace App\Repositories;


use App\Models\DistrictGroup;
use Yish\Generators\Foundation\Repository\Repository;

class DistrictGroupsRepository extends Repository
{
    protected $model;

    /**
     * DistrictGroupsRepository constructor.
     * @param DistrictGroup $districtGroup
     */
    public function __construct(DistrictGroup $districtGroup)
    {
        $this->model = $districtGroup;
    }

}
