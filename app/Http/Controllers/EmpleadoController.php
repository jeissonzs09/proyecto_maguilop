<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empleado;
use App\Helpers\PermisosHelper;
use App\Models\Persona;
use App\Helpers\BitacoraHelper;
use PDF;

class EmpleadoController extends Controller
{
    public function index(Request $request)
    {
        if (!PermisosHelper::tienePermiso('Empleados', 'ver')) {
            abort(403, 'No tienes permiso para ver esta sección.');
        }

        $search = $request->input('search');

        $empleados = Empleado::with('persona')
            ->when($search, function ($query, $search) {
                $query->whereHas('persona', function ($q) use ($search) {
                    $q->where('Nombre', 'like', "%$search%")
                      ->orWhere('Apellido', 'like', "%$search%");
                });
            })
            ->orderBy('EmpleadoID', 'desc')
            ->paginate(5);

        $personas = Persona::orderBy('Nombre')->get();

        return view('empleados.index', compact('empleados', 'personas'));
    }

    public function store(Request $request)
    {
        $messages = [
            'PersonaID.unique' => 'La persona ya está registrada como empleado.',
        ];

        $data = $request->validate([
            'PersonaID'         => 'required|integer|exists:persona,PersonaID|unique:empleado,PersonaID',
            'Departamento'      => 'required|string|max:255',
            'Cargo'             => 'required|string|max:255',
            'FechaContratacion' => 'required|date',
            'Salario'           => 'required|numeric|min:0',
        ], $messages);

        $empleado = Empleado::create($data);

        BitacoraHelper::registrar(
            'CREAR',
            'empleado',
            'Se registró un nuevo empleado ID: ' . $empleado->EmpleadoID,
            null,
            $empleado->toArray(),
            'Módulo de Empleados'
        );

        return redirect()
            ->route('empleados.index')
            ->with('success', 'Empleado registrado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $empleado = Empleado::findOrFail($id);

        $messages = [
            'PersonaID.unique' => 'La persona ya está registrada como empleado.',
        ];

        // La validación unique ignora el registro actual para PersonaID
        $data = $request->validate([
            'PersonaID'         => 'required|integer|exists:persona,PersonaID|unique:empleado,PersonaID,' . $empleado->EmpleadoID . ',EmpleadoID',
            'Departamento'      => 'required|string|max:255',
            'Cargo'             => 'required|string|max:255',
            'FechaContratacion' => 'required|date',
            'Salario'           => 'required|numeric|min:0',
        ], $messages);

        $antes = $empleado->toArray();

        $empleado->update($data);

        BitacoraHelper::registrar(
            'ACTUALIZAR',
            'empleado',
            'Se actualizó el empleado ID: ' . $empleado->EmpleadoID,
            $antes,
            $empleado->toArray(),
            'Módulo de Empleados'
        );

        return redirect()
            ->route('empleados.index')
            ->with('success', 'Empleado actualizado correctamente.');
    }

    public function exportarPDF(Request $request)
{
    $search = $request->input('search');

    $empleados = Empleado::with('persona')
        ->when($search, function ($query, $search) {
            $query->whereHas('persona', function ($q) use ($search) {
                $q->where('Nombre', 'like', "%$search%")
                  ->orWhere('Apellido', 'like', "%$search%");
            });
        })
        ->orderBy('EmpleadoID', 'desc')
        ->get();

    $pdf = PDF::loadView('empleados.pdf', compact('empleados'));

    return $pdf->download('empleados.pdf');
}
    public function destroy($id)
    {
        try {
            $empleado = Empleado::findOrFail($id);
            $anterior = $empleado->toArray();
            $empleado->delete();

            BitacoraHelper::registrar(
                'ELIMINAR',
                'empleado',
                'Se eliminó el empleado ID: ' . $empleado->EmpleadoID,
                $anterior,
                null,
                'Módulo de Empleados'
            );

            return redirect()->route('empleados.index')->with('success', 'Empleado eliminado correctamente.');
        } catch (\Illuminate\Database\QueryException $e) {
            // Puedes registrar el error aquí si quieres para debugging
            return redirect()->route('empleados.index')->with('error', 'No se puede eliminar el empleado porque tiene registros asociados.');
        }
    }
}
