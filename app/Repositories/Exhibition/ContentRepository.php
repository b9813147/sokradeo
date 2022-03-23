<?php

namespace App\Repositories\Exhibition;

use Exception;
use App\Models\GroupChannelContent;

abstract class ContentRepository
{
    protected $type = null;
    
    //
    public function __construct($type)
    {
        $this->type = $type;
    }
    
    abstract public function listByContentIds($contentIds, $page = 1, $perPage = null);
    abstract public function getContentsByIds($contentIds);
    
    //
    public function getPageContentIds($page, $perPage, $orders = [], $conds = [], $search = '', $tbaFeatures = [])
    {
        // 待實作(通用函式但不適用tbavideo 故tbavideo須重載)
        throw new Exception('please implement:通用函式但不適用tbavideo, 故tbavideo須重載');
    }
    
    //
    public function getRankContentIds($limit = 1, $orders = [])
    {
        // 待實作(通用函式但不適用tbavideo 故tbavideo須重載)
        throw new Exception('please implement:通用函式但不適用tbavideo, 故tbavideo須重載');
    }
}
