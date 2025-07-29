<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Reparacion;


class Cliente extends Model
{
    protected $table = 'cliente'; // Singular
    protected $primaryKey = 'ClienteID';
    public $timestamps = false;

    protected $fillable = [
        'NombreCliente',
        'PersonaID',
        'Categoria',
        'FechaRegistro',
        'Estado',
        'Notas',
    ];

    // En App\Models\Cliente.php

public function reparaciones()
{
    return $this->hasMany(Reparacion::class, 'ClienteID');
}

public function persona()
{
    return $this->belongsTo(Persona::class, 'PersonaID', 'PersonaID');
}

}
