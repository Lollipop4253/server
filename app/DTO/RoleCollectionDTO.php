<?php

namespace App\DTO;

use Illuminate\Support\Collection;
use App\DTO\RoleDTO;

class RoleCollectionDTO
{
    public $roles;

    public function __construct(Collection $roles)
    {
        $this->roles = $roles->map(function ($role) {
            return new RoleDTO(
                $role->name,
                $role->description,
                $role->code,
                $role->created_by,
                $role->deleted_by
            );
        });
    }
}
