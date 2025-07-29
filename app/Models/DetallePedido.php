<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetallePedido extends Model
{
    protected $table = 'detalle_pedido'; // Nombre correcto de la tabla
    protected $primaryKey = 'DetallePedidoID';
    public $timestamps = false;

    protected $fillable = [
        'PedidoID',
        'ProductoID',
        'Cantidad',
        'PrecioUnitario',
        'Subtotal',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'PedidoID');
    }
}