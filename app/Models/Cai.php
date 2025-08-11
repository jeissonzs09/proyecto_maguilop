<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cai extends Model
{
    protected $table = 'cai';
    protected $primaryKey = 'id'; // 👈 asegúrate de esto
    public $timestamps = true;    // o false, según lo uses

    protected $fillable = [
        'codigo',
        'rango_inicial',
        'rango_final',
        'fecha_autorizacion',
        'fecha_limite_emision',
        'facturas_emitidas',
    ];
}


