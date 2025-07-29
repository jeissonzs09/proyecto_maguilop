<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Telefono; // Importa el modelo Telefono

class Persona extends Model
{
    protected $table = 'persona';
    protected $primaryKey = 'PersonaID';
    public $timestamps = false;

    protected $fillable = [
        'Nombre',
        'Apellido',
        'FechaNacimiento',
        'Genero',
        'CorreoElectronico',
    ];

    // Relación con Telefonos (uno a muchos)
    public function telefonos()
    {
        return $this->hasMany(Telefono::class, 'PersonaID', 'PersonaID');
    }

    // Nombre completo (opcional)
    public function getNombreCompletoAttribute()
    {
        return "{$this->Nombre} {$this->Apellido}";
    }
}

