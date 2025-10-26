<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request)
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validación del formulario
        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email', 'max:255'],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:255',
                'regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_])[^\s]+$/', // mayúscula, número, carácter especial, sin espacios
                'confirmed',
            ],
        ]);

        // Buscar usuario
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'No se encontró un usuario con ese correo.']);
        }

        // Evitar que la nueva contraseña sea igual a la anterior
        if (Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => 'La nueva contraseña no puede ser igual a la anterior.',
            ]);
        }

        // Restablecer contraseña usando token
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', 'La contraseña se restableció correctamente.')
            : back()->withErrors(['email' => __($status)]);
    }
}