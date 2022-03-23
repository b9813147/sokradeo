<?php

namespace App\Repositories;

use InvalidArgumentException;
use App\Models\Resource;
use App\Models\User;
use App\Types\Src\SrcType;

class ResourceRepository extends BaseRepository
{
    protected $model;

    //
    public function __construct(Resource $model)
    {
        $this->model = $model;
    }

    //
    public function list($page = 1)
    {
        return Resource::paginate(null, ['*'], 'page', $page);
    }

    //
    public function listByUserId($userId, $page = 1)
    {
        return User::findOrFail($userId)->resources()->paginate(null, ['*'], 'page', $page);
    }

    //
    public function getResrc($resrcId)
    {
        return Resource::findOrFail($resrcId);
    }

    //
    public function setResrc($resrcId, $resrcData)
    {
        $resrc = Resource::findOrFail($resrcId);
        $resrc->fill($resrcData);
        $resrc->save();
    }

    //
    public function createResrc($userId, $resrc)
    {
        if (!SrcType::check($resrc['src_type'])) {
            throw new InvalidArgumentException('src type is illegal');
        }

        $resrc['user_id'] = $userId;
        return Resource::query()->create($resrc);
    }

}
