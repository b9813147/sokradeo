<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Config;
use App\Models\Module;

class ModuleRepository
{
    //
    public function __construct()
    {
        
    }
    
    //
    public function list()
    {
        return Config::get('modules', []);
    }
}
