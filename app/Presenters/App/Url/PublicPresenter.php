<?php

namespace App\Presenters\App\Url;

use App\Libraries\Lang\Lang;

class PublicPresenter
{
    protected $browserLang;
    /**
     * PublicPresenter constructor.
     * @param Lang $lang
     */
    public function __construct(Lang $lang)
    {
        $this->browserLang = $lang;
    }

    //
    public function image($module, $path, $lang = false)
    {
        return url('images/' . $module . '/' . ($lang ? $this->getLang() . '/' : '') . $path);
    }

    //
    private function getLang()
    {
        return $this->browserLang->getBrowserLang();
    }
}
