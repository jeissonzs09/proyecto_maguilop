<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoDetalle extends Model
{
    protected $table = 'detalle_pedido'; // ← usa el nombre real de tu tabla
    protected $primaryKey = 'DetalleID'; // si tu tabla tiene este como clave primaria

    public $timestamps = false;

    protected $fillable = [
        'PedidoID',
        'ProductoID',
        'Cantidad',
        'PrecioUnitario',
        'Subtotal',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'ProductoID');
    }
}

