<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Compra;
use App\Models\Proveedor;
use App\Models\Empleado;
use App\Models\Bitacora;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class CompraController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $compras = Compra::with(['proveedor', 'empleado.persona'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('CompraID', 'like', "%$search%")
                      ->orWhereHas('proveedor', function ($q2) use ($search) {
                          $q2->where('Descripcion', 'like', "%$search%");
                      })
                      ->orWhereHas('empleado.persona', function ($q3) use ($search) {
                          $q3->where('NombreCompleto', 'like', "%$search%");
                      });
                });
            })
            ->orderBy('CompraID', 'desc')
            ->paginate(5);

        $proveedores = Proveedor::orderBy('Descripcion')->get();
        $empleados = Empleado::with('persona')->orderBy('EmpleadoID')->get();

        return view('compras.index', compact('compras', 'proveedores', 'empleados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ProveedorID' => 'required|exists:proveedor,ProveedorID',
            'EmpleadoID' => 'required|exists:empleado,EmpleadoID',
            'FechaCompra' => 'required|date',
            'TotalCompra' => 'required|numeric|min:0.01',
            'Estado' => 'required|in:Recibido,Pendiente,Cancelado',
        ]);

        $compra = Compra::create($request->only([
            'ProveedorID', 'EmpleadoID', 'FechaCompra', 'TotalCompra', 'Estado'
        ]));

        Bitacora::create([
            'UsuarioID' => Auth::id(),
            'Accion' => 'Crear',
            'TablaAfectada' => 'Compra',
            'FechaAccion' => now(),
            'Descripcion' => 'Se registró una nueva compra con ID: ' . $compra->CompraID,
            'Modulo' => 'Compras',
            'Resultado' => 'Éxito',
            'DatosPrevios' => null,
            'DatosNuevos' => json_encode($compra->toArray()),
        ]);

        return redirect()->route('compras.index')->with('success', 'Compra registrada exitosamente.');
    }

    public function update(Request $request, $id)
    {
        $compra = Compra::findOrFail($id);
        $datosPrevios = $compra->toArray();

        $request->validate([
            'ProveedorID' => 'required|exists:proveedor,ProveedorID',
            'EmpleadoID' => 'required|exists:empleado,EmpleadoID',
            'FechaCompra' => 'required|date',
            'TotalCompra' => 'required|numeric|min:0.01',
            'Estado' => 'required|in:Recibido,Pendiente,Cancelado',
        ]);

        $compra->update($request->only([
            'ProveedorID', 'EmpleadoID', 'FechaCompra', 'TotalCompra', 'Estado'
        ]));

        Bitacora::create([
            'UsuarioID' => Auth::id(),
            'Accion' => 'Actualizar',
            'TablaAfectada' => 'Compra',
            'FechaAccion' => now(),
            'Descripcion' => 'Se actualizó la compra con ID: ' . $id,
            'Modulo' => 'Compras',
            'Resultado' => 'Éxito',
            'DatosPrevios' => json_encode($datosPrevios),
            'DatosNuevos' => json_encode($compra->toArray()),
        ]);

        return redirect()->route('compras.index')->with('success', 'Compra actualizada exitosamente.');
    }

    public function destroy($id)
    {
        $compra = Compra::findOrFail($id);
        $datosPrevios = $compra->toArray();

        $compra->delete();

        Bitacora::create([
            'UsuarioID' => Auth::id(),
            'Accion' => 'Eliminar',
            'TablaAfectada' => 'Compra',
            'FechaAccion' => now(),
            'Descripcion' => 'Se eliminó la compra con ID: ' . $id,
            'Modulo' => 'Compras',
            'Resultado' => 'Éxito',
            'DatosPrevios' => json_encode($datosPrevios),
            'DatosNuevos' => null,
        ]);

        return redirect()->route('compras.index')->with('success', 'Compra eliminada exitosamente.');
    }

    public function exportarPDF(Request $request)
    {
        $search = $request->input('search');

        $compras = Compra::with(['proveedor', 'empleado.persona'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('CompraID', 'like', "%$search%")
                      ->orWhereHas('proveedor', function ($q2) use ($search) {
                          $q2->where('Descripcion', 'like', "%$search%");
                      })
                      ->orWhereHas('empleado.persona', function ($q3) use ($search) {
                          $q3->where('NombreCompleto', 'like', "%$search%");
                      });
                });
            })
            ->orderBy('CompraID', 'desc')
            ->get();

        $pdf = Pdf::loadView('compras.pdf', compact('compras', 'search'));

        return $pdf->download('compras_' . now()->format('Ymd_His') . '.pdf');
    }
}
