<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RolPermisoModulo extends Model
{
    protected $table = 'rol_permiso_modulo';

    protected $fillable = [
    'ID_Rol',
    'Modulo',
    'puede_ver',
    'puede_crear',
    'puede_editar',
    'puede_borrar',
    ];

    public $timestamps = false;
}