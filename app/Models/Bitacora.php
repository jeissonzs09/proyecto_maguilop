<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Configuracion;

class Bitacora extends Model
{
    // Nombre de la tabla
    protected $table = 'bitacora'; // asegúrate que el nombre coincide con tu BD

    // Clave primaria
    protected $primaryKey = 'BitacoraID';

    // No usar timestamps automáticos (created_at/updated_at)
    public $timestamps = false;

    // Columnas que se pueden asignar masivamente
    protected $fillable = [
        'UsuarioID',       // ID del usuario, null si no se identifica
        'Accion',          // Qué acción hizo (Inicio sesión, Logout, Intento fallido…)
        'TablaAfectada',   // Si aplica, tabla del sistema afectada
        'FechaAccion',     // Fecha y hora del evento
        'Descripcion',     // Detalle (IP, user agent, intentos, etc.)
        'DatosPrevios',    // Datos anteriores si aplica
        'DatosNuevos',     // Datos nuevos si aplica
        'Modulo',          // Módulo del sistema (Autenticación, Productos…)
        'Resultado',       // Éxito / Error
    ];

    /**
     * Opcional: método helper para crear un registro rápido de bitácora.
     */
    public static function registrar($usuarioId, $accion, $descripcion, $resultado = 'Éxito', $tabla = null, $modulo = 'Autenticación', $datosPrevios = null, $datosNuevos = null)
{
    $config = Configuracion::first();

    // Si no existe registro o la bitácora está desactivada, no registrar
    if (!$config || !$config->bitacora_activa) {
        return false;
    }

    return self::create([
        'UsuarioID'     => $usuarioId,
        'Accion'        => $accion,
        'TablaAfectada' => $tabla,
        'FechaAccion'   => now(),
        'Descripcion'   => $descripcion,
        'DatosPrevios'  => $datosPrevios,
        'DatosNuevos'   => $datosNuevos,
        'Modulo'        => $modulo,
        'Resultado'     => $resultado,
    ]);
}
}