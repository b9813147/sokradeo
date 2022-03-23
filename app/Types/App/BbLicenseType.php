<?php

namespace App\Types\App;

abstract class BbLicenseType
{
    const CHANNEL_SPACE_ID = 1;
    const OBSRV_CLASS_LIMIT_ID = 2;
    const OBSRV_CONNECTION_LIMIT_ID = 3;
    const SOK_CHANNEL_MGMT_ID = 4;

    public static function check(int $id)
    {
        switch ($id) {
            case BbLicenseType::CHANNEL_SPACE_ID:
            case BbLicenseType::OBSRV_CLASS_LIMIT_ID:
            case BbLicenseType::OBSRV_CONNECTION_LIMIT_ID:
            case BbLicenseType::SOK_CHANNEL_MGMT_ID:
                return true;
            default:
                return false;
        }
    }
}
