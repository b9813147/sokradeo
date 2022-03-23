<?php

namespace App\Types\App;

abstract class DefaultChannelType
{
    // Set on .env
    const STATION_CODE_ORG = 'org';
    const STATION_CODE_CN = 'cn';

    // Please refer to 'groups' table
    const DEFAULT_CHANNEL_GROUP_ABBRV_ORG_EN = 'tbslen'; // ORG: en
    const DEFAULT_CHANNEL_GROUP_ABBRV_ORG_GB = 'tbslgb'; // ORG: tw, cn
    const DEFAULT_CHANNEL_GROUP_ABBRV_CN = 'tbslcn'; // CN: en, tw, cn

    /**
     * Get GroupSchoolAbbrv by Station and Language
     * @param string $stationCode
     * @param string $languageCode
     * @return string
     */
    public static function getGroupSchoolAbbrv(string $stationCode, string $languageCode): string
    {
        $stationCode = strtolower($stationCode);
        $languageCode = strtolower($languageCode);

        $groupSchoolAbbrv = '';
        switch ($stationCode) {
            case self::STATION_CODE_ORG:
                // If 'org', determine by language
                switch ($languageCode) {
                    case 'tw':
                    case 'cn':
                        $groupSchoolAbbrv = self::DEFAULT_CHANNEL_GROUP_ABBRV_ORG_GB;
                        break;
                    default:
                        $groupSchoolAbbrv = self::DEFAULT_CHANNEL_GROUP_ABBRV_ORG_EN;
                        break;
                }
                break;
            case self::STATION_CODE_CN:
                $groupSchoolAbbrv = self::DEFAULT_CHANNEL_GROUP_ABBRV_CN;
                break;
        }

        return $groupSchoolAbbrv;
    }
}
