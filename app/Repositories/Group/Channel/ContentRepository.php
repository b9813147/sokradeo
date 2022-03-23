<?php

namespace App\Repositories\Group\Channel;

abstract class ContentRepository
{
    protected $channelId = null;
    protected $type      = null;
    
    protected function __construct($channelId)
    {
        $this->channelId = $channelId;
    }
    
    abstract public function list($conds = [], $page = 1);
    abstract public function getContent($contentId);
    abstract public function setContent($contentId, $contentData);
}
