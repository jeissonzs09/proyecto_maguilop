<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OTPHP\TOTP;
use App\Models\Bitacora;

class TwoFactorController extends Controller
{
    // Configuración de 2FA
    public function setup()
    {
        $user = Auth::user();

        // Generar la clave secreta
        $totp = TOTP::create();
        $totp->setLabel($user->NombreUsuario);
        $totp->setIssuer('Maguilop');
        $secret = $totp->getSecret();

        // Guardarla en la base de datos
        $user->two_factor_secret = $secret;
        $user->save();

        // Crear el QR Code en base64
        $qrCodeUrl = $totp->getProvisioningUri();

        // Registrar log de configuración 2FA
        Bitacora::create([
            'UsuarioID' => $user->UsuarioID,
            'accion' => '2FA setup generado',
            'descripcion' => 'Se generó y guardó la clave secreta para 2FA',
        ]);

        return view('auth.2fa-setup', [
            'qrCodeUrl' => $qrCodeUrl,
            'secret' => $secret,
        ]);
    }

    // Verificación de código OTP
    public function verify(Request $request)
    {
        $user = Auth::user();
        $code = $request->input('code');

        $totp = TOTP::create($user->two_factor_secret);

        if ($totp->verify($code)) {
            // Log OTP correcto
            Bitacora::create([
                'UsuarioID' => $user->UsuarioID,
                'accion' => 'OTP verificado',
                'descripcion' => 'Código OTP correcto',
            ]);
            return redirect()->intended('dashboard');
        } else {
            // Log OTP fallido
            Bitacora::create([
                'UsuarioID' => $user->UsuarioID,
                'accion' => 'OTP fallido',
                'descripcion' => 'Código OTP incorrecto',
            ]);
            return back()->withErrors(['code' => 'Código OTP inválido.']);
        }
    }
}