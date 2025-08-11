<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Factura;
use App\Models\DetalleFactura;
use App\Models\Cliente;
use App\Models\Empleado;
use App\Models\Producto;
use App\Helpers\PermisosHelper;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Helpers\BitacoraHelper;
use App\Models\Cai;

class FacturaController extends Controller
{
   public function index(Request $request)
{
    if (!PermisosHelper::tienePermiso('Factura', 'ver')) {
        abort(403, 'No tienes permiso para ver esta sección.');
    }

    $query = Factura::with(['cliente', 'empleado.persona', 'detalles.producto']);

    if ($request->filled('search')) {
        $search = $request->search;

        $query->where('Fecha', 'LIKE', "%{$search}%")
            ->orWhere('Total', 'LIKE', "%{$search}%")
            ->orWhereHas('cliente', fn($q) =>
                $q->where('NombreCliente', 'LIKE', "%{$search}%")
            )
            ->orWhereHas('empleado.persona', fn($q) =>
                $q->where('Nombre', 'LIKE', "%{$search}%")
                  ->orWhere('Apellido', 'LIKE', "%{$search}%")
            )
            ->orWhereHas('detalles.producto', fn($q) =>
                $q->where('NombreProducto', 'LIKE', "%{$search}%")
            )
            ->orWhereHas('detalles', fn($q) =>
                $q->where('Cantidad', 'LIKE', "%{$search}%")
                  ->orWhere('PrecioUnitario', 'LIKE', "%{$search}%")
                  ->orWhere('Subtotal', 'LIKE', "%{$search}%")
            );
    }

    $query->orderByDesc('FacturaID');

    $facturas = $query->paginate(10);

    $clientes = Cliente::orderBy('NombreCliente')->get();
    $productos = Producto::where('Estado', 'Activo')->orderBy('NombreProducto')->get();

    return view('facturas.index', compact('facturas', 'clientes', 'productos'));
}


    public function create()
    {
        $clientes = Cliente::all();
        $productos = Producto::where('Estado', 'Activo')->get();

        return view('facturas.create', compact('clientes', 'productos'));
    }

 public function store(Request $request)
{
    $request->validate([
    'ClienteID' => 'required|integer|exists:cliente,ClienteID',
    'RTN' => 'nullable|string|max:14',
    'tipo_pago' => 'required|in:Contado,Crédito',
    'detalles' => 'required|array|min:1',
    'detalles.*.ProductoID' => 'required|integer|exists:producto,ProductoID',
    'detalles.*.Cantidad' => 'required|integer|min:1',
    'detalles.*.PrecioUnitario' => 'required|numeric|min:0',
]);
    $empleadoID = Auth::user()->EmpleadoID;

    if (!$empleadoID) {
        return back()->withErrors(['Empleado no asociado al usuario actual.']);
    }

    // Validar stock antes de crear la factura
    foreach ($request->detalles as $detalle) {
        $producto = Producto::find($detalle['ProductoID']);
        if (!$producto) {
            return back()->withErrors(['error' => "Producto ID {$detalle['ProductoID']} no encontrado."]);
        }
        if ($producto->Stock < $detalle['Cantidad']) {
            return back()->withErrors(['error' => "Stock insuficiente para el producto {$producto->NombreProducto}. Disponible: {$producto->Stock}, solicitado: {$detalle['Cantidad']}"]);
        }
    }

    DB::beginTransaction();

    try {
        $subtotal = collect($request->detalles)->sum(function ($detalle) {
    return $detalle['Cantidad'] * $detalle['PrecioUnitario'];
});

$impuesto = $subtotal * 0.15;
$total = $subtotal + $impuesto;

        $cai = Cai::first(); // O puedes aplicar un where si luego tienes múltiples CAI activos

if (!$cai) {
    return back()->withErrors(['error' => 'No hay CAI configurado.']);
}

if (now()->toDateString() > $cai->fecha_limite_emision) {
    return back()->withErrors(['error' => 'El CAI ha vencido.']);
}

// Obtener número siguiente
$numeroSiguiente = $cai->facturas_emitidas + 1;
$inicio = (int) substr($cai->rango_inicial, -8);
$fin = (int) substr($cai->rango_final, -8);

if ($numeroSiguiente > $fin) {
    return back()->withErrors(['error' => 'Se ha alcanzado el límite de facturación permitido por el CAI.']);
}

// Generar NumeroFactura (con formato 000-001-01-00000001)
$prefijo = substr($cai->rango_inicial, 0, 10);
$numeroFactura = $prefijo . '-' . str_pad($numeroSiguiente, 8, '0', STR_PAD_LEFT);


    $factura = Factura::create([
    'NumeroFactura' => $numeroFactura,
    'ClienteID' => $request->ClienteID,
    'RTN' => $request->RTN,
    'EmpleadoID' => $empleadoID,
    'Fecha' => now(),
    'Total' => $total, // ✅ incluye el impuesto ahora
    'Estado' => 'Activa',
    'tipo_pago' => $request->tipo_pago,
    'CAI' => $cai->codigo,
]);


// Si es al crédito, registrar en cuentas por cobrar
if ($request->tipo_pago === 'Crédito') {
    \App\Models\CuentaPorCobrar::create([
    'FacturaID' => $factura->FacturaID,
    'fecha_vencimiento' => now()->addDays(30),
    'monto_total' => $total, // ✅ incluye impuesto
    'monto_pagado' => 0,
    'estado' => 'Pendiente',
]);
}

// Incrementar facturas emitidas en el CAI
$cai->facturas_emitidas = $numeroSiguiente;
$cai->save();


        foreach ($request->detalles as $detalle) {
            DetalleFactura::create([
                'FacturaID' => $factura->FacturaID,
                'ProductoID' => $detalle['ProductoID'],
                'Cantidad' => $detalle['Cantidad'],
                'PrecioUnitario' => $detalle['PrecioUnitario'],
                'Subtotal' => $detalle['Cantidad'] * $detalle['PrecioUnitario'],
            ]);

            $producto = Producto::find($detalle['ProductoID']);
            $producto->Stock -= $detalle['Cantidad'];
            $producto->save();
        }

        BitacoraHelper::registrar(
            'CREAR',
            'factura',
            'Factura ID: ' . $factura->FacturaID . ' creada por el empleado ID: ' . $empleadoID,
            null,
            $factura->toArray(),
            'Módulo de Factura'
        );

        DB::commit();

        return redirect()->route('facturas.index')->with('success', 'Factura registrada correctamente.');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withErrors(['error' => 'Error al registrar la factura: ' . $e->getMessage()]);
    }
}

