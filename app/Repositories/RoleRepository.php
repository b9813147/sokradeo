<?php

namespace App\Repositories;

use LogicException;
use Illuminate\Support\Facades\Config;
use App\Models\Role;

class RoleRepository
{
    //
    public function __construct()
    {
        
    }
    
    //
    public function list($page = 1)
    {
        return Role::paginate(null, ['*'], 'page', $page);
    }
    
    //
    public function getRoles() // 待改成條件篩選
    {
        return Role::all();
    }
    
    //
    public function getRole($roleId)
    {
        $role = Role::findOrFail($roleId);
        $role['modules'] = Config::get('module_role.'.$role->type, []);
        return $role;
    }
    
    //
    public function createRole($role)
    {
        if (Role::where('type', $role['type'])->exists()) {
            throw new LogicException('role type is already exist');
        }
        
        return Role::create($role);
    }
}
