<?php

namespace App\Types\ObservationClass;

abstract class ContentPublicType
{
    const PRIVATE = 'private';
    const PUBLIC = 'public';
    const GLOBAL = 'global';

    // Based on ???? (please ask Legend)
    const PRIVATE_VALUE = 4;
    const PUBLIC_VALUE = 2;
    const GLOBAL_VALUE = 3;

    public static function check($type)
    {
        switch ($type) {
            case ContentPublicType::PRIVATE:
                return true;
            case ContentPublicType::PUBLIC:
                return true;
            case ContentPublicType::GLOBAL:
                return true;
            default:
                return false;
        }
    }

    public static function list()
    {
        return [
            ['type' => ContentPublicType::PRIVATE, 'text' => 'private', 'value' => ContentPublicType::PRIVATE],
            ['type' => ContentPublicType::PUBLIC, 'text' => 'public', 'value' => ContentPublicType::PUBLIC],
            ['type' => ContentPublicType::GLOBAL, 'text' => 'global', 'value' => ContentPublicType::GLOBAL],
        ];
    }

    public static function getContentPublicValue($type)
    {
        switch ($type) {
            case ContentPublicType::PRIVATE:
                return ContentPublicType::PRIVATE_VALUE;
            case ContentPublicType::PUBLIC:
                return ContentPublicType::PUBLIC_VALUE;
            case ContentPublicType::GLOBAL:
                return ContentPublicType::GLOBAL_VALUE;
            default:
                return ContentPublicType::PRIVATE_VALUE;
        }
    }
}