    public function destroy($id)
    {
        if (!PermisosHelper::tienePermiso('Factura', 'eliminar')) {
            abort(403);
        }

        $factura = Factura::findOrFail($id);

        $anterior = $factura->toArray();

        DB::beginTransaction();

        try {
            DetalleFactura::where('FacturaID', $id)->delete();
            $factura->delete();

            BitacoraHelper::registrar(
                'ELIMINAR',
                'factura',
                'Se eliminó la factura ID: ' . $id,
                $anterior,
                null,
                'Módulo de Factura'
            );

            DB::commit();

            return redirect()->route('facturas.index')->with('error', 'Factura eliminada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al eliminar la factura: ' . $e->getMessage()]);
        }
    }

    public function exportarPDF(Request $request)
    {
        $query = Factura::with(['cliente', 'empleado.persona', 'detalles.producto']);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where('Fecha', 'LIKE', "%{$search}%")
                ->orWhere('Total', 'LIKE', "%{$search}%")
                ->orWhereHas('cliente', fn($q) =>
                    $q->where('NombreCliente', 'LIKE', "%{$search}%")
                )
                ->orWhereHas('empleado.persona', fn($q) =>
                    $q->where('Nombre', 'LIKE', "%{$search}%")
                      ->orWhere('Apellido', 'LIKE', "%{$search}%")
                )
                ->orWhereHas('detalles.producto', fn($q) =>
                    $q->where('NombreProducto', 'LIKE', "%{$search}%")
                )
                ->orWhereHas('detalles', fn($q) =>
                    $q->where('Cantidad', 'LIKE', "%{$search}%")
                      ->orWhere('PrecioUnitario', 'LIKE', "%{$search}%")
                      ->orWhere('Subtotal', 'LIKE', "%{$search}%")
                );
        }

        $facturas = $query->get();

        $logoPath = public_path('images/logo-maguilop.png');
        $logoBase64 = base64_encode(file_get_contents($logoPath));
        $logoMime = mime_content_type($logoPath);
        $logoSrc = "data:$logoMime;base64,$logoBase64";

        $pdf = Pdf::loadView('facturas.pdf', compact('facturas', 'logoSrc'))
            ->setPaper('letter', 'portrait');

        return $pdf->download('facturas.pdf');
    }

    public function generarFacturaPDF($id)
{
    $factura = Factura::with(['cliente', 'empleado.persona', 'detalles.producto'])->findOrFail($id);

    $logoPath = public_path('images/logo-maguilop.png');
    $logoBase64 = base64_encode(file_get_contents($logoPath));
    $logoMime = mime_content_type($logoPath);
    $logoSrc = "data:$logoMime;base64,$logoBase64";

    $cai = \App\Models\Cai::first(); // 👈 Aquí está la clave

    return Pdf::loadView('facturas.pdf', compact('factura', 'logoSrc', 'cai'))
        ->setPaper('letter', 'portrait')
        ->download('factura_' . $factura->NumeroFactura . '.pdf');
}


    public function cancelar($id)
    {
        $factura = Factura::with('detalles')->findOrFail($id);

        if ($factura->Estado === 'Cancelada') {
            return redirect()->back()->with('error', 'La factura ya está cancelada.');
        }

        DB::beginTransaction();

        try {
            foreach ($factura->detalles as $detalle) {
                $producto = Producto::find($detalle->ProductoID);
                if ($producto) {
                    $producto->Stock += $detalle->Cantidad;
                    $producto->save();
                }
            }

            $factura->Estado = 'Cancelada';
            $factura->save();

            BitacoraHelper::registrar(
                'CANCELAR',
                'factura',
                'Se canceló la factura ID: ' . $factura->FacturaID . ' y se restauró el stock.',
                null,
                $factura->toArray(),
                'Módulo de Factura'
            );

            DB::commit();

            return redirect()->route('facturas.index')->with('success', 'Factura cancelada y stock actualizado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al cancelar la factura: ' . $e->getMessage());
        }
    }
}
