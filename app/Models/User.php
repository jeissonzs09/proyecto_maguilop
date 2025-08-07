<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable implements CanResetPassword
{
    use Notifiable, CanResetPasswordTrait;

    protected $table = 'usuario';
    protected $primaryKey = 'UsuarioID';
    public $timestamps = false;

    protected $fillable = [
        'EmpleadoID',
        'NombreUsuario',
        'password',
        'Estado',
        'two_factor_secret',
        'email',
        'TipoUsuario', // ✅ Agregado del segundo código
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Campo personalizado para la contraseña.
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Devuelve el nombre del campo que se usará como identificador de login.
     */
    public function getAuthIdentifierName()
    {
        return 'UsuarioID';
    }

    /**
     * Campo que se usará para enviar correos de recuperación.
     */
    public function getEmailForPasswordReset()
    {
        return $this->email;
    }

    /**
     * Relación con la tabla roles (ajústala según tu estructura).
     */
    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'usuario_rol', 'UsuarioID', 'RolID');
    }

    /**
     * Mutador para hash automático de contraseña si se guarda directamente.
     */
    public function setContrasenaAttribute($value)
    {
        if (!empty($value) && Hash::needsRehash($value)) {
            $this->attributes['password'] = bcrypt($value);
        } else {
            $this->attributes['password'] = $value;
        }
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'EmpleadoID', 'EmpleadoID');
    }

    // ✅ Relación añadida con el modelo Persona
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'PersonaID', 'PersonaID');
    }
}

