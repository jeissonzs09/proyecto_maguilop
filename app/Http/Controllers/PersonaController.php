<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            ->when($search, function ($query) use ($search) {
                // Agrupar condiciones de búsqueda
                $query->where(function ($q) use ($search) {
                    $q->where('Nombre', 'like', "%{$search}%")
                      ->orWhere('Apellido', 'like', "%{$search}%");
                });
            })
            ->orderBy('PersonaID', 'desc')
            ->paginate(10);

        return view('persona.index', compact('personas'));
    }

    public function store(Request $request)
    {
        // ---- Limpieza previa ----
        $clean = function ($v) {
            if ($v === null) return $v;
            $v = trim($v);
            return preg_replace('/\s+/', ' ', $v);
        };

        // Normalizar entradas principales
        $request->merge([
            'Nombre'            => $clean($request->input('Nombre')),
            'Apellido'          => $clean($request->input('Apellido')),
            'CorreoElectronico' => $clean($request->input('CorreoElectronico')),
        ]);

        // Limpiar teléfonos (solo dígitos, máx 8)
        $telefonos = $request->input('telefonos', []);
        foreach ($telefonos as $i => $tel) {
            $numeroLimpio = isset($tel['Numero']) ? preg_replace('/\D+/', '', $tel['Numero']) : '';
            $telefonos[$i]['Numero'] = mb_substr($numeroLimpio, 0, 8);
            $telefonos[$i]['Tipo']   = isset($tel['Tipo']) ? $clean($tel['Tipo']) : null;
        }
        $request->merge(['telefonos' => $telefonos]);

        // ---- Validación ----
        $data = $request->validate([
            'Nombre' => [
                'required', 'string', 'max:255',
                // Solo letras A-Z y espacios (sin acentos/símbolos/números)
                'regex:/^[A-Za-z]+(?:\s[A-Za-z]+)*$/',
            ],
            'Apellido' => [
                'required', 'string', 'max:255',
                'regex:/^[A-Za-z]+(?:\s[A-Za-z]+)*$/',
            ],
            'FechaNacimiento'  => ['required', 'date'],
            'Genero'           => ['required', 'in:F,M'],
            'CorreoElectronico'=> [
                'required', 'email', 'max:255',
                'unique:persona,CorreoElectronico',
            ],
            'telefonos' => ['required', 'array', 'min:1'],
            'telefonos.*.Tipo' => [
                'required', 'string', 'max:50',
                'regex:/^(Personal|Trabajo|Otro)$/i',
            ],
            'telefonos.*.Numero' => [
                'required', 'regex:/^[0-9]{8}$/',
            ],
        ], [
            'Nombre.regex'   => 'El nombre solo debe contener letras (A-Z) y espacios.',
            'Apellido.regex' => 'El apellido solo debe contener letras (A-Z) y espacios.',
            'CorreoElectronico.required' => 'El correo es obligatorio.',
            'CorreoElectronico.email'    => 'Ingresa un correo válido que lleve @ y dominio.',
            'CorreoElectronico.unique'   => 'Este correo ya está registrado.',
            'telefonos.required'         => 'Agrega al menos un teléfono.',
            'telefonos.*.Numero.regex'   => 'Cada teléfono debe tener exactamente 8 dígitos.',
            'telefonos.*.Tipo.regex'     => 'El tipo de teléfono debe ser Personal, Trabajo u Otro.',
        ]);

        // ---- Persistencia con transacción ----
        DB::beginTransaction();
        try {
            $persona = Persona::create([
                'Nombre'            => $data['Nombre'],
                'Apellido'          => $data['Apellido'],
                'FechaNacimiento'   => $data['FechaNacimiento'],
                'Genero'            => $data['Genero'],
                'CorreoElectronico' => $data['CorreoElectronico'],
            ]);

            foreach ($data['telefonos'] as $tel) {
                $persona->telefonos()->create([
                    'Tipo'   => ucfirst(strtolower($tel['Tipo'])),
                    'Numero' => $tel['Numero'],
                ]);
            }

            BitacoraHelper::registrar(
                'CREAR',
                'persona',
                'Se registró una nueva persona ID: ' . $persona->PersonaID,
                null,
                $persona->toArray(),
                'Módulo de Personas'
            );

            DB::commit();

            return redirect()
                ->route('persona.index')
                ->with('success', 'Persona registrada con éxito');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return back()
                ->withInput()
                ->with('error', 'Ocurrió un error al registrar la persona.');
        }
    }

    public function update(Request $request, $id)
    {
        $persona = Persona::findOrFail($id);

        // ---- Limpieza previa ----
        $clean = function ($v) {
            if ($v === null) return $v;
            $v = trim($v);
            return preg_replace('/\s+/', ' ', $v);
        };

        $request->merge([
            'Nombre'            => $clean($request->input('Nombre')),
            'Apellido'          => $clean($request->input('Apellido')),
            'CorreoElectronico' => $clean($request->input('CorreoElectronico')),
        ]);

        $telefonos = $request->input('telefonos', []);
        foreach ($telefonos as $i => $tel) {
            $numeroLimpio = isset($tel['Numero']) ? preg_replace('/\D+/', '', $tel['Numero']) : '';
            $telefonos[$i]['Numero'] = mb_substr($numeroLimpio, 0, 8);
            $telefonos[$i]['Tipo']   = isset($tel['Tipo']) ? $clean($tel['Tipo']) : null;
        }
        $request->merge(['telefonos' => $telefonos]);

        // ---- Validación ----
        $data = $request->validate([
            'Nombre' => [
                'required', 'string', 'max:255',
                'regex:/^[A-Za-z]+(?:\s[A-Za-z]+)*$/',
            ],
            'Apellido' => [
                'required', 'string', 'max:255',
                'regex:/^[A-Za-z]+(?:\s[A-Za-z]+)*$/',
            ],
            'FechaNacimiento'   => ['required', 'date'],
            'Genero'            => ['required', 'in:F,M'],
            'CorreoElectronico' => [
                'required', 'email', 'max:255',
                'unique:persona,CorreoElectronico,' . $persona->PersonaID . ',PersonaID',
            ],
            'telefonos' => ['required', 'array', 'min:1'],
            'telefonos.*.Tipo' => [
                'required', 'string', 'max:50',
                'regex:/^(Personal|Trabajo|Otro)$/i',
            ],
            'telefonos.*.Numero' => [
                'required', 'regex:/^[0-9]{8}$/',
            ],
        ], [
            'Nombre.regex'   => 'El nombre solo debe contener letras (A-Z) y espacios.',
            'Apellido.regex' => 'El apellido solo debe contener letras (A-Z) y espacios.',
            'CorreoElectronico.required' => 'El correo es obligatorio.',
            'CorreoElectronico.email'    => 'Ingresa un correo válido que lleve @ y dominio.',
            'CorreoElectronico.unique'   => 'Este correo ya está registrado.',
            'telefonos.required'         => 'Agrega al menos un teléfono.',
            'telefonos.*.Numero.regex'   => 'Cada teléfono debe tener exactamente 8 dígitos.',
            'telefonos.*.Tipo.regex'     => 'El tipo de teléfono debe ser Personal, Trabajo u Otro.',
        ]);

        $antes = $persona->toArray();

        DB::beginTransaction();
        try {
            $persona->update([
                'Nombre'            => $data['Nombre'],
                'Apellido'          => $data['Apellido'],
                'FechaNacimiento'   => $data['FechaNacimiento'],
                'Genero'            => $data['Genero'],
                'CorreoElectronico' => $data['CorreoElectronico'],
            ]);

            // Estrategia simple: borrar y re-crear teléfonos
            $persona->telefonos()->delete();
            foreach ($data['telefonos'] as $tel) {
                $persona->telefonos()->create([
                    'Tipo'   => ucfirst(strtolower($tel['Tipo'])),
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

            DB::commit();

            return redirect()
                ->route('persona.index')
                ->with('success', 'Persona actualizada con éxito');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return back()
                ->withInput()
                ->with('error', 'Ocurrió un error al actualizar la persona.');
        }
    }

    public function exportarPDF(Request $request)
    {
        $search = $request->input('search');

        $personas = Persona::with('telefonos')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('Nombre', 'like', "%{$search}%")
                      ->orWhere('Apellido', 'like', "%{$search}%");
                });
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

            return redirect()
                ->route('persona.index')
                ->with('error', 'Persona eliminada con éxito');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()
                ->route('persona.index')
                ->with('error', 'No se puede eliminar la persona porque tiene registros asociados.');
        }
    }
}