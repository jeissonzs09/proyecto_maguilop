<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Empleado;
use App\Models\Pedido;
use App\Models\Venta;
use App\Models\Reparacion;
use App\Models\Compra;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
public function index()
{
    // Totales
    $totalClientes = Cliente::count();
    $totalEmpleados = Empleado::count();
    $totalPedidos = Pedido::count();
    $totalVentas = Venta::sum('TotalVenta');

    // Reparaciones por estado
    $reparacionesPorEstado = Reparacion::selectRaw('Estado, COUNT(*) as total')
        ->groupBy('Estado')
        ->pluck('total', 'Estado')
        ->toArray();

    // Ventas por mes
    $ventasPorMes = Venta::selectRaw("DATE_FORMAT(FechaVenta, '%b') as mes, SUM(TotalVenta) as total")
        ->groupBy('mes')
        ->orderByRaw("STR_TO_DATE(mes, '%b')")
        ->pluck('total', 'mes')
        ->toArray();

    // Compras por mes (nuevo)
    $comprasPorMes = Compra::selectRaw("MONTH(FechaCompra) as mes, SUM(TotalCompra) as total")
        ->where('FechaCompra', '>=', Carbon::now()->subMonths(6))
        ->groupBy(DB::raw("MONTH(FechaCompra)"))
        ->orderBy('mes')
        ->pluck('total', 'mes');

    // Formatear nombres de los meses
    $meses = [1=>'Ene',2=>'Feb',3=>'Mar',4=>'Abr',5=>'May',6=>'Jun',7=>'Jul',8=>'Ago',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dic'];
    $comprasPorMesFormateado = [];
    foreach ($comprasPorMes as $mes => $total) {
        $comprasPorMesFormateado[$meses[$mes]] = round($total, 2);
    }

    return view('dashboard', compact(
        'totalClientes',
        'totalEmpleados',
        'totalPedidos',
        'totalVentas',
        'reparacionesPorEstado',
        'ventasPorMes',
        'comprasPorMesFormateado' // nuevo
    ));
}
}

