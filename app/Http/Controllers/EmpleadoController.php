<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Empleado;
use App\Models\Persona;
use App\Helpers\PermisosHelper;
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
            ->when($search, function ($query) use ($search) {
                $query->whereHas('persona', function ($q) use ($search) {
                    $q->where('Nombre', 'like', "%{$search}%")
                      ->orWhere('Apellido', 'like', "%{$search}%");
                });
            })
            ->orderBy('EmpleadoID', 'desc')
            ->paginate(5);

        $personas = Persona::orderBy('Nombre')->get();

        return view('empleados.index', compact('empleados', 'personas'));
    }

    public function store(Request $request)
    {
        $clean = function ($v) {
            if ($v === null) return $v;
            $v = trim($v);
            return preg_replace('/\s+/', ' ', $v);
        };

        $request->merge([
            'Departamento' => $clean($request->input('Departamento')),
            'Cargo'        => $clean($request->input('Cargo')),
        ]);

        $data = $request->validate([
            'PersonaID'         => ['required', 'integer', 'exists:persona,PersonaID', 'unique:empleado,PersonaID'],
            'Departamento'      => ['required', 'string', 'max:100', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúñÑ ]{2,100}$/u'],
            'Cargo'             => ['required', 'string', 'max:100', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúñÑ ]{2,100}$/u'],
            'FechaContratacion' => ['required', 'date'],
            'Salario'           => ['required', 'numeric', 'min:0'],
        ], [
            'PersonaID.required'    => 'Debe seleccionar una persona válida.',
            'PersonaID.integer'     => 'PersonaID debe ser un número entero válido.',
            'PersonaID.exists'      => 'La persona seleccionada no existe en el sistema.',
            'PersonaID.unique'      => 'La persona ya está registrada como empleado.',

            'Departamento.required' => 'El departamento es obligatorio.',
            'Departamento.regex'    => 'El departamento solo debe contener letras y espacios.',
            'Departamento.max'      => 'El departamento no puede tener más de 100 caracteres.',

            'Cargo.required'        => 'El cargo es obligatorio.',
            'Cargo.regex'           => 'El cargo solo debe contener letras y espacios.',
            'Cargo.max'             => 'El cargo no puede tener más de 100 caracteres.',

            'FechaContratacion.required' => 'La fecha de contratación es obligatoria.',
            'FechaContratacion.date'     => 'La fecha de contratación debe ser una fecha válida.',

            'Salario.required'      => 'El salario es obligatorio.',
            'Salario.numeric'       => 'El salario debe ser un número válido.',
            'Salario.min'           => 'El salario no puede ser menor que 0.',
        ]);

        DB::beginTransaction();
        try {
            $empleado = Empleado::create($data);

            BitacoraHelper::registrar(
                'CREAR',
                'empleado',
                'Se registró un nuevo empleado ID: ' . $empleado->EmpleadoID,
                null,
                $empleado->toArray(),
                'Módulo de Empleados'
            );

            DB::commit();

            return redirect()
                ->route('empleados.index')
                ->with('success', 'Empleado registrado con éxito');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return back()
                ->withInput()
                ->with('error', 'Ocurrió un error al registrar el empleado.');
        }
    }

    public function update(Request $request, $id)
    {
        $empleado = Empleado::findOrFail($id);

        $clean = function ($v) {
            if ($v === null) return $v;
            $v = trim($v);
            return preg_replace('/\s+/', ' ', $v);
        };

        $request->merge([
            'Departamento' => $clean($request->input('Departamento')),
            'Cargo'        => $clean($request->input('Cargo')),
        ]);

        $data = $request->validate([
            'PersonaID'         => ['required', 'integer', 'exists:persona,PersonaID', 'unique:empleado,PersonaID,' . $empleado->EmpleadoID . ',EmpleadoID'],
            'Departamento'      => ['required', 'string', 'max:100', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúñÑ ]{2,100}$/u'],
            'Cargo'             => ['required', 'string', 'max:100', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúñÑ ]{2,100}$/u'],
            'FechaContratacion' => ['required', 'date'],
            'Salario'           => ['required', 'numeric', 'min:0'],
        ], [
            'PersonaID.required'    => 'Debe seleccionar una persona válida.',
            'PersonaID.integer'     => 'PersonaID debe ser un número entero válido.',
            'PersonaID.exists'      => 'La persona seleccionada no existe en el sistema.',
            'PersonaID.unique'      => 'La persona ya está registrada como empleado.',

            'Departamento.required' => 'El departamento es obligatorio.',
            'Departamento.regex'    => 'El departamento solo debe contener letras y espacios.',
            'Departamento.max'      => 'El departamento no puede tener más de 100 caracteres.',

            'Cargo.required'        => 'El cargo es obligatorio.',
            'Cargo.regex'           => 'El cargo solo debe contener letras y espacios.',
            'Cargo.max'             => 'El cargo no puede tener más de 100 caracteres.',

            'FechaContratacion.required' => 'La fecha de contratación es obligatoria.',
            'FechaContratacion.date'     => 'La fecha de contratación debe ser una fecha válida.',

            'Salario.required'      => 'El salario es obligatorio.',
            'Salario.numeric'       => 'El salario debe ser un número válido.',
            'Salario.min'           => 'El salario no puede ser menor que 0.',
        ]);

        $antes = $empleado->toArray();

        DB::beginTransaction();
        try {
            $empleado->update($data);

            BitacoraHelper::registrar(
                'ACTUALIZAR',
                'empleado',
                'Se actualizó el empleado ID: ' . $empleado->EmpleadoID,
                $antes,
                $empleado->toArray(),
                'Módulo de Empleados'
            );

            DB::commit();

            return redirect()
                ->route('empleados.index')
                ->with('success', 'Empleado actualizado con éxito');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return back()
                ->withInput()
                ->with('error', 'Ocurrió un error al actualizar el empleado.');
        }
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

            return redirect()
                ->route('empleados.index')
                ->with('success', 'Empleado eliminado con éxito');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()
                ->route('empleados.index')
                ->with('error', 'No se puede eliminar el empleado porque tiene registros asociados.');
        }
    }

    public function exportarPDF(Request $request)
    {
        $search = $request->input('search');

        $empleados = Empleado::with('persona')
            ->when($search, function ($query) use ($search) {
                $query->whereHas('persona', function ($q) use ($search) {
                    $q->where('Nombre', 'like', "%{$search}%")
                      ->orWhere('Apellido', 'like', "%{$search}%");
                });
            })
            ->orderBy('EmpleadoID', 'desc')
            ->get();

        $pdf = PDF::loadView('empleados.pdf', compact('empleados'));

        return $pdf->download('empleados.pdf');
    }
}