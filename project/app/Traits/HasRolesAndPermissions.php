<?php

namespace App\Traits;

use App\Models\Role;
use App\Models\Permission;
trait HasRolesAndPermissions
{

    /**
     * @return mixed
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class,'admins_roles');
    }

    /**
     * @return mixed
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class,'admins_permissions');
    }

    public function hasRole($role)
    {        
        if( strpos($role, ',') !== false ){//check if this is an list of roles

            $listOfRoles = explode(',',$role);

            foreach ($listOfRoles as $role) {                    
                if ($this->roles->contains('slug', $role)) {
                    return true;
                }
            }
        }else{                
            if ($this->roles->contains('slug', $role)) {
                return true;
            }
        }

        return false;
    }

    public function hasPermission($permission)
    {        
        if( strpos($permission, ',') !== false ){//check if this is an list of roles

            $listOfpermission = explode(',',$permission);

            foreach ($listOfpermission as $permission) {                    
                if ($this->permissions->contains('slug', $permission)) {
                    return true;
                }
            }
        }else{                
            if ($this->permissions->contains('slug', $permission)) {
                return true;
            }
        }

        return false;
    }

}