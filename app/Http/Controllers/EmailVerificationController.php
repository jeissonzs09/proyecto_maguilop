<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\EmailVerificationCode;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;


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

    // Elimina códigos anteriores
    EmailVerificationCode::where('user_id', $user->UsuarioID)->delete();

    // Genera nuevo código
    $code = random_int(100000, 999999);

    EmailVerificationCode::create([
        'user_id' => $user->UsuarioID,
        'code' => $code,
        'expires_at' => Carbon::now()->addMinutes(5),
    ]);

    // Enviar correo
    Mail::raw("Tu nuevo código de verificación es: $code", function ($message) use ($user) {
        $message->to($user->empleado->persona->CorreoElectronico)
                ->subject('Reenvío de código de verificación - Maguilop');
    });

    return back()->with('status', 'Nuevo código enviado al correo.');
}
}



