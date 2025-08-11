<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoCuentaPorCobrar extends Model
{
    protected $table = 'pagos_cuentas_por_cobrar';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'cuenta_por_cobrar_id',
        'fecha_pago',
        'monto',
        'descripcion',
    ];

    public function cuenta()
    {
        return $this->belongsTo(CuentaPorCobrar::class, 'cuenta_por_cobrar_id');
    }
}
