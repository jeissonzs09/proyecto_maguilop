<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Ensure2FAIsVerified
{
    public function handle(Request $request, Closure $next)
    {
        if (
            Auth::check() &&
            Auth::user()->two_factor_secret &&
            !session('2fa_passed')
        ) {
            return redirect()->route('2fa.verify.form');
        }

        return $next($request);
    }
}
