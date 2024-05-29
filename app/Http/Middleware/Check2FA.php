<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Check2FA
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user->two_factor_enabled && !$request->session()->get('2fa_verified')) {
            return redirect()->route('otp.send');
        }

        return $next($request);
    }
}
