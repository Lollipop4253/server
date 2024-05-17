<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DTO\RoleCollectionDTO;
use App\Models\Role;

class RoleController extends Controller
{
    public function getRoles(Request $request) {
    	return RoleCollectionDTO(Role::all());
    }
}
