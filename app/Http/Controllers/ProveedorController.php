<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proveedor;
use App\Models\Persona;
use App\Models\Empresa;
use App\Helpers\PermisosHelper;
use PDF;
use App\Helpers\BitacoraHelper;

class ProveedorController extends Controller
{
    public function index(Request $request)
{
    $search = $request->input('search');

    $query = Proveedor::with(['persona', 'empresa'])
        ->where(function ($query) use ($search) {
            $query->whereHas('persona', function ($q) use ($search) {
                $q->whereRaw("CONCAT(Nombre, ' ', Apellido) LIKE ?", ["%{$search}%"]);
            })
            ->orWhere('RTN', 'like', "%{$search}%")
            ->orWhere('Descripcion', 'like', "%{$search}%");
        })
        ->orderByDesc('ProveedorID'); // 👈 orden descendente por ID

    $proveedores = $query->paginate(5);

    // Necesitamos estas variables para los selects en los modales
    $personas = Persona::orderBy('Nombre')->get();
    $empresas = Empresa::orderBy('NombreEmpresa')->get();

    return view('proveedores.index', compact('proveedores', 'personas', 'empresas'));
}


    public function store(Request $request)
    {
        if (!PermisosHelper::tienePermiso('Proveedores', 'crear')) {
            abort(403);
        }

        $request->validate([
            'PersonaID' => 'required|integer|exists:persona,PersonaID',
            'EmpresaID' => 'nullable|integer|exists:empresa,EmpresaID',
            'RTN' => 'nullable|string|max:50',
            'Descripcion' => 'nullable|string',
            'URL_Website' => 'nullable|url|max:255',
            'Ubicacion' => 'nullable|string|max:255',
            'Telefono' => 'nullable|string|max:50',
            'CorreoElectronico' => 'nullable|email|max:255',
            'TipoProveedor' => 'required|in:Local,Internacional',
            'FechaRegistro' => 'required|date',
            'Estado' => 'required|in:Activo,Inactivo',
            'Notas' => 'nullable|string',
        ]);

        $proveedor = Proveedor::create($request->all());

        BitacoraHelper::registrar(
            'CREAR',
            'proveedor',
            'Se registró un nuevo proveedor ID: ' . $proveedor->ProveedorID,
            null,
            $proveedor->toArray(),
            'Módulo de Proveedores'
        );

        return redirect()->route('proveedores.index')->with('success', 'Proveedor registrado correctamente.');
    }

    public function update(Request $request, $id)
    {
        if (!PermisosHelper::tienePermiso('Proveedores', 'editar')) {
            abort(403);
        }

        $request->validate([
            'PersonaID' => 'required|integer|exists:persona,PersonaID',
            'EmpresaID' => 'nullable|integer|exists:empresa,EmpresaID',
            'RTN' => 'nullable|string|max:50',
            'Descripcion' => 'nullable|string',
            'URL_Website' => 'nullable|url|max:255',
            'Ubicacion' => 'nullable|string|max:255',
            'Telefono' => 'nullable|string|max:50',
            'CorreoElectronico' => 'nullable|email|max:255',
            'TipoProveedor' => 'required|in:Local,Internacional',
            'FechaRegistro' => 'required|date',
            'Estado' => 'required|in:Activo,Inactivo',
            'Notas' => 'nullable|string',
        ]);

        $proveedor = Proveedor::findOrFail($id);
        $anterior = $proveedor->toArray();

        $proveedor->update($request->all());

        BitacoraHelper::registrar(
            'ACTUALIZAR',
            'proveedor',
            'Se actualizó el proveedor ID: ' . $id,
            $anterior,
            $proveedor->toArray(),
            'Módulo de Proveedores'
        );

        return redirect()->route('proveedores.index')->with('success', 'Proveedor actualizado correctamente.');
    }

    public function destroy($id)
    {
        if (!PermisosHelper::tienePermiso('Proveedores', 'eliminar')) {
            abort(403);
        }

        $proveedor = Proveedor::findOrFail($id);

        if (method_exists($proveedor, 'compras') && $proveedor->compras()->exists()) {
            return redirect()->route('proveedores.index')->with('error', 'No se puede eliminar el proveedor porque tiene compras asociadas.');
        }

        $anterior = $proveedor->toArray();
        $proveedor->delete();

        BitacoraHelper::registrar(
            'ELIMINAR',
            'proveedor',
            'Se eliminó el proveedor ID: ' . $id,
            $anterior,
            null,
            'Módulo de Proveedores'
        );

        return redirect()->route('proveedores.index')->with('success', 'Proveedor eliminado correctamente.');
    }

    public function exportarPDF(Request $request)
    {
        $search = $request->input('search');

        $proveedores = Proveedor::with(['persona', 'empresa'])
            ->where(function ($query) use ($search) {
                $query->whereHas('persona', function ($q) use ($search) {
                    $q->whereRaw("CONCAT(Nombre, ' ', Apellido) LIKE ?", ["%{$search}%"]);
                })
                ->orWhere('RTN', 'like', "%{$search}%")
                ->orWhere('Descripcion', 'like', "%{$search}%");
            })
            ->get();

        $pdf = PDF::loadView('proveedores.pdf', compact('proveedores'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('proveedores.pdf');
    }
}