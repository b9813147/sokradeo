<?php

namespace App\Repositories;

use App\Models\Locale;

class LocaleRepository
{
    //
    public function __construct()
    {
        
    }
    
    //
    public function getLocales($conds = [])
    {
        return Locale::where($conds)->get();
    }
    
    //
    public function getLocale($localeId)
    {
        return Locale::findOrFail($localeId);
    }
}
