<?php

namespace App\Http\Controllers\Api\V1\HaBook;

use App\Libraries\Lang\Lang;
use App\Services\Habook\ApiService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;

class HaBookController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * @return string
     */
    public function getTicket(): string
    {
       return \Crypt::encryptString(auth()->id());
    }
}
