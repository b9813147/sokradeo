<?php

namespace App\Services\App;

use App\Repositories\LocaleRepository;

class LocaleService
{
    private $localeRepo = null;
    
    //
    public function __construct(LocaleRepository $localeRepo)
    {
        $this->localeRepo = $localeRepo;
    }
    
    //
    public function getLocales()
    {
        return $this->localeRepo->getLocales(['active' => 1]);
    }
}
