<?php

namespace App\Factories\Group\Channel;

use Exception;
use App\Models\Group;
use App\Repositories\Group\Channel\TbaContentRepository;
use App\Repositories\Group\Channel\TbaVideoContentRepository;
use App\Repositories\Group\Channel\VideoContentRepository;
use App\Types\Cms\CmsType;

class ContentRepositoryFactory
{
    //
    public function create($groupId, $channelId)
    {
        $type = Group::findOrFail($groupId)->channels()->findOrFail($channelId)->cms_type;

        $repo = null;
        switch($type) {
            case CmsType::Video:
                $repo = new VideoContentRepository($channelId);
                break;
            case CmsType::Tba:
                $repo = new TbaContentRepository($channelId);
                break;
            case CmsType::TbaVideo:
                $repo = new TbaVideoContentRepository($channelId);
                break;
            default:
                assert(false);
        }
        return $repo;
    }
}
