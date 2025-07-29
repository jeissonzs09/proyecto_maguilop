<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bitacora;
use App\Models\Venta;
use App\Models\Compra;
use App\Models\Movimiento;
use App\Helpers\PermisosHelper;

class ReportesController extends Controller
{
    public function index()
    {
        if (!PermisosHelper::tienePermiso('Reportes', 'ver')) {
            abort(403, 'No tienes permiso para ver esta sección.');
        }

        // Puedes aplicar filtros o paginación si lo deseas
        $bitacoras = Bitacora::latest()->take(10)->get();
        $ventas = Venta::latest()->take(10)->get();
        $compras = Compra::latest()->take(10)->get();
        $movimientos = Movimiento::latest()->take(10)->get();

        return view('reporte.index', compact('bitacoras', 'ventas', 'compras', 'movimientos'));
    }

    public function show($tipo)
    {
        if (!PermisosHelper::tienePermiso('Reportes', 'ver')) {
            abort(403, 'No tienes permiso para ver esta sección.');
        }

        switch ($tipo) {
            case 'bitacoras':
                $datos = Bitacora::all();
                break;
            case 'ventas':
                $datos = Venta::with('cliente', 'detalleVentas')->get();
                break;
            case 'compras':
                $datos = Compra::with('proveedor', 'detalleCompras')->get();
                break;
            case 'movimientos':
                $datos = Movimiento::with('producto', 'almacen')->get();
                break;
            default:
                abort(404, 'Tipo de reporte no válido.');
        }

        return view('reporte.detalle', compact('datos', 'tipo'));
    }

    // Puedes agregar métodos para exportar a Excel o PDF si lo necesitas
}