<?php

namespace App\Http\Controllers;

use Illuminate\Http\Requests;
use App\Http\Requests\RegisterRequest;
use App\DTO\RegisterDTO;
use App\Models\User;
use App\Models\UsersAndRoles;
use Illuminate\Http\Response;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request) {

        $userData = $request->createDTO();

    	$user = User::create([
            'username' => $userData->username,
            'email' => $userData->email,
            'password' => bcrypt($userData->password),
            'birthday' => $userData->birthday,
        ]);

        $role = UsersAndRoles::create([
    		'user_id' => $user->id,
    		'role_id' => 3,
    		'created_by' => 1,
    	]);
        $Log = new LogsController();
        $Log->createLogs('User', $user->id,'null', $user->username, $user->id);
        $Log->createLogs('UsersAndRoles', $role->id, $role->role_id,'null', $user->id);
        return response()->json($user, Response::HTTP_CREATED);
    }
}
