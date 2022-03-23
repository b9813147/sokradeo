<?php

namespace App\Repositories;

use App\Models\Score;

class ScoreRepository extends BaseRepository
{
    protected $model;

    /**
     * ScoreRepository constructor.
     * @param Score $model
     */
    public function __construct(Score $model)
    {
        $this->model = $model;
    }


}
