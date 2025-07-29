<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

use App\Models\Persona;
use App\Models\Telefono;
use App\Helpers\PermisosHelper;
use App\Helpers\BitacoraHelper;
use PDF;

class PersonaController extends Controller
{
    public function index(Request $request)
    {
        if (!PermisosHelper::tienePermiso('Personas', 'ver')) {
            abort(403, 'No tienes permiso para ver esta sección.');
        }

        $search = $request->input('search');

        $personas = Persona::with('telefonos')
            ->when($search, function ($query, $search) {
                $query->where('Nombre', 'like', "%$search%")
                      ->orWhere('Apellido', 'like', "%$search%");
            })
            ->orderBy('PersonaID', 'desc')
            ->paginate(5);

        return view('persona.index', compact('personas'));
    }

   

public function store(Request $request)
{
    $data = $request->validate([
        'Nombre' => [
            'required',
            'string',
            'max:255',
            'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ]+(?:\s[A-Za-zÁÉÍÓÚáéíóúÑñ]+)*$/u',
        ],
        'Apellido' => [
            'required',
            'string',
            'max:255',
            'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ]+(?:\s[A-Za-zÁÉÍÓÚáéíóúÑñ]+)*$/u',
        ],
        'FechaNacimiento' => ['required', 'date'],
        'Genero' => ['required', 'in:F,M'],
        'CorreoElectronico' => [
            'nullable',
            'email',
            'max:255',
            'unique:persona,CorreoElectronico'
        ],
        'telefonos.*.Tipo' => [
            'required',
            'string',
            'max:50',
            'regex:/^(Personal|Trabajo|Otro)$/i'
        ],
        'telefonos.*.Numero' => [
            'required',
            'regex:/^[0-9]{8}$/',
        ],
    ], [
        'Nombre.regex' => 'El nombre solo debe contener letras y espacios, sin números ni símbolos.',
        'Apellido.regex' => 'El apellido solo debe contener letras y espacios, sin números ni símbolos.',
        'telefonos.*.Numero.regex' => 'El número debe tener exactamente 8 dígitos numéricos.',
        'telefonos.*.Tipo.regex' => 'El tipo de teléfono debe ser Personal, Trabajo u Otro.',
    ]);

    $persona = Persona::create($data);

    // Guardar teléfonos
    if ($request->has('telefonos')) {
        foreach ($request->input('telefonos') as $tel) {
            $persona->telefonos()->create([
                'Tipo' => $tel['Tipo'],
                'Numero' => $tel['Numero'],
            ]);
        }
    }

    BitacoraHelper::registrar(
        'CREAR',
        'persona',
        'Se registró una nueva persona ID: ' . $persona->PersonaID,
        null,
        $persona->toArray(),
        'Módulo de Personas'
    );

    return redirect()
        ->route('persona.index')
        ->with('success', 'Persona registrada correctamente.');
}


    public function update(Request $request, $id)
    {
        $persona = Persona::findOrFail($id);

        $data = $request->validate([
            'Nombre'           => 'required|string|max:255',
            'Apellido'         => 'required|string|max:255',
            'FechaNacimiento'  => 'required|date',
            'Genero'           => 'required|in:F,M',
            'CorreoElectronico'=> 'nullable|email|max:255|unique:persona,CorreoElectronico,' . $persona->PersonaID . ',PersonaID',
            'telefonos.*.Tipo'   => 'required|string|max:50',
            'telefonos.*.Numero' => 'required|string|max:20',
        ]);

        $antes = $persona->toArray();

        $persona->update($data);

        // Actualizar teléfonos:
        $telefonosInput = $request->input('telefonos', []);

        // Obtener ids actuales de teléfonos para persona
        $telefonosActuales = $persona->telefonos()->get();

        // Para simplicidad, eliminamos todos y reinsertamos los enviados:
        $persona->telefonos()->delete();

        foreach ($telefonosInput as $tel) {
            $persona->telefonos()->create([
                'Tipo' => $tel['Tipo'],
                'Numero' => $tel['Numero'],
            ]);
        }

        BitacoraHelper::registrar(
            'ACTUALIZAR',
            'persona',
            'Se actualizó la persona ID: ' . $persona->PersonaID,
            $antes,
            $persona->toArray(),
            'Módulo de Personas'
        );

        return redirect()
            ->route('persona.index')
            ->with('success', 'Persona actualizada correctamente.');
    }

    public function exportarPDF(Request $request)
    {
        $search = $request->input('search');

        $personas = Persona::with('telefonos')
            ->when($search, function ($query, $search) {
                $query->where('Nombre', 'like', "%$search%")
                      ->orWhere('Apellido', 'like', "%$search%");
            })
            ->orderBy('PersonaID', 'desc')
            ->get();

        $pdf = PDF::loadView('persona.pdf', compact('personas'));

        return $pdf->download('personas.pdf');
    }

    public function destroy($id)
    {
        try {
            $persona = Persona::findOrFail($id);
            $anterior = $persona->toArray();
            $persona->delete();

            BitacoraHelper::registrar(
                'ELIMINAR',
                'persona',
                'Se eliminó la persona ID: ' . $persona->PersonaID,
                $anterior,
                null,
                'Módulo de Personas'
            );

            return redirect()->route('persona.index')->with('success', 'Persona eliminada correctamente.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('persona.index')->with('error', 'No se puede eliminar la persona porque tiene registros asociados.');
        }
    }
}