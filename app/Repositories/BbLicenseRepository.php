<?php

namespace App\Repositories;

use App\Models\BbLicense;

class BbLicenseRepository extends BaseRepository
{
    protected $model;

    public function __construct(BbLicense $BbLicenseModel)
    {
        $this->model = $BbLicenseModel;
    }

    /**
     * Get all BB licenses
     */
    public function list()
    {
        return $this->model->all();
    }

    /**
     * Get BB licenses by Id
     * @param int $id
     */
    public function getById(int $id)
    {
        return $this->model->find($id);
    }
}
