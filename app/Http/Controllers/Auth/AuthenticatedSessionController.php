<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\RedirectResponse;
use App\Models\EmailVerificationCode;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Models\Bitacora;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        // Validaciones personalizadas
        $request->validate([
            'NombreUsuario' => [
                'required',
                'string',
                'min:3',
                'max:50',
                'regex:/^[a-zA-Z0-9._-]+$/'
            ],
            'password' => 'required|string'
        ], [
            'NombreUsuario.required' => 'El campo de usuario es obligatorio.',
            'NombreUsuario.min' => 'El usuario debe tener al menos 3 caracteres.',
            'NombreUsuario.max' => 'El usuario no puede superar los 50 caracteres.',
            'NombreUsuario.regex' => 'El usuario ingresado tiene un formato inválido.',
            'password.required' => 'Debe ingresar una contraseña.'
        ]);

        // Buscar usuario en base de datos
        $user = User::where('NombreUsuario', $request->NombreUsuario)->first();

        if (!$user) {
            // Log intento fallido
            Bitacora::create([
                'UsuarioID' => null,
                'Accion' => 'Login fallido',
                'Descripcion' => 'Usuario no encontrado: ' . $request->NombreUsuario,
                'FechaAccion' => now(),
                'TablaAfectada' => 'usuarios',
            ]);

            throw ValidationException::withMessages([
                'NombreUsuario' => '⚠️ El usuario es incorrecto.',
            ]);
        }

        if (isset($user->Estado) && strtolower($user->Estado) !== 'activo') {
            // Log intento de usuario inactivo
            Bitacora::create([
                'UsuarioID' => $user->UsuarioID,
                'Accion' => 'Login fallido',
                'Descripcion' => 'Usuario inactivo o bloqueado',
                'FechaAccion' => now(),
                'TablaAfectada' => 'usuarios',
            ]);

            throw ValidationException::withMessages([
                'NombreUsuario' => '⚠️ El usuario está inactivo o bloqueado. Contacte al administrador.',
            ]);
        }

        // Autenticación
if (!Auth::attempt([
    'NombreUsuario' => $request->NombreUsuario,
    'password' => $request->password,
])) {
    // Log intento fallido
    Bitacora::create([
        'UsuarioID' => $user->UsuarioID,
        'Accion' => 'Login fallido',
        'Descripcion' => 'Contraseña incorrecta',
        'FechaAccion' => now(),
        'TablaAfectada' => 'usuarios',
    ]);

    throw ValidationException::withMessages([
        'NombreUsuario' => 'Usuario o contraseña incorrectos.',
    ]);
}


        $request->session()->regenerate();

        $user = Auth::user();

        // Log login exitoso
        Bitacora::create([
            'UsuarioID' => $user->UsuarioID,
            'Accion' => 'Login exitoso',
            'Descripcion' => 'Inicio de sesión correcto',
            'FechaAccion' => now(),
            'TablaAfectada' => 'usuarios',
        ]);

        // Generar código OTP
        $code = random_int(100000, 999999);

        EmailVerificationCode::where('user_id', $user->UsuarioID)->delete();

        EmailVerificationCode::create([
            'user_id' => $user->UsuarioID,
            'code' => $code,
            'expires_at' => now()->addMinutes(5),
        ]);

        // Log generación de OTP
        Bitacora::create([
            'UsuarioID' => $user->UsuarioID,
            'Accion' => 'OTP generado',
            'Descripcion' => 'Código OTP enviado por correo',
            'FechaAccion' => now(),
            'TablaAfectada' => 'email_verification_codes',
        ]);

        // Enviar correo
        Mail::raw("Tu código de verificación es: $code", function ($message) use ($user) {
            $message->to($user->CorreoElectronico)
                ->subject('Código de verificación - Maguilop');
        });

        return redirect()->route('2fa.code.form');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if ($user) {
            Bitacora::create([
                'UsuarioID' => $user->UsuarioID,
                'Accion' => 'Logout',
                'Descripcion' => 'Cierre de sesión',
                'FechaAccion' => now(),
                'TablaAfectada' => 'usuarios',
            ]);
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}