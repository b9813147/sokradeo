<?php

namespace App\Services\Group;

use LogicException;
use App\Factories\Group\Channel\ContentRepositoryFactory;
use App\Repositories\GroupRepository;

class ChannelService
{
    private $contentRepoFty = null;
    private $groupRepo      = null;
    
    /*
     * channel
     * */
    
    //
    public function __construct(ContentRepositoryFactory $contentRepoFty, GroupRepository $groupRepo)
    {
        $this->contentRepoFty = $contentRepoFty;
        $this->groupRepo      = $groupRepo;
    }
    
    //
    public function list($groupId, $conds, $page)
    {
        return $this->groupRepo->channels($groupId, $conds, $page);
    }
    
    //
    public function getChannels($groupId)
    {
        return $this->groupRepo->getChannels($groupId);
    }
    
    //
    public function getChannel($groupId, $channelId)
    {
        return $this->groupRepo->getChannel($groupId, $channelId);
    }
    
    //
    public function setChannel($groupId, $channelId, $channelData)
    {
        return $this->groupRepo->setChannel($groupId, $channelId, $channelData);
    }
    
    //
    public function createChannel($groupId, $channel)
    {
        if ($this->groupRepo->totalChannel($groupId) > 0) // 邏輯:限制只能擁有一個頻道
        {
            throw new LogicException('限制:群組只能擁有一個頻道');
        }
        return $this->groupRepo->createChannel($groupId, $channel);
    }
    
    /*
     * content
     * */
    
    //
    public function contents($groupId, $channelId, $conds, $page)
    {
        $contentRepo = $this->contentRepoFty->create($groupId, $channelId);
        $paginator = $contentRepo->list($conds, $page);
        $paginator->getCollection()->transform(function ($v) {
            return $this->toContent($v);
        });
        return $paginator;
    }
    
    //
    public function getContent($groupId, $channelId, $contentId)
    {
        $contentRepo = $this->contentRepoFty->create($groupId, $channelId);
        $src = $contentRepo->getContent($contentId);
        return $this->toContent($src);
    }
    
    //
    public function setContent($groupId, $channelId, $contentId, $contentData)
    {
        $contentRepo = $this->contentRepoFty->create($groupId, $channelId);
        return $contentRepo->setContent($contentId, $contentData);
    }
    
    //
    private function toContent(& $src)
    {
        // 無效不顯示  invalid (0, 0) 1
        // 頻道內觀摩  valid (1, 0)  2
        // 全平台分享  share (1, 1) 3
        // 尚待審核中  pending (2, 0) 4
        $src->content_status = is_null($src->pivot) ? null : $src->pivot->content_status;
        $src->content_public = is_null($src->pivot) ? null : $src->pivot->content_public;

        if ($src->content_status === 0 && $src->content_public === 0) {
            $src->status = '無效不顯示';
        } elseif ($src->content_status === 1 && $src->content_public === 0) {
            $src->status = '頻道內觀摩';
        } elseif ($src->content_status === 1 && $src->content_public === 1) {
            $src->status = '全平台分享';
        } else {
            $src->status = '尚待審核中';
        }
        unset($src->pivot);
        return $src;
    }
}
