<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bitacora;
use App\Helpers\PermisosHelper;

class BitacoraController extends Controller
{
    public function index()
{
    if (!PermisosHelper::tienePermiso('Bitacora', 'ver')) {
        abort(403, 'No tienes permiso para ver esta sección.');
    }

    // Ordenar por ID descendente (más nuevos primero)
    $bitacoras = Bitacora::orderBy('BitacoraID', 'desc')->paginate(20);

    return view('bitacoras.index', compact('bitacoras'));
}


    public function create()
    {
        if (!PermisosHelper::tienePermiso('Bitacora', 'crear')) {
            abort(403);
        }

        return view('bitacoras.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'UsuarioID' => 'required|integer',
            'Accion' => 'required|string|max:100',
            'TablaAfectada' => 'required|string|max:100',
            'FechaAccion' => 'nullable|date',
            'Descripcion' => 'required|string',
            'DatosPrevios' => 'nullable|string',
            'DatosNuevos' => 'nullable|string',
            'Modulo' => 'nullable|string|max:50',
            'Resultado' => 'nullable|string|max:50',
        ]);

        Bitacora::create($request->all());

        return redirect()->route('bitacoras.index')->with('success', 'Bitácora registrada correctamente.');
    }

    public function edit($id)
    {
        if (!PermisosHelper::tienePermiso('Bitacora', 'editar')) {
            abort(403);
        }

        $bitacora = Bitacora::findOrFail($id);
        return view('bitacoras.edit', compact('bitacora'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'UsuarioID' => 'required|integer',
            'Accion' => 'required|string|max:100',
            'TablaAfectada' => 'required|string|max:100',
            'FechaAccion' => 'nullable|date',
            'Descripcion' => 'required|string',
            'DatosPrevios' => 'nullable|string',
            'DatosNuevos' => 'nullable|string',
            'Modulo' => 'nullable|string|max:50',
            'Resultado' => 'nullable|string|max:50',
        ]);

        $bitacora = Bitacora::findOrFail($id);
        $bitacora->update($request->all());

        return redirect()->route('bitacoras.index')->with('success', 'Bitácora actualizada correctamente.');
    }

    public function destroy($id)
    {
        if (!PermisosHelper::tienePermiso('Bitacora', 'eliminar')) {
            abort(403);
        }

        Bitacora::findOrFail($id)->delete();

        return redirect()->route('bitacoras.index')->with('success', 'Bitácora eliminada correctamente.');
    }
}