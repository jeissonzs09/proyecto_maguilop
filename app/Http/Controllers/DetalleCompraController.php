<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DetalleCompra;
use App\Models\Producto;
use App\Models\Compra;
use App\Helpers\PermisosHelper;
use App\Helpers\BitacoraHelper;
use Barryvdh\DomPDF\Facade\Pdf;

class DetalleCompraController extends Controller
{
    public function index(Request $request)
    {
        if (!PermisosHelper::tienePermiso('DetalleCompras', 'ver')) {
            abort(403, 'No tienes permiso para ver este módulo.');
        }

        $search = $request->input('search');

        $detalleCompras = DetalleCompra::with(['producto', 'compra'])
            ->when($search, function ($query, $search) {
                $query->where('DetalleCompraID', 'like', "%{$search}%")
                    ->orWhereHas('producto', function ($q) use ($search) {
                        $q->where('NombreProducto', 'like', "%{$search}%");
                    })
                    ->orWhereHas('compra', function ($q) use ($search) {
                        $q->where('CompraID', 'like', "%{$search}%")
                            ->orWhere('FechaCompra', 'like', "%{$search}%");
                    });
            })
            ->orderBy('DetalleCompraID', 'desc')
            ->paginate(10);

        $compras = Compra::all();
        $productos = Producto::all();

        BitacoraHelper::registrar('DetalleCompras', 'ver', 'Se consultó la lista de detalles de compras.');

        return view('detallecompras.index', [
            'detalleCompras' => $detalleCompras,
            'search' => $search,
            'compras' => $compras,
            'productos' => $productos,
        ]);
    }

    public function store(Request $request)
    {
        if (!PermisosHelper::tienePermiso('DetalleCompras', 'crear')) {
            abort(403, 'No tienes permiso para crear.');
        }

        $validated = $request->validate([
            'CompraID' => 'required|integer|exists:compra,CompraID',  // tabla singular "compra"
            'ProductoID' => 'required|integer|exists:producto,ProductoID', // tabla singular "producto"
            'Cantidad' => 'required|integer|min:1',
            'PrecioUnitario' => 'required|numeric|min:0',
        ]);

        $detalle = new DetalleCompra();
        $detalle->CompraID = $validated['CompraID'];
        $detalle->ProductoID = $validated['ProductoID'];
        $detalle->Cantidad = $validated['Cantidad'];
        $detalle->PrecioUnitario = $validated['PrecioUnitario'];
        $detalle->Subtotal = $validated['Cantidad'] * $validated['PrecioUnitario'];
        $detalle->save();

        BitacoraHelper::registrar('DetalleCompras', 'crear', 'Se creó un nuevo detalle de compra con ID ' . $detalle->DetalleCompraID);

        return redirect()->route('detallecompras.index')->with('success', 'Detalle de compra creado correctamente.');
    }

    public function update(Request $request, $id)
    {
        if (!PermisosHelper::tienePermiso('DetalleCompras', 'editar')) {
            abort(403, 'No tienes permiso para editar.');
        }

        $validated = $request->validate([
            'CompraID' => 'required|integer|exists:compra,CompraID',
            'ProductoID' => 'required|integer|exists:producto,ProductoID',
            'Cantidad' => 'required|integer|min:1',
            'PrecioUnitario' => 'required|numeric|min:0',
        ]);

        $detalle = DetalleCompra::findOrFail($id);
        $detalle->CompraID = $validated['CompraID'];
        $detalle->ProductoID = $validated['ProductoID'];
        $detalle->Cantidad = $validated['Cantidad'];
        $detalle->PrecioUnitario = $validated['PrecioUnitario'];
        $detalle->Subtotal = $validated['Cantidad'] * $validated['PrecioUnitario'];
        $detalle->save();

        BitacoraHelper::registrar('DetalleCompras', 'editar', 'Se actualizó el detalle de compra con ID ' . $detalle->DetalleCompraID);

        return redirect()->route('detallecompras.index')->with('success', 'Detalle de compra actualizado correctamente.');
    }

    public function destroy($id)
    {
        if (!PermisosHelper::tienePermiso('DetalleCompras', 'eliminar')) {
            abort(403, 'No tienes permiso para eliminar.');
        }

        $detalle = DetalleCompra::findOrFail($id);
        $detalle->delete();

        BitacoraHelper::registrar('DetalleCompras', 'eliminar', 'Se eliminó el detalle de compra con ID ' . $id);

        return redirect()->route('detallecompras.index')->with('success', 'Detalle de compra eliminado correctamente.');
    }

    public function exportarPDF(Request $request)
    {
        if (!PermisosHelper::tienePermiso('DetalleCompras', 'ver')) {
            abort(403, 'No tienes permiso para exportar.');
        }

        $query = DetalleCompra::with(['compra', 'producto']);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where('DetalleCompraID', 'LIKE', "%{$search}%")
                ->orWhereHas('compra', function ($q) use ($search) {
                    $q->where('CompraID', 'LIKE', "%{$search}%")
                        ->orWhere('Estado', 'LIKE', "%{$search}%")
                        ->orWhere('FechaCompra', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('producto', function ($q) use ($search) {
                    $q->where('NombreProducto', 'LIKE', "%{$search}%");
                });
        }

        $detalles_compra = $query->get();

        BitacoraHelper::registrar('DetalleCompras', 'exportar', 'Se exportó a PDF la lista de detalles de compra.');

        $pdf = Pdf::loadView('detallecompras.pdf', compact('detalles_compra'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('detalles_compra.pdf');
    }
}