<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Usuario;
use App\Helpers\PermisosHelper;
use App\Helpers\BitacoraHelper;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Notifications\CrearCuenta;

class UsuarioController extends Controller
{
    public function index(Request $request)
{
    if (!PermisosHelper::tienePermiso('Usuarios', 'ver')) {
        abort(403, 'No tienes permiso para ver esta sección.');
    }

    $query = DB::table('usuario');

    // Búsqueda
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('NombreUsuario', 'like', "%{$search}%")
              ->orWhere('CorreoElectronico', 'like', "%{$search}%")
              ->orWhere('TipoUsuario', 'like', "%{$search}%");
        });
    }

    $usuarios = $query->orderByDesc('UsuarioID')->paginate(10);

    $roles = DB::table('tbl_roles')
        ->where('Estado', 'Activo')
        ->get()
        ->mapWithKeys(function ($rol) {
            return [$rol->ID_Rol => strtoupper($rol->Descripcion)];
        });

    $empleados = DB::table('empleado')
        ->join('persona', 'empleado.PersonaID', '=', 'persona.PersonaID')
        ->select('empleado.EmpleadoID', DB::raw("CONCAT(persona.Nombre, ' ', persona.Apellido) as nombre_completo"))
        ->get();

        
    return view('usuarios.index', compact('usuarios', 'roles', 'empleados'));
}


public function create()
{
    // Cargar los roles desde la base de datos
    $roles = DB::table('tbl_roles')
        ->where('Estado', 'Activo') // Opcional: solo roles activos
        ->get()
        ->mapWithKeys(function ($rol) {
            return [$rol->ID_Rol => strtoupper($rol->Descripcion)];
        }); // Devuelve un array ID => Nombre en mayúsculas

    $empleados = DB::table('empleado')
        ->join('persona', 'empleado.PersonaID', '=', 'persona.PersonaID')
        ->select('empleado.EmpleadoID', DB::raw("CONCAT(persona.Nombre, ' ', persona.Apellido) as nombre_completo"))
        ->get();

    return view('usuarios.create', compact('roles', 'empleados'));
}


public function store(Request $request)
{
    if (!PermisosHelper::tienePermiso('Usuarios', 'crear')) {
        abort(403, 'No tienes permiso para crear usuarios.');
    }

    // Validaciones personalizadas
    $this->validateUsuario($request);

    // Convertir nombre de usuario a mayúsculas
    $nombreUsuario = strtoupper(trim($request->NombreUsuario));
    $correo = strtolower(trim($request->correo));

    // Usar fecha de vencimiento del request o obtener desde parámetros
    $fechaVencimiento = $request->FechaVencimiento ?: $this->obtenerFechaVencimiento();

    // 1) Insertar y obtener el ID
    $id = DB::table('usuario')->insertGetId([
        'NombreUsuario'     => $nombreUsuario,
        'TipoUsuario'       => strtoupper($request->TipoUsuario),
        'EmpleadoID'        => $request->EmpleadoID,
        'Contrasena'        => Hash::make(Str::random(32)), // Contraseña temporal
        'Estado'            => 'Activo',
        'PrimerAcceso'      => 1,
        'UsuarioRegistro'   => auth()->user()->NombreUsuario,
        'FechaRegistro'     => now(),
        'FechaCreacion'     => now(),
        'FechaVencimiento'  => $fechaVencimiento,
        'CorreoElectronico' => $correo,
        'email'             => $correo,
    ]);

    // 2) Registrar en bitácora
    BitacoraHelper::registrar(
        'CREAR',
        'usuario',
        'Se creó un nuevo usuario: ' . $nombreUsuario,
        null,
        [
            'NombreUsuario'     => $nombreUsuario,
            'TipoUsuario'       => strtoupper($request->TipoUsuario),
            'EmpleadoID'        => $request->EmpleadoID,
            'CorreoElectronico' => $correo,
            'FechaVencimiento'  => $fechaVencimiento
        ],
        'Módulo de Usuarios'
    );

    // 3) Recuperar el modelo y enviar correo de bienvenida con token
    $usuario = Usuario::find($id);

    $token = Password::broker()->createToken($usuario);
    $usuario->notify(new CrearCuenta($token));

    return redirect()
        ->route('usuarios.index')
        ->with('success', 'Usuario creado exitosamente. Se envió un correo de bienvenida para crear su contraseña.');
}

