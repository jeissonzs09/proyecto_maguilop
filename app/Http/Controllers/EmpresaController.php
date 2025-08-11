<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empresa;
use App\Models\Bitacora;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Helpers\PermisosHelper;

class EmpresaController extends Controller
{
    public function index(Request $request)
    {
        $query = Empresa::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('NombreEmpresa', 'LIKE', "%{$search}%")
                  ->orWhere('Telefono', 'LIKE', "%{$search}%")
                  ->orWhere('Website', 'LIKE', "%{$search}%")
                  ->orWhere('Direccion', 'LIKE', "%{$search}%")
                  ->orWhere('Descripcion', 'LIKE', "%{$search}%");
            });
        }

        $empresas = $query->orderBy('EmpresaID', 'desc')->paginate(10);

        return view('empresa.index', compact('empresas'));
    }

    public function exportarPDF(Request $request)
    {
        $query = Empresa::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('NombreEmpresa', 'LIKE', "%{$search}%")
                  ->orWhere('Telefono', 'LIKE', "%{$search}%")
                  ->orWhere('Website', 'LIKE', "%{$search}%")
                  ->orWhere('Direccion', 'LIKE', "%{$search}%")
                  ->orWhere('Descripcion', 'LIKE', "%{$search}%");
            });
        }

        $empresas = $query->orderBy('EmpresaID', 'desc')->get();

        $pdf = Pdf::loadView('empresa.pdf', compact('empresas'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('Listado_de_Empresas.pdf');
    }

    public function store(Request $request)
{
    $data = $request->validate([
        'NombreEmpresa' => 'required|string|max:255',
        'Telefono'      => ['nullable', 'regex:/^[0-9]+$/'], // Solo números, cualquier longitud
        'Website'       => 'nullable|string|max:255',
        'Direccion'     => 'nullable|string|max:500',
        'Descripcion'   => ['nullable', 'string', 'max:1000', 'regex:/^[A-Za-z0-9ÁÉÍÓÚÑáéíóúñ\s\.,\-]+$/u'],
        // letras, números, acentos, espacios, puntos, comas y guiones
    ], [
        'Telefono.regex' => 'El teléfono solo debe contener números.',
        'Descripcion.regex' => 'La descripción solo puede contener letras, números, espacios, puntos, comas y guiones.',
    ]);

    $empresa = Empresa::create($data);

    Bitacora::create([
        'UsuarioID' => Auth::id(),
        'Accion' => 'Crear',
        'TablaAfectada' => 'empresa',
        'FechaAccion' => now(),
        'Descripcion' => 'Empresa creada: ID '.$empresa->EmpresaID.' - Nombre: '.$empresa->NombreEmpresa,
        'DatosPrevios' => null,
        'DatosNuevos' => json_encode($empresa->toArray()),
        'Modulo' => 'Empresas',
        'Resultado' => 'Exitoso',
    ]);

    return redirect()->route('empresa.index')->with('success', 'Empresa registrada correctamente.');
}


    public function update(Request $request, $id)
    {
        $request->validate([
            'NombreEmpresa' => 'required|string|max:255',
            'Telefono'      => 'nullable|string|max:50',
            'Website'       => 'nullable|string|max:255',
            'Direccion'     => 'nullable|string|max:500',
            'Descripcion'   => 'nullable|string',
        ]);

        $empresa = Empresa::findOrFail($id);
        $datosPrevios = $empresa->toArray();

        $empresa->update($request->only(['NombreEmpresa', 'Telefono', 'Website', 'Direccion', 'Descripcion']));

        Bitacora::create([
            'UsuarioID' => Auth::id(),
            'Accion' => 'Actualizar',
            'TablaAfectada' => 'empresa',
            'FechaAccion' => now(),
            'Descripcion' => 'Empresa actualizada: ID '.$empresa->EmpresaID.' - Nombre: '.$empresa->NombreEmpresa,
            'DatosPrevios' => json_encode($datosPrevios),
            'DatosNuevos' => json_encode($empresa->toArray()),
            'Modulo' => 'Empresas',
            'Resultado' => 'Exitoso',
        ]);

        return redirect()->route('empresa.index')->with('success', 'Empresa actualizada correctamente.');
    }

    public function destroy($id)
    {
        if (!PermisosHelper::tienePermiso('Empresas', 'eliminar')) {
            abort(403);
        }

        try {
            $empresa = Empresa::findOrFail($id);
            $datosPrevios = $empresa->toArray();

            if (method_exists($empresa, 'proveedores') && $empresa->proveedores()->exists()) {
                return redirect()->route('empresa.index')->with('error', 'No se puede eliminar la empresa porque tiene proveedores vinculados.');
            }

            $empresa->delete();

            Bitacora::create([
                'UsuarioID' => Auth::id(),
                'Accion' => 'Eliminar',
                'TablaAfectada' => 'empresa',
                'FechaAccion' => now(),
                'Descripcion' => 'Empresa eliminada: ID '.$id.' - Nombre: '.$datosPrevios['NombreEmpresa'],
                'DatosPrevios' => json_encode($datosPrevios),
                'DatosNuevos' => null,
                'Modulo' => 'Empresas',
                'Resultado' => 'Exitoso',
            ]);

            return redirect()->route('empresa.index')->with('success', 'Empresa eliminada correctamente.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('empresa.index')->with('error', 'No se puede eliminar la empresa porque tiene registros asociados.');
        }
    }

    public function show($id)
    {
        if (!PermisosHelper::tienePermiso('Empresas', 'ver')) {
            abort(403, 'No tienes permiso para ver esta empresa.');
        }

        $empresa = Empresa::findOrFail($id);

        return view('empresa.show', compact('empresa'));
    }
}