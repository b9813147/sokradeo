<?php

namespace App\Services;

use App\Repositories\GroupSubjectFieldsRepository;
use Yish\Generators\Foundation\Service\Service;

class GroupSubjectFieldsService extends BaseService
{
    protected $repository;

    /**
     * GroupSubjectFieldsService constructor.
     * @param GroupSubjectFieldsRepository $repository
     */
    public function __construct(GroupSubjectFieldsRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param array $attributes
     * @param array $values
     * @return \App\Models\GroupSubjectFields
     */
    public function updateOrCreate(array $attributes, array $values = [])
    {
        return $this->repository->updateOrCreate($attributes, $values);
    }

}
