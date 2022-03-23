<?php

namespace App\Repositories\Group\Channel;

use App\Models\GroupChannel;
use App\Types\Cms\CmsType;

class TbaVideoContentRepository extends ContentRepository
{
    private $contentType = CmsType::Tba;
    
    //
    public function __construct($channelId)
    {
        parent::__construct($channelId);
        $this->type = CmsType::TbaVideo;
    }
    
    //
    public function list($conds = [], $page = 1)
    {
        return GroupChannel::findOrFail($this->channelId)->tbas()->where($conds)->paginate(null, ['*'], 'page', $page);
    }
    
    //
    public function getContent($contentId)
    {
        return GroupChannel::findOrFail($this->channelId)->tbas()->findOrFail($contentId);
    }
    
    //
    public function setContent($contentId, $contentData)
    {
        // 無效不顯示   (0, 0)
        // 頻道內觀摩   (1, 0)
        // 全平台分享   (1, 1)
        // 尚待審核中   (2, 0)
        switch ($contentData['content_status']) {
            case 1:
                $contentData['content_status'] = 0;
                $contentData['content_public'] = 0;
                break;
            case 2:
                $contentData['content_status'] = 1;
                $contentData['content_public'] = 0;
                break;
            case 3:
                $contentData['content_status'] = 1;
                $contentData['content_public'] = 1;
                break;
            case 4:
                $contentData['content_status'] = 2;
                $contentData['content_public'] = 0;
                break;
        }
        GroupChannel::findOrFail($this->channelId)->tbas()->updateExistingPivot($contentId, $contentData);
    }
}
