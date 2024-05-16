<?php

namespace App\DTO;

use Illuminate\Support\Collection;
use App\DTO\PermissionDTO;

class PermissionCollectionDTO
{
    public $permissions;

    public function __construct(Collection $permissions)
    {
        $this->permissions = $permissions->map(function ($permission) {
            return new PermissionDTO(
                $permission->name,
                $permission->description,
                $permission->code,
                $permission->created_by,
                $permission->deleted_by
            );
        });
    }
}
