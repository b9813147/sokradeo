<?php

namespace App\Helpers\String;

trait Template
{
    /**
     * @return string
     */
    public static function getTmplStr($tmpl, $maps)
    {
        foreach ($maps as $k => $v) {
            
            $tmpl = str_replace('{{'.$k.'}}', $v, $tmpl);
        }
        
        return $tmpl;
    }
    
}