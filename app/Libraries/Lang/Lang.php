<?php

/**
 * Created by PhpStorm.
 * User: ares
 * Date: 2019-07-23
 * Time: 11:32
 */

namespace App\Libraries\Lang;


use App\Models\Locale;

class Lang
{
    protected $browserLang;

    /**
     * 讀取瀏覽億語系
     * Lang constructor.
     */
    public function __construct()
    {
        $browser_lang      = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'zh-TW';
        $this->browserLang = strtolower(strtok(strip_tags($browser_lang), ','));
    }

    public function getBrowserLang()
    {
        if (!session()->get('locale')) {
            switch ($this->browserLang) {
                case 'en-us':
                    $this->browserLang = 'en';
                    break;
                case 'zh-tw':
                    $this->browserLang = 'tw';
                    break;
                default:
                    $this->browserLang = 'cn';
                    break;
            };
            app()->setLocale($this->browserLang);
            session()->put('locale', $this->browserLang);

            return $this->browserLang;
        } else {
            return app()->getLocale();
        }
    }

    /**
     * Get meta keywords by lang string
     * @param string $lang
     * @return string
     */
    public function getMetaKeywordsByLang($lang): string
    {
        $keywords = '';
        switch ($lang) {
            case 'tw':
                $keywords = '教師專業發展,公開授課,智慧教育,教育大數據,醍摩豆';
            case 'cn':
                $keywords = '教师专业发展,公开授课,智慧教育,教育大数据,醍摩豆';
                break;
            case 'en':
            default:
                $keywords = 'Teacher Professional Development,lesson study,open classroom observation,smart education,TEAM Model';
                break;
        }
        return $keywords;
    }

    /**
     * Get meta description by lang string
     * @param string $lang
     * @return string
     */
    public function getMetaDescByLang($lang): string
    {
        $desc = '';
        switch ($lang) {
            case 'tw':
                $desc = '蘇格拉底平台是由全球醍摩豆智慧教育研究院 (Global TEAM Model Education Research Institute，簡稱GTERI) 所提供，匯流來自智慧教室錄製的蘇格拉底課例，便於研究醍摩豆智慧教育，以建立全球性的智慧教育研究平台與教學大數據研究中心';
                break;
            case 'cn':
                $desc = '苏格拉底平台是由全球醍摩豆智慧教育研究院 (Global TEAM Model Education Research Institute，简称GTERI) 所提供，汇流来自智慧教室录制的苏格拉底课例，便于研究醍摩豆智慧教育，以建立全球性的智慧教育研究平台与教学大数据研究中心';
                break;
            case 'en':
            default:
                $desc = 'The Sokrates platform, by the Global TEAM Model Education Research Institute (GTERI), collects lessons from smart classrooms to facilitate the study of TEAM Model Education Research Institute (GTERI). Global smart education research platform and teaching big data research center';
                break;
        }
        return $desc;
    }

    /**
     * 將語系 字串轉換與DB相同
     *
     * @param $lang
     * @return string
     */
    public static function getConvertByLangString($lang)
    {
        switch ($lang) {
            case 'en':
                return Locale::query()->where('type', 'en-US')->pluck('id')->first();
            case 'tw':
                return Locale::query()->where('type', 'zh-TW')->pluck('id')->first();
            case 'cn':
                return Locale::query()->where('type', 'zh-CN')->pluck('id')->first();
        }
    }

    /**
     * 將語系 字串轉換與大寫
     * @param $lang
     * @return string
     */
    public static function getConvertByLang($lang)
    {
        switch ($lang) {
            case 'en':
                return 'en-US';
            case 'tw':
                return 'zh-TW';
            case 'cn':
                return 'zh-CN';
        }
    }
}
