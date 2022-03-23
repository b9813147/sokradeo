<?php

namespace App\Services\Group;

use LogicException;
use App\Repositories\GroupRepository;
use App\Repositories\GroupChannelContentRepository;

class ContentService
{
    private $groupChannelContentRepo = null;
    
    //
    public function __construct(GroupChannelContentRepository $groupChannelContentRepo)
    {
        $this->groupChannelContentRepo = $groupChannelContentRepo;
    }
    
    //
    public function list($channelId, $conds, $page)
    {
        // 待實作
    }
    
    //
    public function getContents($channelId)
    {
        // 待實作
    }
    
    //
    public function getContent($channelId, $contentId, $contentType)
    {
        return $this->groupChannelContentRepo->getContent($channelId, $contentId, $contentType);
    }
    
    //
    public function setContent($channelId, $contentId, $contentType, $contentData)
    {
        // 待實作
    }
    
    //
    public function createContent($channelId, $content, $extra_info = null)
    {
        return $this->groupChannelContentRepo->createContent($channelId, $content, $extra_info);
    }
    
    //
    public function createContentWithSchoolCode($schoolCode, $content)
    {
        $group   = (new GroupRepository())->getGroupBySchoolCode($schoolCode);
        $channel = $group->channels()->first();
        if (is_null($channel)) {
            throw new LogicException('group has no channel');
        }
        
        return $this->groupChannelContentRepo->createContent($channel->id, $content);
    }
    
}
