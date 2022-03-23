<?php

namespace App\Services\Management;

use App\Repositories\UserRepository;

class UserService
{
    private $userRepo = null;
    
    //
    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }
    
    //
    public function list($page)
    {
        return $this->userRepo->list($page);
    }
    
    //
    public function getUser($userId)
    {
        return $this->userRepo->getUser($userId);
    }
    
    //
    public function setUser($userId, $userData, $roles)
    {
        return $this->userRepo->setUser($userId, $userData, $roles);
    }
    
    //
    public function searchUsers($name)
    {
        return $this->userRepo->searchUsers($name);
    }
}
