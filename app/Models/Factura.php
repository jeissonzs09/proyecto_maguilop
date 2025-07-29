<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DetalleFactura; // Asegúrate de importar la clase si está en otro namespace

class Factura extends Model
{
    protected $table = 'factura'; // ✅ Nombre de la tabla en singular si así está en la BD
    protected $primaryKey = 'FacturaID'; // ✅ Clave primaria personalizada
    public $timestamps = false; // ✅ Si no usas created_at / updated_at

    protected $fillable = [
        'ClienteID',
        'EmpleadoID',
        'Fecha',
        'Total',
        'Estado',
    ];

    // Relación uno a muchos con detalle_factura
    public function detalles()
    {
        return $this->hasMany(DetalleFactura::class, 'FacturaID', 'FacturaID');
        // El tercer parámetro garantiza coincidencia correcta si la clave primaria es personalizada
    }

    public function cliente()
{
    return $this->belongsTo(Cliente::class, 'ClienteID');
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