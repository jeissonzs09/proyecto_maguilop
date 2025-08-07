<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Compra;
use App\Models\Producto;

class DetalleCompra extends Model
{
    protected $table = 'detalle_compra';
    protected $primaryKey = 'DetalleCompraID';
    public $timestamps = false;

    protected $fillable = [
        'CompraID',
        'ProductoID',
        'Cantidad',
        'PrecioUnitario',
        'Subtotal',
    ];

    // Relación con Producto
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'ProductoID', 'ProductoID');
    }

    // ✅ Relación con Compra
    public function compra()
    {
        return $this->belongsTo(Compra::class, 'CompraID', 'CompraID');
    }
}