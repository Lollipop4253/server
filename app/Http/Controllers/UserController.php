<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DTO\UserCollectionDTO;
use App\DTO\UserAndRoleCollectionDTO;
use App\Models\UsersAndRoles;
use App\Models\Role;
use App\DTO\RoleCollectionDTO;
use App\Http\Requests\ChangeUserAndRoleRequest;
use App\Http\Requests\CreateUserAndRoleRequest;
use App\Http\Requests\UserRequest;
use App\Http\Controllers\LogsConroller;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function getUsers(Request $request) {
    	$users = new UserCollectionDTO();
    	return response()->json($users->users);
    }

    public function getUserRoles(UserRequest $request) {

    	$user_id = $request->id;

    	$roles_id = UsersAndRoles::select('role_id')->where('user_id', $user_id)->get();

    	$roles = $roles_id->map(function($id) {
    		return Role::where('id', $id->role_id)->first();
    	});

    	return response()->json($roles);
    }

    public function giveUserRoles(CreateUserAndRoleRequest $request) {

    	$user_id = $request->id;

    	$role_id = $request->input('role_id');

    	$count = UsersAndRoles::where('user_id', $user_id)->where('role_id', $role_id)->count();

    	if ($count) {
    		return response()->json(['status' => '418']);
    	}

        DB::beginTransaction();

        try {
            $usersAndRoles = UsersAndRoles::create([
                'user_id' => $user_id,
                'role_id' => $role_id,
                'created_by' => $request->user()->id,
            ]);

            $Log = new LogsController();
            $Log->createLogs('UsersAndRoles', $usersAndRoles->id,'null',$usersAndRoles->role_id, $request->user()->id);

            DB::commit();

            return response()->json(['status' => '200']);
        }
        catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }

		
    	
    }

    public function hardDeleteRole(ChangeUserAndRoleRequest $request) {
    	$user_id = $request->id;
    	$role_id = $request->role_id;

        DB::beginTransaction();

        try {
            $userAndRoles = UsersAndRoles::withTrashed()->where('user_id', $user_id)->where('role_id', $role_id);

            $forLog = $userAndRoles->first();
            $Log = new LogsController();
            $Log->createLogs('UsersAndRoles', $forLog->id, $forLog->role_id,'null', $request->user()->id);

            $userAndRoles->forcedelete();

            DB::commit();

            return response()->json(['status' => '200']);
        }
        catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }
    	
    }

    public function softDeleteRole(ChangeUserAndRoleRequest $request){

    	$user_id = $request->id;
    	$role_id = $request->role_id;

        DB::beginTransaction();

        try {
            $userAndRoles = UsersAndRoles::where('user_id', $user_id)->where('role_id', $role_id)->first();

            $Log = new LogsController();
            $Log->createLogs('UsersAndRoles', $userAndRoles->id, $userAndRoles->role_id,'null',$request->user()->id);

            $userAndRoles->deleted_by = $request->user()->id;
            $userAndRoles->delete();
            $userAndRoles->save();

            DB::commit();

            return response()->json(['status' => '200']);
        }
        catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }

    	
    }

    public function restoreDeletedRole(ChangeUserAndRoleRequest $request) {
    	$user_id = $request->id;
    	$role_id = $request->role_id;

        DB::beginTransaction();

        try {
            $userAndRoles = UsersAndRoles::withTrashed()->where('user_id', $user_id)->where('role_id', $role_id)->first();

            $userAndRoles->restore();

            $Log = new LogsController();
            $Log->createLogs('UsersAndRoles', $userAndRoles->id, 'null', $userAndRoles->role_id,$request->user()->id);

            $userAndRoles->deleted_by = null;
            $userAndRoles->save();

            DB::commit();
            
            return response()->json(['status' => '200']);
        }
        catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }
    	
    }
}
