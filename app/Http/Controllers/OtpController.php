<?php

namespace App\Http\Controllers;

use App\Models\OtpCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Models\User;

class OtpController extends Controller
{
    public function verifyOtp(Request $request)
    {
        $user = User::where('username', $request->input('username'))->first();
        $otpCode = OtpCode::where('user_id', $user->id)
                          ->where('code', $request->input('otp'))
                          ->where('expires_at', '>', Carbon::now())
                          ->first();

     	$curent_code = OtpCode::where('user_id', $user->id)->latest()->first();
     	
        if ($otpCode->code == $curent_code->code) {
            OtpCode::where('user_id', $user->id)->delete();

            $userTokenCount = $user->tokens()->count();

	        if ($userTokenCount >= env('MAX_ACTIVE_TOKENS', 3)) {
	            $oldestToken = $user->tokens()->oldest()->first();
	            $oldestToken->revoke();
	        }

	        $tokenResult = $user->createToken('Personal Access Token');
	        $token = $tokenResult->token;
	        $token->expires_at = Carbon::now()->addDays(env('TOKEN_EXPIRATION_DAYS', 15));
	        $token->save();

	        return response()->json([
	            'access_token' => $tokenResult->accessToken,
	            'token_type' => 'Bearer',
	            'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
	        ]);
        }
        else {
            return response()->json(['message' => 'Все не заебись']);
        }
    }
}
