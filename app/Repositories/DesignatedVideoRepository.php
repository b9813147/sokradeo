<?php

namespace App\Repositories;

use App\Models\DesignatedVideo;
use Yish\Generators\Foundation\Repository\Repository;

class DesignatedVideoRepository extends Repository
{
    protected $model;

    /**
     * DesignatedVideoRepository constructor.
     * @param $model
     */
    public function __construct(DesignatedVideo $model)
    {
        $this->model = $model;
    }

    //
    public function getDesignatedInfo($groupId, $tbaId, $teamModelId)
    {
        $info = DesignatedVideo::query()->where(['group_id' => $groupId, 'tba_id' => $tbaId, 'team_model_id' => $teamModelId])->first();
        
        return $info;
    }
}
