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
        'TipoUsuario'   => 'required|string',
        'correo'        => 'required|email|unique:usuario,CorreoElectronico|unique:usuario,email',
        'EmpleadoID'    => 'required|integer|exists:empleado,EmpleadoID',
    ], [
        'correo.unique'        => 'Este correo ya está registrado.',
        'NombreUsuario.unique' => 'El nombre de usuario ya está en uso.',
    ]);

    // 1) Insertar y obtener el ID
    $id = DB::table('usuario')->insertGetId([
        'NombreUsuario'     => $request->NombreUsuario,
        'TipoUsuario'       => $request->TipoUsuario,
        'EmpleadoID'        => $request->EmpleadoID,
        'Contrasena' => Hash::make(Str::random(32)), // <--- en vez de null
        'Estado'            => 'Activo',
        'PrimerAcceso'      => 1,
        'UsuarioRegistro'   => auth()->user()->NombreUsuario,
        'FechaRegistro'     => now(),
        'CorreoElectronico' => $request->correo,      // sincronizados
        'email'             => $request->correo,      // sincronizados
    ]);

    // 2) Registrar en bitácora
    BitacoraHelper::registrar(
        'CREAR',
        'usuario',
        'Se creó un nuevo usuario: ' . $request->NombreUsuario,
        null,
        [
            'NombreUsuario'     => $request->NombreUsuario,
            'TipoUsuario'       => $request->TipoUsuario,
            'EmpleadoID'        => $request->EmpleadoID,
            'CorreoElectronico' => $request->correo
        ],
        'Módulo de Usuarios'
    );

    // 3) Recuperar el modelo y enviar correo de bienvenida con token
    $usuario = Usuario::find($id);

    // IMPORTANTE: limpiar tokens viejos en pruebas si andabas con correos malos
    // DB::table('password_reset_tokens')->where('email', 'like', $usuario->CorreoElectronico)->delete();

    $token = Password::broker()->createToken($usuario);
    $usuario->notify(new CrearCuenta($token));

    return redirect()
        ->route('usuarios.index')
        ->with('success', 'Usuario creado. Se envió un correo de bienvenida para crear su contraseña.');
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
        'email'             => $request->correo, // <<--- mantener en sync
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