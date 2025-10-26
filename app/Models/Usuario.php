<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuario';
    protected $primaryKey = 'UsuarioID';
    public $timestamps = false;

    protected $fillable = [
        'NombreUsuario',
        'CorreoElectronico',
        'Contrasena',
        'TipoUsuario',
        'EmpleadoID',
        'Estado',
        'PrimerAcceso',
        'UsuarioRegistro',
        'FechaRegistro',
        'FechaCreacion',
        'FechaVencimiento',
        'Foto', // Nueva columna para la foto
    ];

    protected $hidden = ['Contrasena', 'remember_token'];

    protected $casts = [
        'FechaRegistro' => 'datetime',
        'FechaCreacion' => 'datetime',
        'FechaVencimiento' => 'date',
    ];

    // Devuelve la URL de la foto de perfil o una imagen por defecto
    public function getFotoPerfilUrlAttribute()
    {
        return $this->FotoPerfil 
            ? Storage::url($this->FotoPerfil)
            : asset('images/default-avatar.png');
    }

    // Para el login, Laravel usará este campo como password
    public function getAuthPassword()
    {
        return $this->Contrasena;
    }

    // Para el broker de contraseñas (reset link) usa tu campo de correo
    public function getEmailForPasswordReset()
    {
        return $this->CorreoElectronico;
    }

    // Para notificaciones por mail
    public function routeNotificationForMail($notification = null)
    {
        return $this->CorreoElectronico;
    }

    public function persona()
    {
        return $this->belongsTo(\App\Models\Persona::class, 'PersonaID', 'PersonaID');
    }

    // app/Models/Usuario.php
public function getNameAttribute()
{
    return $this->NombreUsuario;
}

}