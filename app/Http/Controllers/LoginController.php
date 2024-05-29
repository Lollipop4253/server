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
use App\Models\OtpCode;
use Illuminate\Support\Facades\Mail;
class LoginController extends Controller
{
    public function login(LoginRequest $request) {

    	$userdata = $request->createDTO();

        $user = User::where('username', $userdata->username)->first();

        if (!$user || !Hash::check($userdata->password, $user->password)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $otp_count = OtpCode::where('user_id', $user->id)->count();

        if ($otp_count >= env("MAX_OTP_CODES", 3)) {

            $now = Carbon::now();
            $time = OtpCode::where('user_id', $user->id)->oldest()->get()->last();
            if ($now->diffInSeconds($time->created_at) >= 30) {
                OtpCode::where('user_id', $user->id)->latest()->first()->delete();
            }
            else {
                return response()->json(['error' => 'Подожди немного']);
            }
        }

        $otp = rand(100000, 999999);

        OtpCode::create([
            'user_id' => $user->id,
            'code' => $otp,
            'expires_at' => Carbon::now()->addMinutes(3),
        ]);

        Mail::raw("Ваш одноразовый пароль: $otp", function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Ваш одноразовый пароль');
        });

        return response()->json(['message' => 'send code']);
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

