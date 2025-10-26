<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use App\Models\Bitacora;
use App\Models\Configuracion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class LogLogout
{
    public function handle(Logout $event)
    {
        try {
            $config = Configuracion::first();

            // Si la bitácora está desactivada o no se registran cierres, salimos
            if (!$config || !$config->bitacora_activa || !$config->registrar_cierre_sesion) {
                return;
            }

            $user = $event->user;

            // Si no hay usuario autenticado, no seguimos
            if (! $user) {
                return;
            }

            // Usa UsuarioID real, no id
            $usuarioID = $user->UsuarioID ?? $user->id ?? null;

            // Toma el nombre del usuario de la BD (NombreUsuario)
            $nombreUsuario = $user->NombreUsuario ?? 'Usuario desconocido';

            // Cierre de sesión
            $desc = "El usuario {$nombreUsuario} cerró sesión correctamente.";

            Bitacora::create([
                'UsuarioID'     => $usuarioID,
                'Accion'        => 'Cerrar Sesión',
                'TablaAfectada' => 'usuario',
                'FechaAccion'   => Carbon::now(),
                'Descripcion'   => $desc,
                'DatosPrevios'  => null,
                'DatosNuevos'   => null,
                'Modulo'        => 'Autenticación',
                'Resultado'     => 'Éxito',
            ]);

            Log::info('Registrado en bitácora el logout del usuario '.$nombreUsuario);

        } catch (\Throwable $e) {
            Log::error('Error al escribir bitácora (logout): ' . $e->getMessage());
        }
    }
}