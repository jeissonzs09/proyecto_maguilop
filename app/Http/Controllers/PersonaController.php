<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

use App\Models\Persona;
use App\Models\Telefono;
use App\Models\Configuracion;
use App\Helpers\PermisosHelper;
use App\Helpers\BitacoraHelper;
use PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PersonasExport;

class PersonaController extends Controller
{
    private $config;
    private $bitacoraActiva;

    public function __construct()
{
    // Cargar parámetros del módulo personas
    $config = Configuracion::where('modulo', 'personas')->first();

    if ($config) {
        $this->config = $config->toArray(); // todos los parámetros disponibles
        $this->bitacoraActiva = (int)$config->bitacora_activa === 1; // TRUE si 1, FALSE si 0
    } else {
        $this->config = [];
        $this->bitacoraActiva = false; // por defecto inactiva si no existe registro
    }
}



    public function index(Request $request)
    {
        if (!PermisosHelper::tienePermiso('Persona', 'ver')) {
            abort(403, 'No tienes permiso para ver esta sección.');
        }

        $search = $request->input('search');
$perPage = $request->input('perPage', 10); // definir antes de la consulta


        $personas = Persona::with('telefonos')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('Nombre', 'like', "%{$search}%")
                      ->orWhere('Apellido', 'like', "%{$search}%");
                });
            })
     
    ->orderBy('PersonaID', 'desc')
    ->paginate($perPage);


        return view('persona.index', compact('personas'));
    }

    private function cleanInput($v)
    {
        if ($v === null) return $v;
        $v = trim($v);
        return preg_replace('/\s+/', ' ', $v);
    }

    private function validarTelefonosUnicos($telefonos, $personaId = null)
{
    $mensaje = $this->config['mensaje_telefono_unico'] ?? 'El número de teléfono ya existe en otro registro.';
    $errores = [];

    foreach ($telefonos as $index => $tel) {
        if (empty($tel['Numero'])) continue; // ignorar vacíos
        $query = Telefono::where('Numero', $tel['Numero']);
        if ($personaId) {
            $query->where('PersonaID', '!=', $personaId);
        }
        if ($query->exists()) {
            $errores[$index] = $mensaje;
        }
    }

    return !empty($errores) ? $errores : null;
}


    public function store(Request $request)
    {
        $request->merge([
            'Nombre' => $this->cleanInput($request->input('Nombre')),
            'Apellido' => $this->cleanInput($request->input('Apellido')),
            'email' => $this->cleanInput($request->input('email')),
        ]);

        $telefonos = $request->input('telefonos', []);
        foreach ($telefonos as $i => $tel) {
            $numeroLimpio = isset($tel['Numero']) ? preg_replace('/\D+/', '', $tel['Numero']) : '';
            $telefonos[$i]['Numero'] = mb_substr($numeroLimpio, 0, 8);
            $telefonos[$i]['Tipo'] = isset($tel['Tipo']) ? $this->cleanInput($tel['Tipo']) : null;
        }
        $request->merge(['telefonos' => $telefonos]);

        $data = $request->validate([
            'Nombre' => [
        'required',
        'string',
        'max:255',
        'regex:/^[A-Za-z]+(?:\s[A-Za-z]+)*$/',
        function ($attribute, $value, $fail) use ($request) {
            $exists = \App\Models\Persona::whereRaw('LOWER(Nombre) = ?', [strtolower($value)])
                ->whereRaw('LOWER(Apellido) = ?', [strtolower($request->Apellido)])
                ->exists();

            if ($exists) {
                $fail('Ya existe una persona registrada con ese mismo nombre y apellido.');
            }
        },
    ],
            'Apellido' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z]+(?:\s[A-Za-z]+)*$/'],
            'FechaNacimiento' => ['required', 'date'],
            'Genero' => ['required', 'in:F,M'],
            'email' => ['required', 'email', 'max:255', 'unique:persona,email'],
            'telefonos' => ['required', 'array', 'min:1'],
            'telefonos.*.Tipo' => ['required', 'string', 'max:50', 'regex:/^(Personal|Trabajo|Otro)$/i'],
            'telefonos.*.Numero' => ['required', 'regex:/^[0-9]{8}$/'],
        ], [
            'Nombre.regex' => $this->config['mensaje_nombre_regex'] ?? 'El nombre solo debe contener letras (A-Z) y espacios.',
            'Apellido.regex' => $this->config['mensaje_apellido_regex'] ?? 'El apellido solo debe contener letras (A-Z) y espacios.',
            'email.required' => $this->config['mensaje_email_requerido'] ?? 'El correo es obligatorio.',
            'email.email' => $this->config['mensaje_email_invalido'] ?? 'Ingresa un correo válido que lleve @ y dominio.',
            'email.unique' => $this->config['mensaje_email_unico'] ?? 'Este correo ya está registrado.',
            'telefonos.required' => $this->config['mensaje_telefonos_requerido'] ?? 'Agrega al menos un teléfono.',
            'telefonos.*.Numero.regex' => $this->config['mensaje_telefono_formato'] ?? 'Cada teléfono debe tener exactamente 8 dígitos.',
            'telefonos.*.Tipo.regex' => $this->config['mensaje_telefono_tipo'] ?? 'El tipo de teléfono debe ser Personal, Trabajo u Otro.',
        ]);

        $errorTelefono = $this->validarTelefonosUnicos($data['telefonos']);
