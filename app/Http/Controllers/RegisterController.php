<?php

namespace App\Http\Controllers;

use Illuminate\Http\Requests;
use App\Http\Requests\RegisterRequest;
use App\DTO\RegisterDTO;
use App\Models\User;
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

        return response()->json($user, Response::HTTP_CREATED);
    }
}
