<?php

namespace App\Repositories\Group\Channel;

use App\Models\GroupChannel;
use App\Types\Cms\CmsType;

class VideoContentRepository extends ContentRepository
{
    //
    public function __construct($channelId)
    {
        parent::__construct($channelId);
        $this->type = CmsType::Video;
    }
    
    //
    public function list($conds = [], $page = 1)
    {
        return GroupChannel::findOrFail($this->channelId)->videos()->where($conds)->paginate(null, ['*'], 'page', $page);
    }
    
    //
    public function getContent($contentId)
    {
        return GroupChannel::findOrFail($this->channelId)->videos()->findOrFail($contentId);
    }
    
    //
    public function setContent($contentId, $contentData)
    {
        GroupChannel::findOrFail($this->channelId)->videos()->updateExistingPivot($contentId, $contentData);
    }
}
