<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Empleado;
use App\Models\Producto;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Helpers\PermisosHelper;
use App\Helpers\BitacoraHelper;


class VentaController extends Controller
{
public function index(Request $request)
{
    if (!PermisosHelper::tienePermiso('Ventas', 'ver')) {
        abort(403, 'No tienes permiso para ver esta sección.');
    }

    $query = Venta::with(['cliente', 'empleado.persona', 'producto']);

    if ($request->filled('search')) {
        $search = $request->search;

        $query->where('FechaVenta', 'LIKE', "%{$search}%")
              ->orWhere('TotalVenta', 'LIKE', "%{$search}%")
              ->orWhereHas('cliente', fn($q) =>
                  $q->where('NombreCliente', 'LIKE', "%{$search}%")
              )
              ->orWhereHas('empleado.persona', fn($q) =>
                  $q->where('Nombre', 'LIKE', "%{$search}%")
                    ->orWhere('Apellido', 'LIKE', "%{$search}%")
              )
              ->orWhereHas('producto', fn($q) =>
                  $q->where('NombreProducto', 'LIKE', "%{$search}%")
              );
    }

    $ventas = $query->paginate(5);

    // Aquí cargas para el modal los datos que necesitas:
    $clientes = Cliente::all();
    $empleados = Empleado::with('persona')->get();
    $productos = Producto::all();

    // Para el modal, si quieres que tenga datos, pasa la primera venta o un objeto vacío
    $venta = $ventas->first() ?? new Venta();

    // Ahora pasas todo a la vista
    return view('ventas.index', compact('ventas', 'clientes', 'empleados', 'productos', 'venta'));
}


public function create()
{
$clientes = Cliente::all();
$empleados = Empleado::all();
$productos = Producto::all(); // esto es clave
return view('ventas.create', compact('clientes', 'empleados', 'productos'));
}

    public function store(Request $request)
{
    $request->validate([
        'ClienteID' => 'required|integer|exists:cliente,ClienteID',
        'EmpleadoID' => 'required|integer|exists:empleado,EmpleadoID',
        'ProductoID' => 'required|integer|exists:producto,ProductoID',
        'FechaVenta' => 'required|date',
        'TotalVenta' => 'required|numeric',
    ]);

    $venta = Venta::create($request->all());

    // Registrar en bitácora
    BitacoraHelper::registrar(
        'CREAR',
        'venta',
        'Se registró una nueva venta ID: ' . $venta->VentaID,
        null,
        json_encode($venta->toArray()),
        'Módulo de Ventas'
    );

    return redirect()->route('ventas.index')->with('success', 'Venta registrada correctamente.');
}


public function edit($id)
{
    $venta = Venta::findOrFail($id);
    $clientes = Cliente::all();
    $empleados = Empleado::all();
    $productos = Producto::all();

    return view('ventas.edit', compact('venta', 'clientes', 'empleados', 'productos'));
}



    public function update(Request $request, $id)
{
    $request->validate([
        'ClienteID' => 'required|integer|exists:cliente,ClienteID',
        'EmpleadoID' => 'required|integer|exists:empleado,EmpleadoID',
        'ProductoID' => 'required|integer|exists:producto,ProductoID',
        'FechaVenta' => 'required|date',
        'TotalVenta' => 'required|numeric',
    ]);

    $venta = Venta::findOrFail($id);
    $anterior = json_encode($venta->toArray());

    $venta->update($request->all());

    BitacoraHelper::registrar(
        'ACTUALIZAR',
        'venta',
        'Se actualizó la venta ID: ' . $id,
        $anterior,
        json_encode($venta->toArray()),
        'Módulo de Ventas'
    );

    return redirect()->route('ventas.index')->with('success', 'Venta actualizada correctamente.');
}


    public function destroy($id)
{
    $venta = Venta::findOrFail($id);
    $anterior = json_encode($venta->toArray());

    $venta->delete();

    BitacoraHelper::registrar(
        'ELIMINAR',
        'venta',
        'Se eliminó la venta ID: ' . $id,
        $anterior,
        null,
        'Módulo de Ventas'
    );

    return redirect()->route('ventas.index')->with('success', 'Venta eliminada correctamente.');
}



public function exportarPDF(Request $request)
{
    $query = Venta::with(['cliente', 'empleado.persona', 'producto']);

    if ($request->filled('search')) {
        $search = $request->search;

        $query->where('FechaVenta', 'LIKE', "%{$search}%")
              ->orWhere('TotalVenta', 'LIKE', "%{$search}%")
              ->orWhereHas('cliente', function($q) use ($search) {
                  $q->where('NombreCliente', 'LIKE', "%{$search}%");
              })
              ->orWhereHas('empleado.persona', function($q) use ($search) {
                  $q->where('Nombre', 'LIKE', "%{$search}%")
                    ->orWhere('Apellido', 'LIKE', "%{$search}%");
              })
              ->orWhereHas('producto', function($q) use ($search) {
                  $q->where('NombreProducto', 'LIKE', "%{$search}%");
              });
    }

    $ventas = $query->get(); // ✅ Aquí se ejecuta después de aplicar todos los filtros

    $pdf = Pdf::loadView('ventas.pdf', compact('ventas'))->setPaper('a4', 'landscape');
    return $pdf->download('ventas.pdf');
}


}