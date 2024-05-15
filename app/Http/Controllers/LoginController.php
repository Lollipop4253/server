<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;
use Laravel\Passport\Token;
use Carbon\Carbon;

class LoginController extends Controller
{
    public function login(LoginRequest $request) {

    	$userdata = $request->createDTO();

        $user = User::where('username', $userdata->username)->first();

        if (!$user || !Hash::check($userdata->password, $user->password)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $userTokenCount = $user->tokens()->count();

        if ($userTokenCount >= env('MAX_ACTIVE_TOKENS')) {
            $oldestToken = $user->tokens()->oldest()->first();
            $oldestToken->revoke();
        }

        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        $token->expires_at = Carbon::now()->addDays(env('TOKEN_EXPIRATION_DAYS'));
        $token->save();

        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
        ]);
    }


    public function out(Request $request) {
        $user = Auth::user();
        $user->token()->revoke();
        return response()->json(["Token is logout"], 200);
    }

    public function outAll(Request $request) {
        $user = Auth::user();
    
        $user->tokens->each(function($token, $key) {
            $token->revoke();
        });
        return response()->json(["All tokens is logout"], 200);
    }
}

