<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Helpers\PermisosHelper;
use App\Helpers\BitacoraHelper;

class PedidoController extends Controller
{
   public function index(Request $request)
{
    if (!PermisosHelper::tienePermiso('Pedidos', 'ver')) {
        abort(403, 'No tienes permiso para ver esta sección.');
    }

    $query = Pedido::with(['cliente', 'empleado.persona', 'detalles']);

    if ($request->filled('search')) {
        $search = $request->search;

        $query->where('FechaPedido', 'LIKE', "%{$search}%")
              ->orWhere('FechaEntrega', 'LIKE', "%{$search}%")
              ->orWhere('Estado', 'LIKE', "%{$search}%")
              ->orWhereHas('cliente', fn($q) =>
                  $q->where('NombreCliente', 'LIKE', "%{$search}%")
              )
              ->orWhereHas('empleado.persona', fn($q) =>
                  $q->where('Nombre', 'LIKE', "%{$search}%")
                    ->orWhere('Apellido', 'LIKE', "%{$search}%")
              )
              ->orWhereHas('producto', fn($q) =>
                  $q->where('NombreProducto', 'LIKE', "%{$search}%")
              )
              ->orWhereHas('detalles', fn($q) =>
                  $q->where('Cantidad', 'LIKE', "%{$search}%")
                    ->orWhere('PrecioUnitario', 'LIKE', "%{$search}%")
                    ->orWhere('Subtotal', 'LIKE', "%{$search}%")
              );
    }
    
    $pedidos = $query->orderBy('PedidoID', 'desc')->paginate(5);

    $clientes = \App\Models\Cliente::all();
    $productos = \App\Models\Producto::all();

    // ✅ Obtener empleado logueado
    $empleado = auth()->user()->empleado ?? null;
    $empleadoID = $empleado->EmpleadoID ?? null;
    $empleadoNombre = $empleado?->persona?->NombreCompleto ?? 'Empleado no disponible';

    return view('pedidos.index', compact('pedidos', 'clientes', 'productos', 'empleadoID', 'empleadoNombre'));
}



    public function create()
    {
        $clientes = \App\Models\Cliente::all();
        $empleados = \App\Models\Empleado::with('persona')->get();
        $productos = \App\Models\Producto::all();

        return view('pedidos.create', compact('clientes', 'empleados', 'productos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ClienteID' => 'required|integer|exists:cliente,ClienteID',
            'EmpleadoID' => 'required|integer|exists:empleado,EmpleadoID',
            'FechaPedido' => 'required|date',
            'FechaEntrega' => 'required|date|after_or_equal:FechaPedido',
            'Estado' => 'required|string|max:255',
            'ProductoID' => 'required|integer|exists:producto,ProductoID',
            'Cantidad' => 'required|integer|min:1',
            'PrecioUnitario' => 'required|numeric|min:0',
        ]);

        $pedido = Pedido::create([
            'ClienteID' => $request->ClienteID,
            'EmpleadoID' => $request->EmpleadoID,
            'FechaPedido' => $request->FechaPedido,
            'FechaEntrega' => $request->FechaEntrega,
            'Estado' => $request->Estado,
        ]);

        // Crea el detalle del pedido
        $detalle = $pedido->detalles()->create([
            'ProductoID' => $request->ProductoID,
            'Cantidad' => $request->Cantidad,
            'PrecioUnitario' => $request->PrecioUnitario,
            'Subtotal' => $request->Cantidad * $request->PrecioUnitario,
        ]);

        BitacoraHelper::registrar(
            'CREAR',
            'pedido',
            'Se creó un nuevo pedido ID: ' . $pedido->PedidoID,
            null,
            [
                'pedido' => $pedido,
                'detalle' => $detalle
            ],
            'Módulo de Pedidos'
        );

        return redirect()->route('pedidos.index')->with('success', 'Pedido creado correctamente.');
    }

    public function edit($id)
    {
        $pedido = Pedido::with('detalles')->findOrFail($id);
        $clientes = \App\Models\Cliente::all();
        $empleados = \App\Models\Empleado::with('persona')->get();
        $productos = \App\Models\Producto::all();

        return view('pedidos.edit', compact('pedido', 'clientes', 'empleados', 'productos'));
    }