public function edit($id)
{
    $usuario = DB::table('usuario')->where('UsuarioID', $id)->first();

    $empleados = DB::table('empleado')
        ->join('persona', 'empleado.PersonaID', '=', 'persona.PersonaID')
        ->select('empleado.EmpleadoID', DB::raw("CONCAT(persona.Nombre, ' ', persona.Apellido) as NombreCompleto"))
        ->get();

    $roles = DB::table('tbl_roles')
        ->where('Estado', 'Activo')
        ->get()
        ->map(function ($rol) {
            return strtoupper($rol->Descripcion);
        });

    return view('usuarios.edit', compact('usuario', 'empleados', 'roles'));
}


public function update(Request $request, $id)
{
    // Validaciones personalizadas para actualización
    $this->validateUsuarioUpdate($request, $id);

    $datosPrevios = DB::table('usuario')->where('UsuarioID', $id)->first();

    // Convertir a mayúsculas
    $nombreUsuario = strtoupper(trim($request->nombre_usuario));
    $correo = strtolower(trim($request->correo));

    DB::table('usuario')->where('UsuarioID', $id)->update([
        'NombreUsuario' => $nombreUsuario,
        'CorreoElectronico' => $correo,
        'email' => $correo,
        'TipoUsuario' => strtoupper($request->rol),
        'EmpleadoID' => $request->empleado,
        'FechaVencimiento' => $request->FechaVencimiento,
    ]);

    $datosNuevos = [
        'NombreUsuario' => $nombreUsuario,
        'CorreoElectronico' => $correo,
        'TipoUsuario' => strtoupper($request->rol),
        'EmpleadoID' => $request->empleado,
        'FechaVencimiento' => $request->FechaVencimiento,
    ];

    BitacoraHelper::registrar(
        'ACTUALIZAR',
        'usuario',
        'Se actualizó el usuario ID: ' . $id,
        $datosPrevios,
        $datosNuevos,
        'Módulo de Usuarios'
    );

    return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente.');
}

/**
 * Validaciones para actualización de usuario
 */
private function validateUsuarioUpdate(Request $request, $id)
{
    $request->validate([
        'nombre_usuario' => [
            'required',
            'string',
            'min:3',
            'max:30',
            'regex:/^[A-Z0-9_]+$/',
            'unique:usuario,NombreUsuario,' . $id . ',UsuarioID'
        ],
        'correo' => [
            'required',
            'email',
            'min:5',
            'max:100',
            'regex:/^[^@\s]+@[^@\s]+\.[^@\s]+$/',
            'unique:usuario,CorreoElectronico,' . $id . ',UsuarioID'
        ],
        'rol' => 'required|string',
        'empleado' => 'required|integer|exists:empleado,EmpleadoID|unique:usuario,EmpleadoID,' . $id . ',UsuarioID',
        'FechaVencimiento' => 'required|date|after_or_equal:today',
    ], [
        'nombre_usuario.required' => 'El nombre de usuario es obligatorio.',
        'nombre_usuario.min' => 'El nombre de usuario debe tener al menos 3 caracteres.',
        'nombre_usuario.max' => 'El nombre de usuario no puede exceder 30 caracteres.',
        'nombre_usuario.regex' => 'El nombre de usuario solo puede contener letras mayúsculas, números y guiones bajos.',
        'nombre_usuario.unique' => 'Este nombre de usuario ya está en uso.',
        'correo.required' => 'El correo electrónico es obligatorio.',
        'correo.email' => 'Debe ingresar un correo electrónico válido.',
        'correo.min' => 'El correo debe tener al menos 5 caracteres.',
        'correo.max' => 'El correo no puede exceder 100 caracteres.',
        'correo.regex' => 'El correo no puede contener espacios.',
        'correo.unique' => 'Este correo ya está registrado.',
        'rol.required' => 'Debe seleccionar un rol.',
        'empleado.required' => 'Debe seleccionar un empleado.',
        'empleado.exists' => 'El empleado seleccionado no existe.',
        'empleado.unique' => 'Este empleado ya tiene un usuario asociado.',
        'FechaVencimiento.required' => 'La fecha de vencimiento es obligatoria.',
        'FechaVencimiento.date' => 'Debe ingresar una fecha válida.',
        'FechaVencimiento.after_or_equal' => 'La fecha de vencimiento no puede ser anterior a hoy.',
    ]);

    // Validar que el nombre de usuario esté en mayúsculas
    if ($request->nombre_usuario !== strtoupper($request->nombre_usuario)) {
        return redirect()->back()
            ->withErrors(['nombre_usuario' => 'El nombre de usuario debe estar en mayúsculas.'])
            ->withInput();
    }

    // Validar dominio institucional si es necesario
    $this->validarDominioInstitucional($request->correo);
}

