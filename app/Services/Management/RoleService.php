<?php

namespace App\Services\Management;

use App\Repositories\RoleRepository;

class RoleService
{
    private $roleRepo = null;
    
    //
    public function __construct(RoleRepository $roleRepo)
    {
        $this->roleRepo = $roleRepo;
    }
    
    //
    public function list($page)
    {
        return $this->roleRepo->list($page);
    }
    
    //
    public function getRoles()
    {
        return $this->roleRepo->getRoles();
    }
    
    //
    public function getRole($roleId)
    {
        return $this->roleRepo->getRole($roleId);
    }
    
    //
    public function createRole($role)
    {
        return $this->roleRepo->createRole($role);
    }
}
