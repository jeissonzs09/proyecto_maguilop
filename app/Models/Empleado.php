<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    protected $table = 'empleado'; // Tabla en singular, debe coincidir con la DB

    protected $primaryKey = 'EmpleadoID';

    public $timestamps = false; // Si tu tabla no tiene created_at/updated_at

    protected $fillable = [
        'PersonaID',
        'Departamento',
        'Cargo',
        'FechaContratacion',
        'Salario',
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'PersonaID', 'PersonaID');
    }
}
