<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChangeLogs;
use App\Models\Permission;
use App\Models\RolesAndPermissions;
use App\Models\UserAndRoles;


class LogsController extends Controller
{
	public function createLogs($table_name, $row_id, $value_before, $value_after, $user_id) {
		ChangeLogs::create([
			'table_name'=>$table_name,
	        'row_id'=>$row_id,
	        'value_before'=>$value_before,
	        'value_after'=>$value_after,
	        'created_by' => $user_id,
    	]);
	}

    public function getUserLogs(Request $request) {
    	$id = $request->id;
    	$Logs = ChangeLogs::where('created_by', $id)->get();
    	return $Logs;
    }


    public function getRoleLogs(Request $request) {
    	$id = $request->id;
    	$LogsRole = ChangeLogs::where('table_name','Role')->where('row_id',$id)->get();

    	$temp1 = ChangeLogs::where('table_name','UserAndRoles')->get();
    	$LogsUsersAndRoles = [];
    	foreach ($temp1 as $log) {
    		$t = UsersAndRoles::where('id',$log->row_id)->first();
    		if($t->role_id == $id) {
    			array_push($LogsUsersAndRoles, $log);
    		}
    	}
    	$temp2 = ChangeLogs::where('table_name','RolesAndPermission')->get();
    	$LogsRolesAndPermissions = [];
    	foreach ($temp2 as $log) {
    		$t = RolesAndPermissions::where('id',$log->row_id)->first();
    		if($t->role_id == $id) {
    			array_push($LogsRolesAndPermissions, $log);
    		}
    	}

    	$Logs = $LogsRole->concat($LogsUsersAndRoles)->concat($LogsRolesAndPermissions);
    	return $Logs;
    }

    public function getPermissionLogs(Request $request) {
    	$id = $request->id;
    	$LogsRole = ChangeLogs::where('table_name','Permission')->where('row_id',$id)->get();

    	$temp = ChangeLogs::where('table_name','RolesAndPermission')->get();
    	$LogsRolesAndPermissions = [];
    	foreach ($temp as $log) {
    		$t = RolesAndPermissions::where('id',$log->row_id)->first();
    		if($t->permission_id == $id) {
    			array_push($LogsRolesAndPermissions, $log);
    		}
    	}

    	$Logs = $LogsRole->concat($LogsRolesAndPermissions);
    	return $Logs;
    }
}
