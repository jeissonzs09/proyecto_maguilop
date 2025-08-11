<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RolPermisoModulo;
use App\Models\Rol;
use Illuminate\Support\Facades\DB;
use App\Helpers\PermisosHelper;

class RolPermisoModuloController extends Controller
{
    public function index()
{
    if (!PermisosHelper::tienePermiso('Permisos por Rol', 'ver')) {
        abort(403, 'No tienes permiso para ver esta sección.');
    }

    $roles = Rol::all();

    // Usa exactamente los mismos nombres que en el sidebar / PermisosHelper
    $modulos = [
        // Seguridad
        'Usuarios',
        'Roles',
        'Permisos por Rol',
        'Backups',
        'Bitacora', // o 'Bitácora' según el nombre que uses en PermisosHelper

        // Gestión Persona
        'Persona',
        'Empleados',
        'Proveedores',
        'Clientes',
        'Empresas',

        // Operación
        'Reparaciones',
        'Productos',
        'Pedidos',
        'Ventas',
        'Compras',
        'DetalleCompras', // unifícalo como "DetalleCompras"

        // Facturación
        'Factura',
        'CAI',
        'CuentasPorCobrar',

        // Reportes
        'Reportes',
    ];

    $permisosExistentes = RolPermisoModulo::all();
    $permisos = [];

    foreach ($permisosExistentes as $permiso) {
        $permisos[$permiso->ID_Rol][$permiso->Modulo] = [
            'ver'     => $permiso->puede_ver,
            'crear'   => $permiso->puede_crear,
            'editar'  => $permiso->puede_editar,
            'eliminar'=> $permiso->puede_borrar,
        ];
    }

    return view('roles.permisos', compact('roles', 'modulos', 'permisos'));
}


    public function guardar(Request $request)
    {
        foreach ($request->permisos as $rolId => $modulos) {
            foreach ($modulos as $modulo => $acciones) {
                RolPermisoModulo::updateOrCreate(
                    ['ID_Rol' => $rolId, 'Modulo' => $modulo],
                    [
                        'puede_ver' => $acciones['ver'] ?? 0,
                        'puede_crear' => $acciones['crear'] ?? 0,
                        'puede_editar' => $acciones['editar'] ?? 0,
                        'puede_borrar' => $acciones['eliminar'] ?? 0,
                    ]
                );
            }
        }

        return redirect()->back()->with('success', 'Permisos actualizados correctamente.');
    }
}