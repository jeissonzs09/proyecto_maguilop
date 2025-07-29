<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleFactura extends Model
{
    protected $table = 'detalle_factura';
    protected $primaryKey = 'DetalleID';
    public $timestamps = false;

    protected $fillable = [
        'FacturaID',
        'ProductoID',
        'Cantidad',
        'PrecioUnitario',
        'Subtotal',
    ];

    public function producto()
{
    return $this->belongsTo(\App\Models\Producto::class, 'ProductoID');
}
}