<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $table = 'empresa';
    protected $primaryKey = 'EmpresaID';
    public $timestamps = false;

    protected $fillable = [
        'NombreEmpresa',
        'Website',
        'Telefono',
        'Direccion',
        'Descripcion',
    ];

    public function proveedores()
{
    return $this->hasMany(Proveedor::class, 'EmpresaID', 'EmpresaID');
}

}

