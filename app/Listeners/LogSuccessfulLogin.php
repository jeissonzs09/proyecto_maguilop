<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Helpers\BitacoraHelper;
use App\Models\Configuracion;

class LogSuccessfulLogin
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $config = Configuracion::first();

        // Si la bitácora está desactivada o no se registran inicios, salimos
        if (!$config || !$config->bitacora_activa || !$config->registrar_inicio_sesion) {
            return;
        }

        // Obtenemos al usuario que acaba de iniciar sesión
        $usuario = $event->user;

        // Llamamos a tu helper de bitácora para registrar el evento
        BitacoraHelper::registrar(
            'Login', // Módulo
            'Iniciar sesión', // Acción
            'El usuario ' . ($usuario->NombreUsuario ?? 'Usuario desconocido') . ' inició sesión correctamente.'
            // Descripción
        );
    }
}