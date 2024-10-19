<?php

namespace Webarq\Model;


use Wa;

class RoleModel extends AbstractListingModel
{
    protected $table = 'roles';

    public function permissions()
    {
        return $this->hasMany('Webarq\Model\PermissionModel', 'role_id')
                ->select('permissions.module', 'permissions.panel', 'permissions.permission');
    }
}