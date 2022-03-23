<?php

namespace App\Repositories;

use App\Models\ObservationClass;

class ObservationClassRepository
{
    protected $model;

    public function __construct(ObservationClass $model)
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
     * Get the data from table by user id.
     * @param int $userId
     */
    public function getAllByUserIdAsQuery(int $userId)
    {
        return $this->model->where('user_id', $userId);
    }

    /**
     * Get the data from table by id.
     * @param int $id
     */
    public function getById(int $id): ObservationClass
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Get the data from table by classroom code.
     * @param string $classroomCode
     */
    public function getByClassroomCodeAsQuery(string $classroomCode)
    {
        return $this->model->where('classroom_code', $classroomCode);
    }

    /**
     * Create a new record in table.
     * @param array $attributes
     */
    public function createObsrvClass(array $attributes): ObservationClass
    {
        return $this->model->create($attributes);
    }

    /**
     * Update the record in table.
     * @param int $id
     * @param array $attributes
     */
    public function updateObsrvClass(int $id, array $attributes): ObservationClass
    {
        $model = $this->model->find($id);
        $model->update($attributes);
        return $model;
    }

    /**
     * Delete data by id
     * @param int $id
     */
    public function deleteObsrvClass(int $id): bool
    {
        return $this->model->destroy($id);
    }
}
