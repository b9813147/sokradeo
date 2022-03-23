<?php

namespace App\Helpers\Enum;

trait Constant
{
    /**
     * @return mixed
     */
    public static function getConstant($v)
    {
        return constant('self::'.$v);
    }
    
}