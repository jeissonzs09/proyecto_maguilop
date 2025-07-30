<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Usuario;
use App\Helpers\PermisosHelper;
use App\Helpers\BitacoraHelper;

class UsuarioController extends Controller
{
    public function index()
{
    if (!PermisosHelper::tienePermiso('Usuarios', 'ver')) {
        abort(403, 'No tienes permiso para ver esta sección.');
    }

    $usuarios = DB::table('usuario')
    ->orderByDesc('UsuarioID') // Ordena por el más reciente
    ->paginate(10);

    $roles = DB::table('tbl_roles')
        ->where('Estado', 'Activo')
        ->pluck('Descripcion', 'ID_Rol');

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
        ->pluck('Descripcion', 'ID_Rol'); // Devuelve un array ID => Nombre

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

$request->validate([
    'NombreUsuario' => 'required|string|unique:usuario,NombreUsuario',
    'TipoUsuario' => 'required|string',
    'correo' => 'required|email|unique:usuario,CorreoElectronico',
    'password' => [
        'required',
        'string',
        'min:8',
        'regex:/[^A-Za-z0-9]/', // al menos un carácter especial
    ],
    'EmpleadoID' => 'required|integer|exists:empleado,EmpleadoID',
], [
    'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
    'password.regex' => 'La contraseña debe contener al menos un carácter especial.',
    'password.required' => 'El campo contraseña es obligatorio.',
    'correo.unique' => 'Este correo ya está registrado.',
    'NombreUsuario.unique' => 'El nombre de usuario ya está en uso.',
]);


    DB::table('usuario')->insert([
        'NombreUsuario' => $request->NombreUsuario,
        'TipoUsuario' => $request->TipoUsuario,
        'EmpleadoID' => $request->EmpleadoID,
        'Contrasena' => bcrypt($request->password),
        'Estado' => 'Activo',
        'PrimerAcceso' => 1,
        'UsuarioRegistro' => auth()->user()->NombreUsuario,
        'FechaRegistro' => now(),
        'CorreoElectronico' => $request->correo,
    ]);

// Bitácora
    BitacoraHelper::registrar(
        'CREAR',
        'usuario',
        'Se creó un nuevo usuario: ' . $request->NombreUsuario,
        null,
        [
            'NombreUsuario' => $request->NombreUsuario,
            'TipoUsuario' => $request->TipoUsuario,
            'EmpleadoID' => $request->EmpleadoID,
            'CorreoElectronico' => $request->correo
        ],
        'Módulo de Usuarios'
    );

    return redirect()->route('usuarios.index')->with('success', 'Usuario creado correctamente.');

}


public function edit($id)
{
    $usuario = DB::table('usuario')->where('UsuarioID', $id)->first();

    $empleados = DB::table('empleado')
        ->join('persona', 'empleado.PersonaID', '=', 'persona.PersonaID')
        ->select('empleado.EmpleadoID', DB::raw("CONCAT(persona.Nombre, ' ', persona.Apellido) as NombreCompleto"))
        ->get();

    $roles = DB::table('tbl_roles')->pluck('Descripcion');

    return view('usuarios.edit', compact('usuario', 'empleados', 'roles'));
}


public function update(Request $request, $id)
{
    $request->validate([
        'nombre_usuario' => 'required',
        'correo' => 'required|email',
        'rol' => 'required',
        'empleado' => 'required',
    ]);

    $datosPrevios = DB::table('usuario')->where('UsuarioID', $id)->first();

    DB::table('usuario')->where('UsuarioID', $id)->update([
        'NombreUsuario' => $request->nombre_usuario,
        'CorreoElectronico' => $request->correo,
        'TipoUsuario' => $request->rol,
        'EmpleadoID' => $request->empleado,
    ]);

$datosNuevos = [
    'NombreUsuario' => $request->nombre_usuario,
    'CorreoElectronico' => $request->correo,
    'TipoUsuario' => $request->rol,
    'EmpleadoID' => $request->empleado,
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


}


