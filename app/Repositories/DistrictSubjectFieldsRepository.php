<?php


namespace App\Repositories;


use App\Models\DistrictSubjectField;
use Yish\Generators\Foundation\Repository\Repository;

class DistrictSubjectFieldsRepository extends Repository
{
    protected $model;

    /**
     * DistrictSubjectFieldsRepository constructor.
     * @param DistrictSubjectField $districtSubjectField
     */
    public function __construct(DistrictSubjectField $districtSubjectField)
    {
        $this->model = $districtSubjectField;
    }

}
