<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    protected $table = 'departamento';

    protected $primaryKey = 'DepartamentoID';

    protected $fillable = [
        'Descripcion',
        // otros campos si existen
    ];

    public function empleados()
{
    return $this->hasMany(Empleado::class, 'DepartamentoID');
}

}