    public function update(Request $request, $id)
    {
        $pedido = Pedido::findOrFail($id);

        $request->validate([
            'ClienteID' => 'required|integer',
            'EmpleadoID' => 'required|integer',
            'FechaPedido' => 'required|date',
            'FechaEntrega' => 'nullable|date',
            'Estado' => 'required|in:Pendiente,Enviado,Entregado,Cancelado',
            'detalles' => 'required|array|min:1',
            'detalles.*.ProductoID' => 'required|integer',
            'detalles.*.Cantidad' => 'required|integer|min:1',
            'detalles.*.PrecioUnitario' => 'required|numeric|min:0',
        ]);

        // Guarda estado anterior para bitácora
        $pedidoAnterior = $pedido->replicate();

        $pedido->ClienteID = $request->ClienteID;
        $pedido->EmpleadoID = $request->EmpleadoID;
        $pedido->FechaPedido = $request->FechaPedido;
        $pedido->FechaEntrega = $request->FechaEntrega;
        $pedido->Estado = $request->Estado;
        $pedido->save();

        // Elimina detalles antiguos
        $pedido->detalles()->delete();

        // Crea los detalles nuevos
        foreach ($request->detalles as $detalleData) {
            $pedido->detalles()->create([
                'ProductoID' => $detalleData['ProductoID'],
                'Cantidad' => $detalleData['Cantidad'],
                'PrecioUnitario' => $detalleData['PrecioUnitario'],
                'Subtotal' => $detalleData['Cantidad'] * $detalleData['PrecioUnitario'],
            ]);
        }

        BitacoraHelper::registrar(
            'ACTUALIZAR',
            'pedido',
            'Se actualizó el pedido ID: ' . $pedido->PedidoID,
            $pedidoAnterior,
            $request->all(),
            'Módulo de Pedidos'
        );

        return redirect()->route('pedidos.index')->with('success', 'Pedido actualizado correctamente.');
    }

    public function destroy($id)
    {
        $pedido = Pedido::findOrFail($id);

        // Guarda datos para bitácora antes de borrar
        $datosEliminados = $pedido->toArray();

        // Primero elimina los detalles relacionados
        $pedido->detalles()->delete();

        // Luego elimina el pedido
        $pedido->delete();

        BitacoraHelper::registrar(
            'ELIMINAR',
            'pedido',
            'Se eliminó el pedido ID: ' . $id,
            $datosEliminados,
            null,
            'Módulo de Pedidos'
        );

        return redirect()->route('pedidos.index')->with('success', 'Pedido eliminado correctamente.');
    }

    public function exportarPDF(Request $request)
    {
        $query = Pedido::with(['cliente', 'empleado.persona', 'productos', 'detalles']);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where('FechaPedido', 'LIKE', "%{$search}%")
                ->orWhere('FechaEntrega', 'LIKE', "%{$search}%")
                ->orWhere('Estado', 'LIKE', "%{$search}%")
                ->orWhereHas('cliente', fn($q) => $q->where('NombreCliente', 'LIKE', "%{$search}%"))
                ->orWhereHas('empleado.persona', fn($q) =>
                    $q->where('Nombre', 'LIKE', "%{$search}%")
                      ->orWhere('Apellido', 'LIKE', "%{$search}%"))
                ->orWhereHas('productos', fn($q) => $q->where('NombreProducto', 'LIKE', "%{$search}%"))
                ->orWhereHas('detalles', fn($q) =>
                    $q->where('Cantidad', 'LIKE', "%{$search}%")
                      ->orWhere('PrecioUnitario', 'LIKE', "%{$search}%")
                      ->orWhere('Subtotal', 'LIKE', "%{$search}%"));
        }

        $pedidos = $query->get();

        $pdf = Pdf::loadView('pedidos.pdf', compact('pedidos'))->setPaper('a4', 'landscape');
        return $pdf->download('pedidos.pdf');
    }
}
