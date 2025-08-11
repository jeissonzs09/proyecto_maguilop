<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CuentaPorCobrar extends Model
{
    protected $table = 'cuentas_por_cobrar';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'FacturaID',
        'fecha_vencimiento',
        'monto_total',
        'monto_pagado',
        'estado',
    ];

    // Relación con la factura
    public function factura()
    {
        return $this->belongsTo(Factura::class, 'FacturaID', 'FacturaID');
    }

    public function pagos()
{
    return $this->hasMany(PagoCuentaPorCobrar::class, 'cuenta_por_cobrar_id');
}

}