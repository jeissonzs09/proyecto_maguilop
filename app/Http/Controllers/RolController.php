<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Bitacora;
use Illuminate\Support\Facades\Auth;
use App\Helpers\PermisosHelper;
use App\Helpers\BitacoraHelper;

class RolController extends Controller
{
    public function index(Request $request)
{
    if (!PermisosHelper::tienePermiso('Roles', 'ver')) {
        abort(403, 'No tienes permiso para ver esta sección.');
    }

    $perPage = $request->input('per_page', 10);

    $query = DB::table('tbl_roles')->orderBy('ID_Rol', 'asc');

    // Si hay búsqueda
    if ($request->filled('search')) {
        $search = strtoupper($request->input('search')); // forzar mayúsculas
        $query->where('Descripcion', 'LIKE', $search . '%');
    }

    $roles = $query->paginate($perPage)->appends([
        'per_page' => $perPage,
        'search'   => $request->input('search'),
    ]);

    // ✅ pasamos también la búsqueda a la vista
    $search = $request->input('search');

    return view('roles.index', compact('roles', 'search'));
}


    public function create()
    {
        return view('roles.create');
    }

    public function store(Request $request)
{
    $request->validate([
        'Descripcion' => [
            'required',
            'string',
            'min:4',
            'max:255',
            'regex:/^[A-ZÁÉÍÓÚÑ\s]+$/', // Solo mayúsculas y espacios
            'unique:tbl_roles,Descripcion', // No duplicados
        ],
        'Estado' => 'required|string|max:20',
    ], [
        'Descripcion.required' => 'El campo Rol es obligatorio.',
        'Descripcion.min' => 'El Rol debe tener al menos 4 caracteres.',
        'Descripcion.max' => 'El Rol no puede tener más de 255 caracteres.',
        'Descripcion.regex' => 'El Rol solo puede contener letras mayúsculas y espacios.',
        'Descripcion.unique' => 'Ya existe un rol con ese nombre.',
        'Estado.required' => 'El campo Estado es obligatorio.',
    ]);

    DB::table('tbl_roles')->insert([
        'Descripcion' => strtoupper($request->Descripcion), // fuerza mayúsculas en backend
        'Estado' => $request->Estado,
        'UsuarioRegistro' => auth()->user()->NombreUsuario ?? 'sistema',
        'FEC_Registro' => Carbon::now(),
    ]);

    // Bitácora
    BitacoraHelper::registrar(
        'CREAR',
        'tbl_roles',
        'Se creó un nuevo rol: ' . $request->Descripcion,
        null,
        [
            'Descripcion' => $request->Descripcion,
            'Estado' => $request->Estado,
        ],
        'Módulo de Roles'
    );

    return redirect()->route('roles.index')->with('success', 'Rol creado exitosamente.');
}




    public function edit($id)
    {
        $rol = DB::table('tbl_roles')->where('ID_Rol', $id)->first();
        return view('roles.edit', compact('rol'));
    }

 public function update(Request $request, $id)
{
    $request->validate([
        'Descripcion' => [
            'required',
            'string',
            'min:4',
            'max:255',
            'regex:/^[A-ZÁÉÍÓÚÑ\s]+$/', // Solo mayúsculas y espacios
            'unique:tbl_roles,Descripcion,' . $id . ',ID_Rol', // Ignora el rol actual
        ],
        'Estado' => 'required|string|max:20',
    ], [
        'Descripcion.required' => 'El campo Rol es obligatorio.',
        'Descripcion.min' => 'El Rol debe tener al menos 4 caracteres.',
        'Descripcion.max' => 'El Rol no puede tener más de 255 caracteres.',
        'Descripcion.regex' => 'El Rol solo puede contener letras mayúsculas y espacios.',
        'Descripcion.unique' => 'Ya existe un rol con ese nombre.',
        'Estado.required' => 'El campo Estado es obligatorio.',
    ]);

    $datosPrevios = DB::table('tbl_roles')->where('ID_Rol', $id)->first();

    DB::table('tbl_roles')->where('ID_Rol', $id)->update([
        'Descripcion' => strtoupper($request->Descripcion), // fuerza mayúsculas
        'Estado' => $request->Estado,
    ]);

    $datosNuevos = [
        'Descripcion' => strtoupper($request->Descripcion),
        'Estado' => $request->Estado,
    ];

    // Bitácora
    BitacoraHelper::registrar(
        'ACTUALIZAR',
        'tbl_roles',
        'Se actualizó el rol ID: ' . $id,
        $datosPrevios,
        $datosNuevos,
        'Módulo de Roles'
    );

    return redirect()->route('roles.index')->with('success', 'Rol actualizado correctamente.');
}



    public function destroy($id)
    {
        $rol = DB::table('tbl_roles')->where('ID_Rol', $id)->first();

DB::table('tbl_roles')->where('ID_Rol', $id)->delete();

BitacoraHelper::registrar(
    'ELIMINAR',
    'tbl_roles',
    'Se eliminó el rol ID: ' . $id,
    $rol,
    null,
    'Módulo de Roles'
);
        return redirect()->route('roles.index')->with('success', 'Rol eliminado correctamente.');
    }

    public function editPermisos($id)
{
    $rol = DB::table('tbl_roles')->where('ID_Rol', $id)->first();

    $permisos = DB::table('permiso')->get();

    $permisosAsignados = DB::table('rol_permiso')
        ->where('ID_Rol', $id)
        ->pluck('PermisoID')
        ->toArray();

    return view('roles.permisos', compact('rol', 'permisos', 'permisosAsignados'));
}

public function updatePermisos(Request $request, $id)
{
    // Obtener permisos anteriores (para la bitácora)
    $permisosAnteriores = DB::table('rol_permiso')
        ->where('ID_Rol', $id)
        ->pluck('PermisoID')
        ->toArray();

    // Elimina todos los permisos anteriores del rol
    DB::table('rol_permiso')->where('ID_Rol', $id)->delete();

    $nuevosPermisos = [];

    // Inserta los nuevos permisos seleccionados
    if ($request->has('permisos')) {
        foreach ($request->permisos as $permisoID) {
            DB::table('rol_permiso')->insert([
                'ID_Rol' => $id,
                'PermisoID' => $permisoID,
            ]);
            $nuevosPermisos[] = $permisoID;
        }
    }

    // Registrar en bitácora
    BitacoraHelper::registrar(
        'ACTUALIZAR',
        'rol_permiso',
        'Se actualizaron los permisos del rol ID: ' . $id,
        ['PermisosAnteriores' => $permisosAnteriores],
        ['PermisosNuevos' => $nuevosPermisos],
        'Módulo de Roles'
    );

    return redirect()->route('roles.index')->with('success', 'Permisos actualizados correctamente.');
}


}