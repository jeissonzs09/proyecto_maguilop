<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    protected $table = 'bitacora';

    protected $primaryKey = 'BitacoraID';

    public $timestamps = false;

    protected $fillable = [
        'UsuarioID',
        'Accion',
        'TablaAfectada',
        'FechaAccion',
        'Descripcion',
        'DatosPrevios',
        'DatosNuevos',
        'Modulo',
        'Resultado',
    ];
}

