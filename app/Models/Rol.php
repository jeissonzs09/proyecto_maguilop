<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'tbl_roles';
    protected $primaryKey = 'ID_Rol';
    public $timestamps = false;

    protected $fillable = ['Descripcion', 'Estado', 'UsuarioRegistro', 'FEC_Registro'];
}
