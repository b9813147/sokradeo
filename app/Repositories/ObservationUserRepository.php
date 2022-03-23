<?php

namespace App\Repositories;

use App\Models\ObservationUser;

class ObservationUserRepository
{
    protected $model;

    public function __construct(ObservationUser $model)
    {
        $this->model = $model;
    }

    /**
     * Get all data from table.
     * @return mixed
     */
    public function getAll()
    {
        return $this->model->all();
    }

    /**
     * Get data by id
     * @param int $id
     */
    public function getById(int $id): ObservationUser
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Create new data to table
     * @param array $attributes
     */
    public function createObsrvUser(array $attributes): ObservationUser
    {
        return $this->model->create($attributes);
    }

    /**
     * Update data by id
     * @param int $id
     * @param array $attributes
     */
    public function updateObsrvUser(int $id, array $attributes): ObservationUser
    {
        $model = $this->model->find($id);
        $model->update($attributes);
        return $model;
    }

    /**
     * Delete data by id
     * @param int $id
     */
    public function deleteObsrvUser(int $id): bool
    {
        return $this->model->destroy($id);
    }

    /**
     * Get data by user id
     * @param int $userId
     */
    public function getObsrvUserByUserId(int $userId): ObservationUser
    {
        return $this->model->where('user_id', $userId)->first();
    }

    /**
     * Get data by observation class id
     * @param int $obsrvClassId
     */

    public function getObsrvUserByObsrvClassId(int $obsrvClassId): Collection
    {
        return $this->model->where('observation_class_id', $obsrvClassId)->get();
    }
}
