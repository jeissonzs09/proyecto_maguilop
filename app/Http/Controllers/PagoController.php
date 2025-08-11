<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PagoCuentaPorCobrar;
use App\Models\CuentaPorCobrar;

class PagoController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'cuenta_id' => 'required|exists:cuentas_por_cobrar,id',
            'monto' => 'required|numeric|min:0.01',
        ]);

        $cuenta = CuentaPorCobrar::findOrFail($request->cuenta_id);

        $saldo = $cuenta->monto_total - $cuenta->monto_pagado;
        if ($request->monto > $saldo) {
            return back()->with('error', 'El monto del pago excede el saldo pendiente.');
        }

        // Crear el pago
        PagoCuentaPorCobrar::create([
            'cuenta_por_cobrar_id' => $cuenta->id,
            'monto' => $request->monto,
            'fecha_pago' => now(),
            'descripcion' => $request->descripcion, 
        ]);

        // Actualizar el monto pagado y estado
        $cuenta->monto_pagado += $request->monto;

        if ($cuenta->monto_pagado >= $cuenta->monto_total) {
            $cuenta->estado = 'Pagada';
        }

        $cuenta->save();

        return back()->with('success', 'Pago registrado correctamente.');
    }

    public function historial($id)
{
    $cuenta = CuentaPorCobrar::with(['factura.cliente', 'pagos'])->findOrFail($id);
    $pagos = $cuenta->pagos; // Extrae la colección de pagos
    $cliente = $cuenta->factura->cliente ?? null;

    return view('cuentas_por_cobrar.historial_pagos', compact('cuenta', 'pagos'));
}
}
