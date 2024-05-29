<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DTO\RoleCollectionDTO;
use App\Models\Role;
use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\ChangeRoleRequest;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function getRoles(Request $request) {
    	$roles = new RoleCollectionDTO(Role::all());
    	return response()->json($roles->roles);
    }

    public function getTargetRole(Request $request) {
    	return response()->json(Role::where('id', $request->id)->first());
    }

    public function createRole(CreateRoleRequest $request) {

    	$user = $request->user();

        DB::beginTransaction();

        try {
            $new_role = Role::create([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'code' => $request->input('code'),
                'created_by' => $user->id,
            ]);

            $Log = new LogsController();
            $Log->createLogs('Roles', $new_role->id, 'null', $new_role->name, $user->id);

            DB::commit();

            return response()->json($new_role);
        }
        catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }

    	
    }

    public function updateRole(ChangeRoleRequest $request) {

    	$user = $request->user();

        DB::beginTransaction();

        try {
            // (new DB())->transaction(function () {

            // })
            $role = Role::where('id', $request->id)->first();

            // $oldValue = json_encode($role);

            $Log = new LogsController();

            if ($role->name != $request->input('name')) {
                $Log->createLogs('Roles', $role->id, $role->name, $request->input('name'), $user->id);
            }
            if ($role->description != $request->input('description')) {
                $Log->createLogs('Roles', $role->id, $role->description, $request->input('description'), $user->id);
            }

            if ($role->code != $request->input('code')) {
                $Log->createLogs('Roles', $role->id, $role->code, $request->input('code'), $user->id);
            }
            

            $role->update([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'code' => $request->input('code'),
            ]);

            // $newValue = Role::find($request_id);    

            
            DB::commit();

            return response()->json($role);
        }
        catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }

    	
    }

    public function hardDeleteRole(ChangeRoleRequest $request) {

    	$role_id = $request->id;

        DB::beginTransaction();

        try {
            $role = Role::withTrashed()->find($role_id);

            $forLog = $role->first();
            $Log = new LogsController();
            $Log->createLogs('Roles', $forLog->id, $forLog->name, 'null', $request->user()->id);

            $role->forcedelete();

            DB::commit();

            return response()->json(['status' => '200']);
        }
        catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }
    	
    }

    public function softDeleteRole(ChangeRoleRequest $request) {

    	$role_id = $request->id;
    	$user = $request->user();

        DB::beginTransaction();

        try {
            $role = Role::where('id', $role_id)->first();

            $Log = new LogsController();
            $Log->createLogs('Roles', $role->id, $role->name, 'null', $user->id);

            $role->deleted_by = $user->id;
            $role->delete();
            $role->save();

            DB::commit();

            return response()->json(['status' => '200']);
        }
        catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }

    public function restoreDeletedRole(ChangeRoleRequest $request) {

    	$role_id = $request->id;

        DB::beginTransaction();

        try {
            $role = Role::withTrashed()->find($role_id);

            $forLog = $permission->first();
            $Log = new LogsController();
            $Log->createLogs('Roles', $forLog->id, 'null', $forLog->name, $request->user()->id);

            $role->restore();
            $role->deleted_by = null;
            $role->save();

            DB::commit();
            
            return response()->json(['status' => '200']);
        }
        catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }

    	
    }
}
