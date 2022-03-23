<?php

namespace App\Services;

use App\Repositories\Oauth2MemberRepository;
use Yish\Generators\Foundation\Service\Service;

class Oauth2MemberService extends Service
{
    protected $repository;

    /**
     * Oauth2MemberService constructor.
     * @param $repository
     */
    public function __construct(Oauth2MemberRepository $repository)
    {
        $this->repository = $repository;
    }


}
