<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BackupController extends Controller
{
    public function index()
    {
        $backups = DB::table('backup')
            ->join('usuario', 'usuario.UsuarioID', '=', 'backup.UsuarioID')
            ->select('backup.*', 'usuario.NombreUsuario as usuario')
            ->orderByDesc('BackupID')
            ->get();

        return view('backups.index', compact('backups'));
    }

    public function store(Request $request)
{
    $descripcion = $request->input('descripcion');
    $fecha = now();
    $usuarioID = Auth::user()->UsuarioID;

    // Nombre y ruta del archivo
    $nombreArchivo = "backup_" . $fecha->format('Ymd_His') . ".sql";
    $ruta = storage_path("app/public/backups/$nombreArchivo");

    // Crea la carpeta si no existe
    if (!file_exists(dirname($ruta))) {
        mkdir(dirname($ruta), 0755, true);
    }

    // Ruta al ejecutable mysqldump
    $mysqldumpPath = 'C:\xampp\mysql\bin\mysqldump.exe';

    // Excluir tabla 'backup' al hacer el respaldo
    $comando = sprintf(
        '"%s" -u%s %s --ignore-table=%s.backup > "%s"',
        $mysqldumpPath,
        env('DB_USERNAME'),
        env('DB_DATABASE'),
        env('DB_DATABASE'),
        $ruta
    );

    // Ejecutar el comando
    $output = null;
    $returnCode = null;
    exec($comando, $output, $returnCode);

    if ($returnCode !== 0) {
        dd("❌ Error ejecutando mysqldump. Código: $returnCode", $output);
    }

    // Insertar registro en la tabla backup
    DB::table('backup')->insert([
        'UsuarioID'     => $usuarioID,
        'FechaBackup'   => $fecha,
        'Descripcion'   => $descripcion,
        'NombreArchivo' => $nombreArchivo,
        'RutaArchivo'   => "storage/backups/$nombreArchivo",
        'TamanoMB'      => round(filesize($ruta) / 1048576, 2),
    ]);

    return redirect()->back()->with('success', 'Backup creado correctamente.');
}






    public function restore($id)
{
    $backup = DB::table('backup')->where('BackupID', $id)->first();

    if (!$backup) {
        return back()->with('error', 'Backup no encontrado.');
    }

    $ruta = storage_path("app/public/backups/" . $backup->NombreArchivo);

    if (!file_exists($ruta)) {
        return back()->with('error', 'El archivo de respaldo no existe.');
    }

    // Comando para restaurar usando mysql
    $comando = sprintf(
        '"%s" -u%s %s %s < "%s"',
        "C:\\xampp\\mysql\\bin\\mysql.exe", // ruta absoluta al ejecutable mysql
        env('DB_USERNAME'),
        env('DB_PASSWORD') ? '-p' . env('DB_PASSWORD') : '',
        env('DB_DATABASE'),
        $ruta
    );

    // Ejecutar restauración
    $salida = null;
    $codigoSalida = null;
    exec($comando, $salida, $codigoSalida);

    if ($codigoSalida !== 0) {
        return back()->with('error', 'Error al restaurar la base de datos.');
    }

    return back()->with('success', '¡Base de datos restaurada con éxito!');
}

}