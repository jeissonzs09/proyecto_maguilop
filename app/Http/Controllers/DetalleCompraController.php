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

        BitacoraHelper::registrar('DetalleCompras', 'ver', 'Se consultó la lista de detalles de compras.');

        return view('detallecompras.index', compact('detalleCompras', 'search'));
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
