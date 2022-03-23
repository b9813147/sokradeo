<?php

namespace App\Repositories\Tba;

use App\Models\TbaFeature;

class FeatureRepository
{
    //
    public function __construct()
    {
        
    }
    
    //
    public function getFeatures()
    {
        return TbaFeature::all();
    }
    
}
