<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;

class User extends Authenticatable implements CanResetPassword
{
    use Notifiable, CanResetPasswordTrait;

    protected $table = 'usuario';
    protected $primaryKey = 'UsuarioID';
    public $timestamps = false;

  protected $fillable = [
    'EmpleadoID', // 👈 Asegúrate de incluir esto
    'NombreUsuario',
    'CorreoElectronico',
    'TipoUsuario',
    'Contrasena',
    'Estado',
];


    protected $hidden = [
        'Contrasena',
    ];

    public function getAuthPassword()
    {
        return $this->Contrasena;
    }

    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'usuario_rol', 'UsuarioID', 'RolID');
    }

    // ✅ Agregar esta relación con el modelo Empleado
  // User.php
public function empleado()
{
    return $this->belongsTo(Empleado::class, 'EmpleadoID', 'EmpleadoID');
}

public function persona()
{
    return $this->belongsTo(Persona::class, 'PersonaID', 'PersonaID');
}


}

