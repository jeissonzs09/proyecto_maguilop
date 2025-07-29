<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use App\Helpers\PermisosHelper;
use App\Models\Proveedor;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Helpers\BitacoraHelper;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        if (!PermisosHelper::tienePermiso('Productos', 'ver')) {
            abort(403, 'No tienes permiso para ver esta sección.');
        }

        $query = Producto::with('proveedor');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('NombreProducto', 'LIKE', "%{$search}%")
                  ->orWhere('Descripcion', 'LIKE', "%{$search}%")
                  ->orWhere('PrecioCompra', 'LIKE', "%{$search}%")
                  ->orWhere('PrecioVenta', 'LIKE', "%{$search}%")
                  ->orWhere('Stock', 'LIKE', "%{$search}%")
                  ->orWhereHas('proveedor', fn($q) => $q->where('ProveedorID', 'LIKE', "%{$search}%"));
        }

        $productos = $query->paginate(5);
        $proveedores = Proveedor::all(); // ✅ Asegurado

        return view('producto.index', compact('productos', 'proveedores'));
    }

    public function create()
    {
        $proveedores = Proveedor::all();
        return view('producto.create', compact('proveedores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'NombreProducto' => 'required|string|max:150',
            'Descripcion' => 'nullable|string|max:500',
            'PrecioCompra' => 'required|numeric|min:0',
            'PrecioVenta' => 'required|numeric|min:0',
            'Stock' => 'required|integer|min:0',
            'ProveedorID' => 'required|integer|exists:proveedor,ProveedorID',
        ]);

        $producto = Producto::create($request->only([
            'NombreProducto',
            'Descripcion',
            'PrecioCompra',
            'PrecioVenta',
            'Stock',
            'ProveedorID',
        ]));

        BitacoraHelper::registrar(
            'CREAR',
            'producto',
            'Se creó el producto: ' . $producto->NombreProducto,
            null,
            $producto->toArray(),
            'Módulo de Productos'
        );

        return redirect()->route('producto.index')->with('success', 'Producto creado correctamente.');
    }

    public function edit($id)
    {
        $producto = Producto::findOrFail($id);
        $proveedores = Proveedor::all();

        return view('producto.edit', compact('producto', 'proveedores'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'NombreProducto' => 'required|string|max:150',
            'Descripcion' => 'nullable|string|max:500',
            'PrecioCompra' => 'required|numeric|min:0',
            'PrecioVenta' => 'required|numeric|min:0',
            'Stock' => 'required|integer|min:0',
            'ProveedorID' => 'required|integer|exists:proveedor,ProveedorID',
        ]);

        $producto = Producto::findOrFail($id);
        $anterior = $producto->toArray();

        $producto->update($request->only([
            'NombreProducto',
            'Descripcion',
            'PrecioCompra',
            'PrecioVenta',
            'Stock',
            'ProveedorID',
        ]));

        BitacoraHelper::registrar(
            'ACTUALIZAR',
            'producto',
            'Se actualizó el producto ID: ' . $id,
            $anterior,
            $producto->toArray(),
            'Módulo de Productos'
        );

        return redirect()->route('producto.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy($id)
    {
        $producto = Producto::findOrFail($id);
        $registroEliminado = $producto->toArray(); // ✅ Definido

        $producto->delete();

        BitacoraHelper::registrar(
            'ELIMINAR',
            'producto',
            'Se eliminó el producto ID: ' . $id,
            $registroEliminado,
            null,
            'Módulo de Productos'
        );

        return redirect()->route('producto.index')->with('success', 'Producto eliminado correctamente.');
    }

    public function exportarPDF(Request $request)
    {
        $query = Producto::with('proveedor');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where('NombreProducto', 'LIKE', "%{$search}%")
                  ->orWhere('Descripcion', 'LIKE', "%{$search}%")
                  ->orWhere('PrecioCompra', 'LIKE', "%{$search}%")
                  ->orWhere('PrecioVenta', 'LIKE', "%{$search}%")
                  ->orWhere('Stock', 'LIKE', "%{$search}%")
                  ->orWhereHas('proveedor', fn($q) =>
                      $q->where('Descripcion', 'LIKE', "%{$search}%")
                  );
        }

        $productos = $query->get();

        $pdf = Pdf::loadView('producto.pdf', compact('productos'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('productos.pdf');
    }
}
