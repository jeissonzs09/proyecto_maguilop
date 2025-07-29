<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reparacion;
use App\Helpers\PermisosHelper;
use App\Models\Cliente; // ✅ Correcto
use App\Models\Producto;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Helpers\BitacoraHelper;


class ReparacionController extends Controller
{

public function index(Request $request)
{
    if (!PermisosHelper::tienePermiso('Reparaciones', 'ver')) {
        abort(403, 'No tienes permiso para ver esta sección.');
    }

    $query = Reparacion::query()
        ->join('cliente', 'reparacion.ClienteID', '=', 'cliente.ClienteID')
        ->join('producto', 'reparacion.ProductoID', '=', 'producto.ProductoID')
        ->select('reparacion.*'); // 👈 evita conflictos por columnas duplicadas

    if ($request->filled('search')) {
        $search = $request->search;

        $query->whereRaw("CONCAT_WS(' ',
            ReparacionID,
            cliente.NombreCliente,
            producto.NombreProducto,
            FechaEntrada,
            FechaSalida,
            reparacion.Estado,
            DescripcionProblema,
            Costo
        ) LIKE ?", ["%{$search}%"]);
    }

    $reparaciones = $query->paginate(10); // 👈 paginación de 5 registros


    // 👇 AÑADE ESTAS DOS LÍNEAS
    $clientes = \App\Models\Cliente::all();
    $productos = \App\Models\Producto::all();

    return view('reparaciones.index', compact('reparaciones', 'clientes', 'productos'));
}




    public function create()
    {
            if (!PermisosHelper::tienePermiso('Reparaciones', 'crear')) {
        abort(403);
    }
    $clientes = Cliente::all();
    $productos = Producto::all();

    return view('reparaciones.create', compact('clientes', 'productos'));
    }

    public function store(Request $request)
{
    $request->validate([
        'ClienteID' => 'required|integer',
        'ProductoID' => 'required|integer',
        'FechaEntrada' => 'required|date',
        'FechaSalida' => 'nullable|date|after_or_equal:FechaEntrada',
        'DescripcionProblema' => 'nullable|string',
        'Estado' => 'required|in:Pendiente,En proceso,Finalizado',
        'Costo' => 'required|numeric|min:0',
    ]);

    $reparacion = Reparacion::create($request->all());

    // Registrar en bitácora
    BitacoraHelper::registrar(
        'CREAR',
        'reparacion',
        'Se registró una nueva reparación ID: ' . $reparacion->ReparacionID,
        null,
        json_encode($reparacion->toArray()),
        'Módulo de Reparaciones'
    );

    return redirect()->route('reparaciones.index')->with('success', 'Reparación registrada correctamente.');
}


    public function edit($id)
    {
            if (!PermisosHelper::tienePermiso('Reparaciones', 'editar')) {
        abort(403);
    }
    $reparacion = Reparacion::findOrFail($id);
    $clientes = Cliente::all();
    $productos = Producto::all();

    return view('reparaciones.edit', compact('reparacion', 'clientes', 'productos'));
    }

    public function update(Request $request, $id)
{
    $request->validate([
        'ClienteID' => 'required|integer',
        'ProductoID' => 'required|integer',
        'FechaEntrada' => 'required|date',
        'FechaSalida' => 'nullable|date|after_or_equal:FechaEntrada',
        'DescripcionProblema' => 'nullable|string',
        'Estado' => 'required|in:Pendiente,En proceso,Finalizado',
        'Costo' => 'required|numeric|min:0',
    ]);

    $reparacion = Reparacion::findOrFail($id);
    $anterior = json_encode($reparacion->toArray());

    $reparacion->update($request->all());

    BitacoraHelper::registrar(
        'ACTUALIZAR',
        'reparacion',
        'Se actualizó la reparación ID: ' . $id,
        $anterior,
        json_encode($reparacion->toArray()),
        'Módulo de Reparaciones'
    );

    return redirect()->route('reparaciones.index')->with('success', 'Reparación actualizada correctamente.');
}


    public function destroy($id)
{
    if (!PermisosHelper::tienePermiso('Reparaciones', 'eliminar')) {
        abort(403);
    }

    $reparacion = Reparacion::findOrFail($id);
    $anterior = json_encode($reparacion->toArray());

    $reparacion->delete();

    BitacoraHelper::registrar(
        'ELIMINAR',
        'reparacion',
        'Se eliminó la reparación ID: ' . $id,
        $anterior,
        null,
        'Módulo de Reparaciones'
    );

    return redirect()->route('reparaciones.index')->with('success', 'Reparación eliminada correctamente.');
}


public function exportarPDF(Request $request)
{
    $query = Reparacion::with(['cliente', 'producto']);

    if ($request->filled('search')) {
        $search = $request->search;
        $query->whereHas('cliente', fn($q) => $q->where('NombreCliente', 'LIKE', "%{$search}%"))
              ->orWhereHas('producto', fn($q) => $q->where('NombreProducto', 'LIKE', "%{$search}%"))
              ->orWhere('DescripcionProblema', 'LIKE', "%{$search}%");
    }

    $reparaciones = $query->get();

    $pdf = Pdf::loadView('reparaciones.pdf', compact('reparaciones'))
              ->setPaper('a4', 'landscape');

    return $pdf->download('reparaciones.pdf');
}



    public function show($id)
{
    if (!PermisosHelper::tienePermiso('Reparaciones', 'ver')) {
        abort(403, 'No tienes permiso para ver esta reparación.');
    }

    $reparacion = Reparacion::with(['cliente', 'producto'])->findOrFail($id);

    return view('reparaciones.show', compact('reparacion'));
}
}

