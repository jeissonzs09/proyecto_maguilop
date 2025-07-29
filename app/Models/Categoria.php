<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'categoria'; // Asegúrate que el nombre coincide con tu tabla real

    protected $primaryKey = 'CategoriaID'; // Cambia si tu llave primaria tiene otro nombre

    public $timestamps = false;

    protected $fillable = [
        'NombreCategoria', // Agrega los campos que uses en la tabla
    ];

    // Relación con productos
    public function productos()
    {
        return $this->hasMany(Producto::class, 'CategoriaID');
    }
}
