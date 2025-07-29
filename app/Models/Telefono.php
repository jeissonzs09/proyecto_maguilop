<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Telefono extends Model
{
    protected $table = 'telefono';
    protected $primaryKey = 'TelefonoID';
    public $timestamps = false;

    protected $fillable = [
        'PersonaID',
        'EmpresaID',
        'Tipo',
        'Numero',
    ];

    // Relación inversa: cada teléfono pertenece a una persona
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'PersonaID', 'PersonaID');
    }
}