public function destroy($id)
{
    $datosEliminados = DB::table('usuario')->where('UsuarioID', $id)->first();
    DB::table('usuario')->where('UsuarioID', $id)->delete();


    BitacoraHelper::registrar(
    'ELIMINAR',
    'usuario',
    'Se eliminó el usuario ID: ' . $id,
    $datosEliminados,
    null,
    'Módulo de Usuarios'
);

return redirect()->back()->with('success', 'Usuario eliminado correctamente.');
    //return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado correctamente.');
    return redirect()->back()->with('success', 'Permisos actualizados correctamente.');
}

/**
 * Validaciones personalizadas para el usuario
 */
private function validateUsuario(Request $request)
{
    // Validar nombre de usuario
    $request->validate([
        'NombreUsuario' => [
            'required',
            'string',
            'min:3',
            'max:30',
            'regex:/^[A-Z0-9_]+$/',
            'unique:usuario,NombreUsuario'
        ],
        'TipoUsuario' => 'required|string',
        'correo' => [
            'required',
            'email',
            'min:5',
            'max:100',
            'regex:/^[^@\s]+@[^@\s]+\.[^@\s]+$/',
            'unique:usuario,CorreoElectronico'
        ],
        'EmpleadoID' => 'required|integer|exists:empleado,EmpleadoID|unique:usuario,EmpleadoID',
        'FechaVencimiento' => 'required|date|after_or_equal:today',
    ], [
        'NombreUsuario.required' => 'El nombre de usuario es obligatorio.',
        'NombreUsuario.min' => 'El nombre de usuario debe tener al menos 3 caracteres.',
        'NombreUsuario.max' => 'El nombre de usuario no puede exceder 30 caracteres.',
        'NombreUsuario.regex' => 'El nombre de usuario solo puede contener letras mayúsculas, números y guiones bajos.',
        'NombreUsuario.unique' => 'Este nombre de usuario ya está en uso.',
        'correo.required' => 'El correo electrónico es obligatorio.',
        'correo.email' => 'Debe ingresar un correo electrónico válido.',
        'correo.min' => 'El correo debe tener al menos 5 caracteres.',
        'correo.max' => 'El correo no puede exceder 100 caracteres.',
        'correo.regex' => 'El correo no puede contener espacios.',
        'correo.unique' => 'Este correo ya está registrado.',
        'EmpleadoID.required' => 'Debe seleccionar un empleado.',
        'EmpleadoID.exists' => 'El empleado seleccionado no existe.',
        'EmpleadoID.unique' => 'Este empleado ya tiene un usuario asociado.',
        'FechaVencimiento.required' => 'La fecha de vencimiento es obligatoria.',
        'FechaVencimiento.date' => 'Debe ingresar una fecha válida.',
        'FechaVencimiento.after_or_equal' => 'La fecha de vencimiento no puede ser anterior a hoy.',
    ]);

    // Validar que el nombre de usuario esté en mayúsculas
    if ($request->NombreUsuario !== strtoupper($request->NombreUsuario)) {
        return redirect()->back()
            ->withErrors(['NombreUsuario' => 'El nombre de usuario debe estar en mayúsculas.'])
            ->withInput();
    }

    // Validar dominio institucional si es necesario
    $this->validarDominioInstitucional($request->correo);
}

/**
 * Validar dominio institucional
 */
private function validarDominioInstitucional($correo)
{
    // Obtener dominios permitidos desde parámetros o configuración
    $dominiosPermitidos = $this->obtenerDominiosPermitidos();
    
    if (!empty($dominiosPermitidos)) {
        $dominio = substr(strrchr($correo, "@"), 1);
        if (!in_array($dominio, $dominiosPermitidos)) {
            return redirect()->back()
                ->withErrors(['correo' => 'Debe usar un correo institucional válido.'])
                ->withInput();
        }
    }
}

/**
 * Obtener dominios permitidos desde parámetros
 */
private function obtenerDominiosPermitidos()
{
    try {
        $parametros = DB::table('parametros')
            ->where('Clave', 'dominios_correo_institucional')
            ->first();
        
        if ($parametros) {
            return explode(',', $parametros->Valor);
        }
    } catch (\Exception $e) {
        // Si no existe la tabla de parámetros, continuar sin validación
    }
    
    return [];
}


/**
 * Obtener fecha de vencimiento desde parámetros
 */
private function obtenerFechaVencimiento()
{
    try {
        $parametros = DB::table('parametros')
            ->where('Clave', 'dias_vencimiento_usuario')
            ->first();
        
        if ($parametros) {
            $dias = (int) $parametros->Valor;
            return now()->addDays($dias);
        }
    } catch (\Exception $e) {
        // Si no existe la tabla de parámetros, usar default
    }
    
    // Default: 365 días
    return now()->addDays(365);
}


}