<?php


namespace App\Repositories;


use App\Models\DistrictLang;
use Yish\Generators\Foundation\Repository\Repository;

class DistrictLangRepository extends Repository
{
    protected $model;

    /**
     * DistrictLangRepository constructor.
     * @param DistrictLang $districtLang
     */
    public function __construct(DistrictLang $districtLang)
    {
        $this->model = $districtLang;
    }
}
