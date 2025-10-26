<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use App\Models\Bitacora;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class LogFailedLogin
{
    public function handle(Failed $event)
    {
        try {
            $credentials = $event->credentials ?? [];

            // Extraer NombreUsuario real que se escribió en el login
            $nombreUsuario = $credentials['NombreUsuario']
                ?? $credentials['username']
                ?? $credentials['email']
                ?? 'sin_identificador';

            $ip = request()->ip();

            // Si existe usuario real, guardamos su ID. Sino, ponemos 0.
            $usuarioID = 0;
            if ($event->user) {
                $usuarioID = $event->user->UsuarioID ?? $event->user->id ?? 0;
            }

            // Texto descriptivo para bitácora
            $desc = "Intento fallido de inicio de sesión. Usuario ingresado: {$nombreUsuario}. IP: {$ip}.";

            // Limpiamos las credenciales antes de guardar (sin password)
            $datosPrevios = collect($credentials)->except(['password']);

            Bitacora::create([
                'UsuarioID'     => $usuarioID,   // 0 si no hay usuario real
                'Accion'        => 'Intento fallido de ingreso',
                'TablaAfectada' => 'usuario',
                'FechaAccion'   => Carbon::now(),
                'Descripcion'   => $desc,
                'DatosPrevios'  => json_encode($datosPrevios),
                'DatosNuevos'   => null,
                'Modulo'        => 'Autenticación',
                'Resultado'     => 'Error',
            ]);

            Log::info('Bitácora: intento fallido guardado para '.$nombreUsuario);
        } catch (\Throwable $e) {
            Log::error('Error al escribir bitácora (login fallido): ' . $e->getMessage());
        }
    }
}