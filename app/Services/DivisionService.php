<?php

namespace App\Services;

use App\Repositories\DivisionRepository;

class DivisionService extends BaseService
{
    protected $repository;

    /**
     * DivisionService constructor.
     * @param DivisionRepository $repository
     */
    public function __construct(DivisionRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return DivisionRepository[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return $this->repository->all();
    }

    /**
     * 查詢分組內的使者
     * @param int $group_id
     * @param int $user_id
     * @return \App\Models\Division|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function findByUsers(int $group_id, int $user_id)
    {
        return $this->repository->findByUsers($group_id, $user_id);
    }


}
