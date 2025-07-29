<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $table = 'venta'; // Nombre de la tabla
    protected $primaryKey = 'VentaID'; // Clave primaria
    public $timestamps = false; // Desactivar timestamps si no se utilizan

    protected $fillable = [
        'ClienteID',
        'EmpleadoID',
        'ProductoID',
        'FechaVenta',
        'TotalVenta',
    ];


public function producto()
{
    return $this->belongsTo(\App\Models\Producto::class, 'ProductoID');
}

public function cliente()
{
    return $this->belongsTo(Cliente::class, 'ClienteID');
}

public function empleado()
{
    return $this->belongsTo(Empleado::class, 'EmpleadoID');
}


}
