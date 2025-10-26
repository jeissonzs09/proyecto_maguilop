<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionTimeout
{
    // Tiempo máximo de sesión en segundos (15 minutos)
    protected $timeout = 900;

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $lastActivity = session('lastActivityTime');

            if ($lastActivity && (time() - $lastActivity) > $this->timeout) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect('/login')->withErrors(['message' => 'La sesión ha expirado por seguridad.']);
            }

            session(['lastActivityTime' => time()]);
        }

        return $next($request);
    }
}