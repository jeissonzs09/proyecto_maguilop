<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureEmailCodeIsVerified
{
    public function handle(Request $request, Closure $next)
    {
        if (
            Auth::check() &&
            !session()->has('2fa_passed')
        ) {
            return redirect()->route('2fa.code.form');
        }

        return $next($request);
    }
}

