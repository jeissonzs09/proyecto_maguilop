<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cai;

class CaiController extends Controller
{
    public function index()
    {
        $cais = Cai::orderByDesc('id')->get();
        return view('cai.index', compact('cais'));
    }

    // Si quieres añadir la vista de creación:
    public function create()
    {
        return view('cai.create');
    }

    public function store(Request $request)
{
    $request->validate([
        'codigo' => 'required|string|size:37|unique:cai,codigo',
        'rango_inicial' => 'required|string|regex:/^\d{3}-\d{3}-\d{2}-\d{8}$/',
        'rango_final' => 'required|string|regex:/^\d{3}-\d{3}-\d{2}-\d{8}$/',
        'fecha_autorizacion' => 'required|date',
        'fecha_limite_emision' => 'required|date|after_or_equal:fecha_autorizacion',
    ]);

    // Convertir correlativo a número
    $inicio = intval(substr($request->rango_inicial, -8));
    $fin = intval(substr($request->rango_final, -8));

    if ($inicio >= $fin) {
        return back()->with('error', 'El rango inicial debe ser menor que el rango final.')->withInput();
    }

    // Validar que no se traslape con otros CAI existentes
    $prefijoNuevo = substr($request->rango_inicial, 0, 10);

    $traslape = \App\Models\Cai::where('rango_inicial', 'like', "$prefijoNuevo%")
        ->orWhere('rango_final', 'like', "$prefijoNuevo%")
        ->exists();

    if ($traslape) {
        return back()->with('error', 'Ya existe un CAI con ese prefijo de rango.')->withInput();
    }

    // Guardar
    Cai::create([
        'codigo' => $request->codigo,
        'rango_inicial' => $request->rango_inicial,
        'rango_final' => $request->rango_final,
        'fecha_autorizacion' => $request->fecha_autorizacion,
        'fecha_limite_emision' => $request->fecha_limite_emision,
        'facturas_emitidas' => 0,
    ]);

    return redirect()->route('cai.index')->with('success', 'CAI registrado correctamente.');
}

}