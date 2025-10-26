<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    protected $table = 'configuracion';
    protected $primaryKey = 'ConfiguracionID';
    protected $fillable = ['bitacora_activa'];
}