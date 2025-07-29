<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Helpers\BitacoraHelper;

class LogSuccessfulLogin
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        // Aquí obtenemos al usuario que acaba de iniciar sesión
        $usuario = $event->user;

        // Llamamos a tu helper de bitácora para registrar el evento
        BitacoraHelper::registrar(
            'Login', // Módulo
            'iniciar sesión', // Acción
            'El usuario ' . $usuario->NombreUsuario . ' inició sesión correctamente.'
 // Descripción
        );
    }
}

