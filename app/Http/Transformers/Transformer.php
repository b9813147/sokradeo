<?php

namespace App\Http\Transformers;

use Exception;

class Transformer
{
    protected $req;
    
    public function __construct($req) {
        
        $this->req = $req;
    }
    
    public function execute() {
        
        throw new Exception('please implement');
    }
    
    protected function map($maps) {
        
        $merges = [];
        
        foreach ($maps as $tar => $src) {
        
            if (! $this->req->exists($src)) {
                continue;
            }
        
            $merges[$tar] = $this->req[$src];
            $merges[$src] = null;
        }
        
        $this->req->merge($merges);
    }
    
}
