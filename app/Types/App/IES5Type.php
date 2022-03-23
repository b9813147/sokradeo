<?php

namespace App\Types\App;

abstract class IES5Type
{
    const TEACHER = 'teacher';
    const STUDENT = 'student';
    
    public static function check($type)
    {
        switch ($type) {
            case IES5Type::TEACHER:
            case IES5Type::STUDENT:
                return true;
            default:
                return false;
        }
    }
}