if ($errorTelefono) {
    $errors = [];
    foreach ($errorTelefono as $index => $msg) {
        $errors["telefonos.$index.Numero"] = $msg; // clave exacta para cada input
    }
    return back()->withInput()->withErrors($errors);
}

     DB::beginTransaction();
        try {
            $persona = Persona::create([
                'Nombre' => $data['Nombre'],
                'Apellido' => $data['Apellido'],
                'FechaNacimiento' => $data['FechaNacimiento'],
                'Genero' => $data['Genero'],
                'email' => $data['email'],
            ]);

            foreach ($data['telefonos'] as $tel) {
                $persona->telefonos()->create([
                    'Tipo' => ucfirst(strtolower($tel['Tipo'])),
                    'Numero' => $tel['Numero'],
                ]);
            }

            if ($this->bitacoraActiva) {
                BitacoraHelper::registrar(
                    'CREAR',
                    'persona',
                    'Se registró una nueva persona ID: ' . $persona->PersonaID,
                    null,
                    $persona->toArray(),
                    'Módulo de Personas'
                );
            }

            DB::commit();
            return redirect()->route('persona.index')->with('success', 'Persona registrada con éxito');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return back()->withInput()->with('error', 'Ocurrió un error al registrar la persona.');
        }
    }

    // En app/Http/Controllers/PersonaController.php

