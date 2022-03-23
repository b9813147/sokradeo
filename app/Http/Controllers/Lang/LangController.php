<?php

namespace App\Http\Controllers\Lang;

use App\Libraries\Lang\Lang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;

class LangController extends Controller
{
    private $locales = ['en', 'tw', 'cn'];

    private $browserLang;

    //
    public function __construct(Lang $lang)
    {
        $this->module      = ['cate' => 'Lang', 'app' => 'Lang'];
        $this->browserLang = $lang;
    }

    //
    public function index(Request $req)
    {
        $locale = $req->locale;
        $locale = in_array($locale, $this->locales) ? $locale : $this->browserLang;
        App::setLocale($locale);
        Redis::set('local', Lang::getConvertByLang($locale));
        Session::put('locale', $locale);

        // Check callBack URL
        $callBack = $req->callBack;
        if (filter_var($callBack, FILTER_VALIDATE_URL) !== false) {
            return Redirect::to($callBack);
        }

        return Redirect::back();
    }

}
