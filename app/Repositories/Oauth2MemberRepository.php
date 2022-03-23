<?php

namespace App\Repositories;

use App\Models\Oauth2Member;
use Yish\Generators\Foundation\Repository\Repository;

class Oauth2MemberRepository extends Repository
{
    protected $model;

    /**
     * Oauth2MemberRepository constructor.
     * @param $model
     */
    public function __construct(Oauth2Member $model)
    {
        $this->model = $model;
    }


}
