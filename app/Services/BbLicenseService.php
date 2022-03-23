<?php

namespace App\Services;

use App\Repositories\BbLicenseRepository;
use App\Types\App\BbLicenseType;


class BbLicenseService extends BaseService
{
    protected $repository;

    public function __construct(BbLicenseRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get all BB licenses
     */
    public function getAll()
    {
        return $this->repository->list();
    }

    /**
     * Get by id
     * @param int $id
     */
    public function getById(int $id)
    {
        return $this->repository->getById($id);
    }

    /**
     * Get Obsrv Class BB license
     */
    public function getObsrvClassBbLicense()
    {
        $id = BbLicenseType::OBSRV_CLASS_LIMIT_ID;
        return $this->getById($id);
    }
}
