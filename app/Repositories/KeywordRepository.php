<?php

namespace App\Repositories;

use App\Models\Keyword;

class KeywordRepository
{
    //
    public function __construct()
    {
        
    }
    
    //
    public function getKeywords($name, $searchInserted = false)
    {
        $keywords = Keyword::where('name', 'like', '%'.$name.'%')->get();
        if ($searchInserted) {
            $search = new Keyword(['name' => $name]);
            $keywords->prepend($search);
        }
        return $keywords;
    }
    
    //
    public function createKeyword($name)
    {
        $name = strtolower(trim($name));
        
        $now = date('Y-m-d H:i:s');
        
        $conds = ['name' => $name];
        
        if (Keyword::where($conds)->exists()) {
            Keyword::where($conds)->update(['updated_at' => $now]);
        } else {
            Keyword::create($conds);
        }
    }
}
