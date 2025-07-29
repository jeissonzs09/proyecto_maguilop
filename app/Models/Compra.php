<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DetalleCompra;

class Compra extends Model
{
    protected $table = 'compra'; // ✅ Nombre de la tabla
    protected $primaryKey = 'CompraID'; // ✅ Clave primaria personalizada
    public $timestamps = false; // ✅ Sin created_at / updated_at

protected $fillable = [
    'ProveedorID',
    'EmpleadoID',
    'FechaCompra',
    'TotalCompra',
    'Estado',
];


    // Relación uno a muchos con detalle_compra
    public function detalles()
    {
        return $this->hasMany(DetalleCompra::class, 'CompraID', 'CompraID');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'ProveedorID');
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'EmpleadoID');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'ProductoID');
    }
}