<?php

namespace App\Helpers;

use App\Models\Bitacora;
use App\Models\Venta;
use App\Models\Compra;
use App\Models\Movimiento;

class Reporte
{
    public static function obtenerResumen()
    {
        return [
            'bitacoras' => Bitacora::latest()->take(10)->get(),
            'ventas' => Venta::latest()->take(10)->get(),
            'compras' => Compra::latest()->take(10)->get(),
            'movimientos' => Movimiento::latest()->take(10)->get(),
        ];
    }
}