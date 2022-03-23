<?php

namespace App\Factories\Src;

use App\Factories\Src\VodServiceFactory;
use App\Repositories\ConfigRepository;
use App\Repositories\FileRepository;
use App\Repositories\UriRepository;
use App\Repositories\VodRepository;
use App\Services\Src\FileService;
use App\Services\Src\UriService;
use App\Types\Src\SrcType;

class SrcServiceFactory
{
    private $configRepo = null;

    //
    public function __construct(ConfigRepository $configRepo)
    {
        $this->configRepo = $configRepo;
    }

    //
    public function create($type, $params = [])
    {
        $srv = null;
        switch ($type) {
            case SrcType::File:
                $srv = app(FileService::class);
                break;
            case SrcType::Uri:
                $srv = new UriService(new UriRepository());
                break;
            case SrcType::Vod:
                if (isset($params['vodType'])) {
                    $fty = new VodServiceFactory($this->configRepo, new VodRepository());
                    $srv = $fty->createByVodType($params['vodType']);
                } else {
                    $fty = new VodServiceFactory($this->configRepo, new VodRepository());
                    $srv = $fty->create();
                }
                break;
            default:
                assert(false);
        }
        return $srv;
    }

}
