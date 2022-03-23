<?php

namespace App\Http\Controllers\Api\Test;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;

class MainController extends Controller
{
    //
    public function index()
    {
        
        return $this->success(['message' => 'index']);
        
    }
    
    //
    public function store(Request $req)
    {
        /*
        $this->validate($req, [
                'info' => 'required|file',
                'thum' => 'file',
        ]);
        
        $infoPath = null;
        $thumPath = null;
        $infoData = null;
        
        if ($req->hasFile('info') && $req->file('info')->isValid()) {
            $infoPath = $req->file('info')->store('tmp');
            $infoPath = storage_path('app/'.$infoPath);
            $infoData = json_decode(file_get_contents($infoPath), true);
        }
        
        if ($req->hasFile('thum') && $req->file('thum')->isValid()) {
            $thumPath = $req->file('thum')->store('tmp');
        }
        
        return $this->success([
                'info-path' => $infoPath,
                'thum-path' => $thumPath,
                'info-data' => $infoData,
        ]);
        */
        //--------------------------------------------
        return $this->success(['message' => 'store']);
    
    }
    
    //
    public function show()
    {
    
        return $this->success(['message' => 'show']);
    
    }
    
    //
    public function update()
    {
    
        return $this->success(['message' => 'update']);
    
    }
    
    //
    public function destroy()
    {
    
        return $this->success(['message' => 'destroy']);
    
    }
}
