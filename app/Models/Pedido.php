<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $table = 'pedido'; // Nombre de la tabla en la base de datos
    protected $primaryKey = 'PedidoID';
    public $timestamps = false;

    protected $fillable = [
        'ClienteID',
        'EmpleadoID',
        'FechaPedido',
        'FechaEntrega',
        'Estado',
    ];

    // Relación: un pedido tiene muchos detalles
    public function detalles()
    {
        return $this->hasMany(PedidoDetalle::class, 'PedidoID', 'PedidoID');
    }

    // Relación: un pedido pertenece a un cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'ClienteID', 'ClienteID');
    }

    // Relación: un pedido pertenece a un empleado
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'EmpleadoID', 'EmpleadoID');
    }

    // Relación: obtener productos a través de detalles del pedido
    // Esto puede ser útil si quieres obtener todos los productos relacionados a este pedido
    public function productos()
    {
        return $this->hasManyThrough(
            Producto::class,
            PedidoDetalle::class,
            'PedidoID',     // Foreign key en pedido_detalle que referencia pedido
            'ProductoID',   // Foreign key en producto
            'PedidoID',     // Local key en pedido
            'ProductoID'    // Local key en pedido_detalle
        );
    }
}
