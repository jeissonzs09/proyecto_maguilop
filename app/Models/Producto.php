<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DetallePedido;

class Producto extends Model
{
    protected $table = 'producto'; // nombre exacto de la tabla

    protected $primaryKey = 'ProductoID'; // llave primaria

    public $timestamps = false; // asumo que no tienes campos created_at, updated_at

    protected $fillable = [
        'Codigo',         // 🆕 Campo nuevo
        'NombreProducto',
        'Descripcion',
        'TipoProductoID',
        'CategorialID',
        'MarcaID',
        'UnidadID',
        'PrecioCompra',
        'PrecioVenta',
        'Stock',
        'ProveedorID',
        'AlmacenID',
        'EmbalajeID',
        'Area',           // 🆕 Campo nuevo
        'Foto',           // 🆕 Campo nuevo
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'ProveedorID', 'ProveedorID');
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'CategoriaID');
    }

    public function detallePedidos()
    {
        return $this->hasMany(DetallePedido::class, 'ProductoID', 'ProductoID');
    }
}