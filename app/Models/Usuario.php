<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuario';
    protected $primaryKey = 'UsuarioID';
    public $timestamps = false;

    protected $fillable = [
        'NombreUsuario',
        'CorreoElectronico',
        'Contraseña',
        'TipoUsuario',
        'EmpleadoID',
    ];

    protected $hidden = ['Contraseña', 'remember_token'];

    public function getAuthPassword()
    {
        return $this->Contraseña;
    }
}

