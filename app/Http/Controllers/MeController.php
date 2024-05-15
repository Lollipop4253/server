<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Http\Request;
use App\DTO\UserDTO;

class MeController extends Controller
{
    public function check(Request $request)
    {
    	$user = $request->user();

    	return response()->json(["user" => $user]);
}

    public function getTokens(Request $request) {
    	$user = $request->user();
	    $tokens = $user->tokens;

	    return response()->json(['tokens' => $tokens]);
    }
}


