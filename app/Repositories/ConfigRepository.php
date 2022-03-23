<?php

namespace App\Repositories;

use App\Models\ConfigParameter;

class ConfigRepository
{
    //
    public function __construct()
    {
        
    }
    
    //
    public function getParamsByCate($cate)
    {
        $params = ConfigParameter::where('cate', $cate)->get();
        
        $result = [];
        $params->each(function ($v) use (& $result) {
            $result[$v->attr] = $v;
        });
        return $result;
    }
    
    //
    public function setParamVal($cate, $attr, $val = null)
    {
        ConfigParameter::where('cate', $cate)->where('attr', $attr)->update(['val' => $val]);
    }
}
