<?php

namespace App\Helpers;

use App\Models\Bitacora;
use Illuminate\Support\Facades\Auth;

class BitacoraHelper
{
    public static function registrar($accion, $tabla, $descripcion, $datosPrevios = null, $datosNuevos = null, $modulo = '', $resultado = 'ÉXITO')
    {
        Bitacora::create([
            'UsuarioID' => Auth::id(),
            'Accion' => $accion,
            'TablaAfectada' => $tabla,
            'FechaAccion' => now(),
            'Descripcion' => $descripcion,
            'DatosPrevios' => $datosPrevios ? json_encode($datosPrevios) : null,
            'DatosNuevos' => $datosNuevos ? json_encode($datosNuevos) : null,
            'Modulo' => $modulo,
            'Resultado' => $resultado
        ]);
    }
}
