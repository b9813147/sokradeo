<?php


namespace App\Types\Lang;


abstract class ConnectionType
{
    const   MYSQL_EN = 'mysql_en';
    const   MYSQL_CN = 'mysql_cn';
    const   MYSQL_TW = 'mysql';

    public static function check($lang)
    {
        switch ($lang) {
            case 'en':
                return self::MYSQL_EN;
                break;
            case 'cn':
                return self::MYSQL_CN;
                break;
            case 'tw':
                return self::MYSQL_TW;
                break;
        }
    }
}

