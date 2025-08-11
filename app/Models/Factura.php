<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DetalleFactura;

class Factura extends Model
{
    protected $table = 'factura';
    protected $primaryKey = 'FacturaID';
    public $timestamps = false;

    protected $fillable = [
        'NumeroFactura',
        'ClienteID',
        'EmpleadoID',
        'Fecha',
        'Total',
        'Estado',
        'tipo_pago',
        'RTN',
        'CAI',
    ];

    public function detalles()
    {
        return $this->hasMany(DetalleFactura::class, 'FacturaID', 'FacturaID');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'ClienteID');
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'EmpleadoID');
    }

    public function cuentaPorCobrar()
{
    return $this->hasOne(\App\Models\CuentaPorCobrar::class, 'FacturaID');
}
}
