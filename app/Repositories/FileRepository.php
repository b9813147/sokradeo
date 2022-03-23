<?php

namespace App\Repositories;

use LogicException;
use App\Models\File;

class FileRepository extends BaseRepository
{
    protected $model;

    //
    public function __construct(File $model)
    {
        $this->model = $model;
    }

    //
    public function list($page = 1)
    {
        return File::paginate(null, ['*'], 'page', $page);
    }

    //
    public function listByUserId($userId, $page = 1)
    {
        // 待實作
    }

    //
    public function getFile($fileId)
    {
        return File::findOrFail($fileId);
    }

    //
    public function createFile($resrcId, $file)
    {
        /*
        if (File::where('resource_id', $resrcId)->exists()) {
            throw new LogicException('resrc of file is already exist');
        }
        */
        $file['resource_id'] = $resrcId;
        return File::create($file);
    }

}
