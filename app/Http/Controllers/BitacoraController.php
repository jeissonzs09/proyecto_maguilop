<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bitacora;
use App\Models\Configuracion;
use App\Helpers\PermisosHelper;
use Barryvdh\DomPDF\Facade\Pdf;

class BitacoraController extends Controller
{
    public function index(Request $request)
    {
        if (!PermisosHelper::tienePermiso('Bitacora', 'ver')) {
            abort(403, 'No tienes permiso para ver esta sección.');
        }

        $query = Bitacora::query()->orderBy('BitacoraID', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('UsuarioID', 'LIKE', "%{$search}%")
                  ->orWhere('Accion', 'LIKE', "%{$search}%")
                  ->orWhere('TablaAfectada', 'LIKE', "%{$search}%")
                  ->orWhere('FechaAccion', 'LIKE', "%{$search}%")
                  ->orWhere('Descripcion', 'LIKE', "%{$search}%")
                  ->orWhere('DatosPrevios', 'LIKE', "%{$search}%")
                  ->orWhere('DatosNuevos', 'LIKE', "%{$search}%")
                  ->orWhere('Modulo', 'LIKE', "%{$search}%")
                  ->orWhere('Resultado', 'LIKE', "%{$search}%");
            });
        }

        $perPage = $request->input('per_page', 20);
        $bitacoras = $query->paginate($perPage)->appends([
            'per_page' => $perPage,
            'search'   => $request->search
        ]);

        $config = Configuracion::first();

        return view('bitacoras.index', compact('bitacoras', 'config', 'perPage'));
    }

    public function exportarPDF(Request $request)
    {
        $config = Configuracion::first();
        if (!$config || !$config->bitacora_activa) {
            return redirect()->route('bitacoras.index')
                             ->with('error', 'La bitácora está desactivada. No se puede exportar.');
        }

        $query = Bitacora::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('UsuarioID', 'LIKE', "%{$search}%")
                  ->orWhere('Accion', 'LIKE', "%{$search}%")
                  ->orWhere('TablaAfectada', 'LIKE', "%{$search}%")
                  ->orWhere('FechaAccion', 'LIKE', "%{$search}%")
                  ->orWhere('Descripcion', 'LIKE', "%{$search}%")
                  ->orWhere('DatosPrevios', 'LIKE', "%{$search}%")
                  ->orWhere('DatosNuevos', 'LIKE', "%{$search}%")
                  ->orWhere('Modulo', 'LIKE', "%{$search}%")
                  ->orWhere('Resultado', 'LIKE', "%{$search}%");
            });
        }

       $bitacoras = $query->orderBy('BitacoraID','desc')->get();
$pdf = Pdf::loadView('bitacoras.pdf', compact('bitacoras'))
          ->setPaper('a4', 'landscape');
return $pdf->download('bitacora.pdf');


    }

    public function exportarExcel(Request $request)
    {
        $query = Bitacora::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('UsuarioID', 'LIKE', "%{$search}%")
                  ->orWhere('Accion', 'LIKE', "%{$search}%")
                  ->orWhere('TablaAfectada', 'LIKE', "%{$search}%")
                  ->orWhere('FechaAccion', 'LIKE', "%{$search}%")
                  ->orWhere('Descripcion', 'LIKE', "%{$search}%")
                  ->orWhere('DatosPrevios', 'LIKE', "%{$search}%")
                  ->orWhere('DatosNuevos', 'LIKE', "%{$search}%")
                  ->orWhere('Modulo', 'LIKE', "%{$search}%")
                  ->orWhere('Resultado', 'LIKE', "%{$search}%");
            });
        }

        $bitacoras = $query->orderBy('BitacoraID','desc')->get();

        $filename = 'bitacoras.csv';
        $handle = fopen($filename, 'w+');
        fputcsv($handle, ['ID', 'UsuarioID', 'Accion', 'TablaAfectada', 'FechaAccion', 'Descripcion', 'DatosPrevios','DatosNuevos','Modulo', 'Resultado']);

        foreach ($bitacoras as $b) {
            fputcsv($handle, [
                $b->BitacoraID,
                $b->UsuarioID,
                $b->Accion,
                $b->TablaAfectada,
                $b->FechaAccion,
                $b->Descripcion,
                $b->DatosPrevios,
                $b->DatosNuevos,
                $b->Modulo,
                $b->Resultado,
                $b->FechaAccion
            ]);
        }

        fclose($handle);

        return response()->download($filename)->deleteFileAfterSend(true);
    }

    public function destroyWithPDF()
    {
        $config = Configuracion::first();
        if (!$config || !$config->bitacora_activa) {
            return redirect()->route('bitacoras.index')->with('error', 'La bitácora está desactivada. No se puede eliminar.');
        }

        $bitacoras = Bitacora::orderBy('BitacoraID', 'desc')->get();
        $pdf = Pdf::loadView('bitacoras.pdf', compact('bitacoras'))
    ->setPaper('a4', 'landscape')
    ->setOption([
        'isHtml5ParserEnabled' => true,
        'isRemoteEnabled' => true,
    ]);

        $filename = 'bitacora_completa_'.now()->format('Ymd_His').'.pdf';

        Bitacora::truncate();

        return $pdf->download($filename);
    }

    public function toggleBitacora()
    {
        $config = Configuracion::first() ?? new Configuracion();
        $config->bitacora_activa = !$config->bitacora_activa;
        $config->save();

        $estado = $config->bitacora_activa ? 'activada' : 'desactivada';
        return redirect()->route('bitacoras.index')->with('success', "Bitácora $estado correctamente.");
    }

    public function show($id)
    {
        return redirect()->route('bitacoras.index'); // Evita errores de Route::resource
    }
}