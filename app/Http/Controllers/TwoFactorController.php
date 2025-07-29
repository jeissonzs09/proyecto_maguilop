<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OTPHP\TOTP;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

class TwoFactorController extends Controller
{
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

        return view('auth.2fa-setup', [
            'qrCodeUrl' => $qrCodeUrl,
            'secret' => $secret,
        ]);
    }
}

