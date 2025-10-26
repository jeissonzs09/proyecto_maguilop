<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;
use App\Models\User;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Quitar espacios del email
        $request->merge([
            'email' => trim($request->email),
        ]);

        // Validar email
        $request->validate([
            'email' => ['required', 'email', 'string', 'max:255', 'regex:/^\S*$/u'],
        ]);

        // Buscar usuario
        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return back()->withErrors(['email' => 'No se encontró un usuario con ese correo.']);
        }

        // Enviar enlace de restablecimiento
        $status = Password::sendResetLink([
            'email' => $user->email,
        ]);

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }
}