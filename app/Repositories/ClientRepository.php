<?php

namespace App\Repositories;

use App\Models\Client;

class ClientRepository
{
    //
    public function __construct()
    {
        
    }
    
    //
    public function list($page = 1)
    {
        return Client::paginate(null, ['*'], 'page', $page);
    }
    
    //
    public function getClient($clientId)
    {
        return Client::findOrFail($clientId);
    }
    
    //
    public function setClient($clientId, $clientData)
    {
        $client = Client::findOrFail($clientId);
        $client->fill($clientData);
        $client->save();
    }
}