public function update(Request $request, $id)
{
    $persona = Persona::findOrFail($id);

    $request->merge([
        'Nombre' => $this->cleanInput($request->input('Nombre')),
        'Apellido' => $this->cleanInput($request->input('Apellido')),
        'email' => $this->cleanInput($request->input('email')),
    ]);

    // En PersonaController@update

$telefonos = $request->input('telefonos', []);
    foreach ($telefonos as $i => $tel) {
        
        // ✅ RE-ACTIVAMOS la limpieza para eliminar caracteres ocultos (¡pero sin truncar!)
        $numeroLimpio = isset($tel['Numero']) ? preg_replace('/\D+/', '', $tel['Numero']) : '';
        // ❌ Quitamos el mb_substr() para no truncar un número que ya pasó por JS
        $telefonos[$i]['Numero'] = $numeroLimpio; 
        
        // ... (Limpieza del Tipo) ...
    }
    $request->merge(['telefonos' => $telefonos]);

    $data = $request->validate([
        'Nombre' => ['required','string','max:255','regex:/^[A-Za-z]+(?:\s[A-Za-z]+)*$/'],
        'Apellido' => ['required','string','max:255','regex:/^[A-Za-z]+(?:\s[A-Za-z]+)*$/'],
        'FechaNacimiento' => ['required', 'date'],
        'Genero' => ['required', 'in:F,M'],
        'email' => ['required', 'email', 'max:255', 'unique:persona,email,' . $persona->PersonaID . ',PersonaID'],
        'telefonos' => ['required', 'array', 'min:1'],
        'telefonos.*.Tipo' => ['required', 'string', 'max:50', 'regex:/^(Personal|Trabajo|Otro)$/i'],
        // 💡 CAMBIO CRÍTICO AQUÍ: Suavizamos la regla
        'telefonos.*.Numero' => [
        'required',
        'string',
        'max:8',
        Rule::unique('telefono', 'Numero')->ignore($persona->PersonaID, 'PersonaID'),
    ],
    ], [
       
        'Nombre.regex' => $this->config['mensaje_nombre_regex'] ?? 'El nombre solo debe contener letras (A-Z) y espacios.',
        'Nombre.unique' => 'Este nombre ya está en uso.',
        'Apellido.regex' => $this->config['mensaje_apellido_regex'] ?? 'El apellido solo debe contener letras (A-Z) y espacios.',
        'Apellido.unique' => 'Este apellido ya está en uso.',
        'email.required' => $this->config['mensaje_email_requerido'] ?? 'El correo es obligatorio.',
        'email.email' => $this->config['mensaje_email_invalido'] ?? 'Ingresa un correo válido que lleve @ y dominio.',
        'email.unique' => $this->config['mensaje_email_unico'] ?? 'Este correo ya está registrado.',
        'telefonos.required' => $this->config['mensaje_telefonos_requerido'] ?? 'Agrega al menos un teléfono.',
        'telefonos.*.Numero.regex' => $this->config['mensaje_telefono_formato'] ?? 'Cada teléfono debe tener exactamente 8 dígitos.',
        'telefonos.*.Tipo.regex' => $this->config['mensaje_telefono_tipo'] ?? 'El tipo de teléfono debe ser Personal, Trabajo u Otro.',
        'telefonos.*.Numero.unique' => 'Este número de teléfono ya está registrado.',
    ]);


// VALIDACIÓN DE COMBINACIÓN NOMBRE + APELLIDO
    // --------------------------------------
    $existe = Persona::where('Nombre', $data['Nombre'])
                     ->where('Apellido', $data['Apellido'])
                     ->where('PersonaID', '<>', $persona->PersonaID)
                     ->exists();

    if ($existe) {
        return back()->withInput()->withErrors([
            'Nombre' => 'La combinación de Nombre y Apellido ya está en uso.',
            'Apellido' => 'La combinación de Nombre y Apellido ya está en uso.',
        ]);
    }


    // ----------------------------------------------------
    // LÓGICA DE VALIDACIÓN DE TELÉFONOS (Añadido manejo JSON 422)
    // ----------------------------------------------------
    $errorTelefono = $this->validarTelefonosUnicos($data['telefonos'], $persona->PersonaID);

    if ($errorTelefono) {
        $errors = [];
        foreach ($errorTelefono as $index => $msg) {
            $errors["telefonos.$index.Numero"] = $msg;
        }
        
        // Si la petición espera JSON (Alpine.js), devolvemos 422
        if ($request->wantsJson()) {
            return response()->json(['errors' => $errors], 422);
        }
        
        // Si no, hacemos redirección tradicional
        return back()->withInput()->withErrors($errors);
    }
    // ----------------------------------------------------

    $antes = $persona->toArray();

    DB::beginTransaction();
    try {
        $persona->update([
            'Nombre' => $data['Nombre'],
            'Apellido' => $data['Apellido'],
            'FechaNacimiento' => $data['FechaNacimiento'],
            'Genero' => $data['Genero'],
            'email' => $data['email'],
        ]);

        $persona->telefonos()->delete();
        foreach ($data['telefonos'] as $tel) {
            $persona->telefonos()->create([
                'Tipo' => ucfirst(strtolower($tel['Tipo'])),
                'Numero' => $tel['Numero'],
            ]);
        }

        if ($this->bitacoraActiva) {
            BitacoraHelper::registrar(
                'ACTUALIZAR',
                'persona',
                'Se actualizó la persona ID: ' . $persona->PersonaID,
                $antes,
                $persona->toArray(),
                'Módulo de Personas'
            );
        }

        DB::commit();

        // ----------------------------------------------------
        // LÓGICA DE ÉXITO (Añadido manejo JSON 200)
        // ----------------------------------------------------
        if ($request->wantsJson()) {
            // Devuelve un JSON 200 para Alpine.js
            return response()->json(['success' => 'Persona actualizada con éxito.'], 200); 
        }

        // Redirección tradicional
        return redirect()->route('persona.index')->with('success', 'Persona actualizada con éxito');
        // ----------------------------------------------------

    } catch (\Throwable $e) {
        DB::rollBack();
        report($e);
        
        // ----------------------------------------------------
        // MANEJO DE ERROR DE EXCEPCIÓN (Añadido manejo JSON 500)
        // ----------------------------------------------------
        if ($request->wantsJson()) {
            // Devuelve un JSON 500 para Alpine.js
            return response()->json(['error' => 'Ocurrió un error interno al actualizar la persona.'], 500);
        }
        
        // Manejo de errores para formulario tradicional
        return back()->withInput()->with('error', 'Ocurrió un error al actualizar la persona.');
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

    public function exportarExcel(Request $request)
{
    $query = Persona::with('telefonos');

    // Filtrar si hay búsqueda
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('Nombre', 'LIKE', "%{$search}%")
              ->orWhere('Apellido', 'LIKE', "%{$search}%")
              ->orWhere('email', 'LIKE', "%{$search}%");
        });
    }

    $personas = $query->orderBy('PersonaID', 'desc')->get();

    $filename = 'personas.csv';
    $handle = fopen($filename, 'w+');

    // Cabecera
    fputcsv($handle, ['ID', 'Nombre', 'Apellido', 'FechaNacimiento', 'Genero', 'Email', 'Teléfonos']);

    foreach ($personas as $p) {
        // Combinar teléfonos en una sola columna separados por coma
        $telefonos = $p->telefonos->map(fn($t) => $t->Tipo . ': ' . $t->Numero)->implode(', ');

        fputcsv($handle, [
            $p->PersonaID,
            $p->Nombre,
            $p->Apellido,
            $p->FechaNacimiento,
            $p->Genero,
            $p->email,
            $telefonos
        ]);
    }

    fclose($handle);

    return response()->download($filename)->deleteFileAfterSend(true);
}


public function toggleActivo($id)
{
    $persona = Persona::findOrFail($id);

    // Si está activa y se intenta desactivar
    if ($persona->Activo) {
        $tieneEmpleado = $persona->empleado()->exists();
        $tieneUsuario = $persona->usuario()->exists();

        if ($tieneEmpleado || $tieneUsuario) {
            return redirect()->back()->with('error', 'No se puede inactivar la persona porque está asignada como empleado o usuario.');
        }
    }

    // Cambiar estado normalmente
    $persona->Activo = !$persona->Activo;
    $persona->save();

    return redirect()->back()->with('success', 'Estado actualizado correctamente');
}





    public function destroy($id)
    {
        try {
            $persona = Persona::findOrFail($id);
            $anterior = $persona->toArray();
            $persona->delete();

            if ($this->bitacoraActiva) {
                BitacoraHelper::registrar(
                    'ELIMINAR',
                    'persona',
                    'Se eliminó la persona ID: ' . $persona->PersonaID,
                    $anterior,
                    null,
                    'Módulo de Personas'
                );
            }

            return redirect()->route('persona.index')->with('error', 'Persona eliminada con éxito');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('persona.index')->with('error', 'No se puede eliminar la persona porque tiene registros asociados.');
        }
    }
}