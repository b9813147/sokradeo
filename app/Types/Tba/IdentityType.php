<?php

namespace App\Types\Tba;

use Illuminate\Support\Facades\Lang;
use App\Helpers\Enum\Item;

abstract class IdentityType
{
    use Item;
    
    //
    const Expert  = 'E';
    const Visitor = 'V';
    const Teacher = 'T';
    const User    = 'U';
    
    //
    public static function check($type)
    {
        switch ($type) {
            case IdentityType::Expert:
            case IdentityType::Visitor:
            case IdentityType::Teacher:
            case IdentityType::User:
                return true;
            default:
                return false;
        }
    }
    
    //
    public static function list()
    {
        $mapTbaIdentity = Lang::get('app/tba/identity');
        return [
            ['type' => 'Expert',  'value' => IdentityType::Expert,  'text' => $mapTbaIdentity['Expert' ], 'comment_public' => 1],
            ['type' => 'Visitor', 'value' => IdentityType::Visitor, 'text' => $mapTbaIdentity['Visitor'], 'comment_public' => 1],
            ['type' => 'Teacher', 'value' => IdentityType::Teacher, 'text' => $mapTbaIdentity['Teacher'], 'comment_public' => 1],
            ['type' => 'User',    'value' => IdentityType::User,    'text' => $mapTbaIdentity['User'   ], 'comment_public' => 0],
        ];
    }
    
}
