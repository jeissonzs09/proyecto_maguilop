<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\EmailVerificationCode;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailVerificationController extends Controller
{
    public function showForm()
    {
        return view('auth.verify-code');
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6',
        ]);

        $record = EmailVerificationCode::where('user_id', Auth::user()->UsuarioID)
            ->where('code', $request->code)
            ->where('expires_at', '>=', Carbon::now())
            ->first();

        if ($record) {
            // Borra el código usado
            $record->delete();

            // Marca en sesión como verificado
            session(['2fa_passed' => true]);

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors(['code' => 'Código inválido o expirado.']);
    }

    public function resendCode()
    {
        $user = auth()->user();

        if (!$user->empleado || !$user->empleado->persona) {
            Log::error("Falta empleado o persona para el usuario ID {$user->UsuarioID}");
            return back()->withErrors(['error' => 'No se pudo enviar el código porque falta información del empleado o persona.']);
        }

        $email = $user->empleado->persona->email;

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Log::error("Email inválido para usuario ID {$user->UsuarioID}: $email");
            return back()->withErrors(['error' => 'El correo electrónico del usuario no es válido.']);
        }

        // Eliminar códigos anteriores
        EmailVerificationCode::where('user_id', $user->UsuarioID)->delete();

        $code = random_int(100000, 999999);

        EmailVerificationCode::create([
            'user_id' => $user->UsuarioID,
            'code' => $code,
            'expires_at' => Carbon::now()->addMinutes(5),
        ]);

        Mail::raw("Tu nuevo código de verificación es: $code", function ($message) use ($email) {
            $message->to($email)
                    ->subject('Reenvío de código de verificación - Maguilop');
        });

        Log::info("Código 2FA enviado a $email para usuario ID {$user->UsuarioID}: $code");

        return back()->with('status', 'Nuevo código enviado al correo.');
    }
}


