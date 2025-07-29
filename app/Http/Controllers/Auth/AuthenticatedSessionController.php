<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use App\Models\EmailVerificationCode;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Auth\LoginRequest;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate();
    $request->session()->regenerate();

    $user = Auth::user();

    // Código 2FA
    $code = random_int(100000, 999999);

    EmailVerificationCode::where('user_id', $user->UsuarioID)->delete();
    EmailVerificationCode::create([
        'user_id' => $user->UsuarioID,
        'code' => $code,
        'expires_at' => now()->addMinutes(5),
    ]);

    Mail::raw("Tu código de verificación es: $code", function ($message) use ($user) {
        $message->to($user->CorreoElectronico)
                ->subject('Código de verificación - Maguilop');
    });

    return redirect()->route('2fa.code.form');
}


    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}

