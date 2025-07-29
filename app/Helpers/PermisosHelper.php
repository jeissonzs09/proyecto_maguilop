<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use App\Models\RolPermisoModulo;
use Illuminate\Support\Facades\DB;

class PermisosHelper
{
    public static function tienePermiso($modulo, $accion)
    {

            $usuario = auth()->user();
    if ($usuario->TipoUsuario === 'Administrador') {
        return true; // acceso total
    }
        $usuario = Auth::user();
        $rolNombre = $usuario->TipoUsuario;

        // Obtener ID del rol desde tbl_roles
        $rolId = DB::table('tbl_roles')->where('Descripcion', $rolNombre)->value('ID_Rol');
        if (!$rolId) return false;

        $permiso = RolPermisoModulo::where('ID_Rol', $rolId)
            ->where('Modulo', $modulo)
            ->first();

        if (!$permiso) return false;

        return match ($accion) {
            'ver' => $permiso->puede_ver,
            'crear' => $permiso->puede_crear,
            'editar' => $permiso->puede_editar,
            'eliminar' => $permiso->puede_borrar,
            default => false,
        };
    }
}

