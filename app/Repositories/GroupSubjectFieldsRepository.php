<?php

namespace App\Repositories;

use App\Helpers\Custom\GlobalPlatform;
use App\Models\GroupSubjectFields;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Yish\Generators\Foundation\Repository\Repository;

class GroupSubjectFieldsRepository extends BaseRepository
{
    protected $model;

    /**
     * GroupSubjectFieldsRepository constructor.
     * @param GroupSubjectFields $model
     */
    public function __construct(GroupSubjectFields $model)
    {
        $this->model = $model;
    }

    /**
     * @param array $attributes
     * @param array $values
     * @return GroupSubjectFields
     */
    public function updateOrCreate(array $attributes, array $values = [])
    {
        return $this->model->updateOrCreate($attributes, $values);
    }

    /**
     * 僅限頻道內部使用
     *
     * @param $channelId
     * @return GroupSubjectFields[]|Builder[]|Collection|
     */
    public function getChannelBySubject($channelId)
    {

        $convertChannelIdToGroupId = GlobalPlatform::convertChannelIdToGroupId($channelId);

        return $this->model->select('subject', 'id')->where('groups_id', $convertChannelIdToGroupId)->distinct('subject_fields_id')->get();
    }
}
