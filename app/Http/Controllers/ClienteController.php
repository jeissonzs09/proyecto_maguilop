<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Persona;
use App\Models\Bitacora; // Importar modelo bitacora
use Illuminate\Support\Facades\Auth;
use App\Helpers\PermisosHelper;
use Barryvdh\DomPDF\Facade\Pdf;

class ClienteController extends Controller
{
    public function index(Request $request)
{
    if (!PermisosHelper::tienePermiso('Clientes', 'ver')) {
        abort(403, 'No tienes permiso para ver esta sección.');
    }

    $query = Cliente::with('persona');

    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->where('NombreCliente', 'LIKE', "%{$search}%")
                ->orWhere('Categoria', 'LIKE', "%{$search}%")
                ->orWhere('Estado', 'LIKE', "%{$search}%")
                ->orWhereHas('persona', function ($q2) use ($search) {
                    $q2->where('Nombre', 'LIKE', "%{$search}%")
                        ->orWhere('Apellido', 'LIKE', "%{$search}%");
                });
        });
    }

$clientes = $query->orderBy('ClienteID', 'desc')->paginate(5);
    $personas = Persona::all();

    // Obtener el nombre del empleado del usuario logueado
    $usuario = Auth::user();
    $empleado = $usuario->empleado;
    $empleadoNombre = $empleado->Persona->NombreCompleto ?? 'Empleado desconocido';

    return view('clientes.index', compact('clientes', 'personas', 'empleadoNombre'));
}


    public function create()
    {
        if (!PermisosHelper::tienePermiso('Clientes', 'crear')) {
            abort(403);
        }

        $personas = Persona::all();
        return view('clientes.create', compact('personas'));
    }

public function store(Request $request)
{
    $request->validate([
    'NombreCliente' => 'required|string|max:100',
    'PersonaID' => 'required|exists:persona,PersonaID',
    'Categoria' => 'nullable|in:Regular,Premium,VIP',
    'FechaRegistro' => 'required|date',
    'Estado' => 'required|in:Activo,Inactivo',
    'Notas' => 'nullable|string',
]);

    $usuario = auth()->user();


    $cliente = Cliente::create([
    'NombreCliente' => $request->NombreCliente,
    'PersonaID' => $request->PersonaID,
    'Categoria' => $request->Categoria,
    'FechaRegistro' => $request->FechaRegistro,
    'Estado' => $request->Estado,
    'Notas' => $request->Notas,
    'EmpleadoID' => auth()->user()->empleado->EmpleadoID ?? null,
]);


    // Registrar en bitácora
    Bitacora::create([
        'UsuarioID' => Auth::id(),
        'Accion' => 'Crear',
        'TablaAfectada' => 'clientes',
        'FechaAccion' => now(),
        'Descripcion' => 'Cliente creado: ID ' . $cliente->ClienteID . ' - Nombre: ' . $cliente->NombreCliente,
        'DatosPrevios' => null,
        'DatosNuevos' => json_encode($cliente->toArray()),
        'Modulo' => 'Clientes',
        'Resultado' => 'Exitoso',
    ]);

    return redirect()->route('clientes.index')->with('success', 'Cliente creado correctamente.');
}


    public function edit($id)
    {
        if (!PermisosHelper::tienePermiso('Clientes', 'editar')) {
            abort(403);
        }

        $cliente = Cliente::with('persona')->findOrFail($id);
        $personas = Persona::all();
        return view('clientes.edit', compact('cliente', 'personas'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'NombreCliente' => 'required|string|max:100',
            'PersonaID' => 'required|exists:persona,PersonaID',
            'Categoria' => 'nullable|in:Regular,Premium,VIP',
            'FechaRegistro' => 'required|date',
            'Estado' => 'required|in:Activo,Inactivo',
            'Notas' => 'nullable|string',
        ]);

        // Validar que PersonaID no se duplique en otro cliente diferente
        if (Cliente::where('PersonaID', $request->PersonaID)->where('ClienteID', '!=', $id)->exists()) {
            return back()->withErrors(['PersonaID' => 'Esta persona ya está registrada como cliente.'])->withInput();
        }

        $cliente = Cliente::findOrFail($id);
        $datosPrevios = $cliente->toArray();

        $cliente->update($request->all());

        // Registrar en bitácora
        Bitacora::create([
            'UsuarioID' => Auth::id(),
            'Accion' => 'Actualizar',
            'TablaAfectada' => 'clientes',
            'FechaAccion' => now(),
            'Descripcion' => 'Cliente actualizado: ID ' . $cliente->ClienteID . ' - Nombre: ' . $cliente->NombreCliente,
            'DatosPrevios' => json_encode($datosPrevios),
            'DatosNuevos' => json_encode($cliente->toArray()),
            'Modulo' => 'Clientes',
            'Resultado' => 'Exitoso',
        ]);

        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy($id)
    {
        if (!PermisosHelper::tienePermiso('Clientes', 'eliminar')) {
            abort(403);
        }

        try {
            $cliente = Cliente::findOrFail($id);
            $datosPrevios = $cliente->toArray();

            $cliente->delete();

            // Registrar en bitácora
            Bitacora::create([
                'UsuarioID' => Auth::id(),
                'Accion' => 'Eliminar',
                'TablaAfectada' => 'clientes',
                'FechaAccion' => now(),
                'Descripcion' => 'Cliente eliminado: ID ' . $id . ' - Nombre: ' . $datosPrevios['NombreCliente'],
                'DatosPrevios' => json_encode($datosPrevios),
                'DatosNuevos' => null,
                'Modulo' => 'Clientes',
                'Resultado' => 'Exitoso',
            ]);

            return redirect()->route('clientes.index')->with('success', 'Cliente eliminado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('clientes.index')->with('error', 'No se puede eliminar el cliente: ' . $e->getMessage());
        }
    }

    // Método para exportar a PDF
    public function exportarPDF(Request $request)
    {
        if (!PermisosHelper::tienePermiso('Clientes', 'ver')) {
            abort(403, 'No tienes permiso para exportar esta sección.');
        }

        $query = Cliente::with('persona');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('NombreCliente', 'LIKE', "%{$search}%")
                    ->orWhere('Categoria', 'LIKE', "%{$search}%")
                    ->orWhere('Estado', 'LIKE', "%{$search}%")
                    ->orWhereHas('persona', function ($q2) use ($search) {
                        $q2->where('Nombre', 'LIKE', "%{$search}%")
                            ->orWhere('Apellido', 'LIKE', "%{$search}%");
                    });
            });
        }

        $clientes = $query->get();

        $pdf = Pdf::loadView('clientes.pdf', compact('clientes'))->setPaper('a4', 'portrait');
        return $pdf->download('clientes.pdf');
    }
}
