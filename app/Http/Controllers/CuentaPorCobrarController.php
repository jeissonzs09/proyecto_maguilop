<?php

namespace App\Http\Controllers;

use App\Models\CuentaPorCobrar;
use App\Models\Factura;
use Illuminate\Http\Request;
use App\Helpers\PermisosHelper;


class CuentaPorCobrarController extends Controller
{
    public function index()
    {
       // if (!PermisosHelper::tienePermiso('CuentasPorCobrar', 'ver')) {
         //   abort(403, 'No tienes permiso para ver esta sección.');
        //}

        $cuentas = CuentaPorCobrar::with('factura.cliente')->orderByDesc('created_at')->get();

        return view('cuentas_por_cobrar.index', compact('cuentas'));
    }
}