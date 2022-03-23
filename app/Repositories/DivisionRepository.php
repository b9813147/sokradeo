<?php

namespace App\Repositories;

use App\Models\Division;

class DivisionRepository extends BaseRepository
{
    protected $model;

    /**
     * DivisionRepository constructor.
     * @param Division $model
     */
    public function __construct(Division $model)
    {
        $this->model = $model;
    }


    /**
     * @param array $columns
     * @return DivisionRepository[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function all($columns = ['*'])
    {
        return $this->model->query()->with('users', 'tbas')->get($columns);
    }

    /**
     * 查詢分組內的使者
     * @param int $group_id
     * @param int $user_id
     * @return Division|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function findByUsers(int $group_id, int $user_id)
    {
        return $this->model->with([
            'users' => function ($user) use ($user_id) {
                return $user->where('user_id', $user_id);
            },
            'tbas' => function ($q) {
                $q->with(['videos' => function ($q) {
                    $q->select('resource_id');
                    $q->with(['resource' => function ($q) {
                        $q->select('id');
                        $q->with(['vod' => function ($q) {
                            $q->select('resource_id', 'rdata');
                        }]);
                    }]);
                }]);
            },
        ])->where('group_id', $group_id)->get();
    }
}
