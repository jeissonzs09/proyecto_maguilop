<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use App\Helpers\PermisosHelper;
use App\Models\Proveedor;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Helpers\BitacoraHelper;
use Illuminate\Validation\Rule;

class ProductoController extends Controller
{
    public function index(Request $request)
{
    if (!PermisosHelper::tienePermiso('Productos', 'ver')) {
        abort(403, 'No tienes permiso para ver esta sección.');
    }

    $query = Producto::with('proveedor')->orderBy('ProductoID', 'desc');

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where('NombreProducto', 'LIKE', "%{$search}%")
              ->orWhere('Descripcion', 'LIKE', "%{$search}%")
              ->orWhere('PrecioCompra', 'LIKE', "%{$search}%")
              ->orWhere('PrecioVenta', 'LIKE', "%{$search}%")
              ->orWhere('Stock', 'LIKE', "%{$search}%")
              ->orWhereHas('proveedor', fn($q) => $q->where('Descripcion', 'LIKE', "%{$search}%"));
    }

    $productos = $query->paginate(5);
    $proveedores = Proveedor::all();

    return view('producto.index', compact('productos', 'proveedores'));
}


    public function create()
    {
        $proveedores = Proveedor::all();
        return view('producto.create', compact('proveedores'));
    }

    public function store(Request $request)
    {
        // Limpieza previa
        $clean = function ($v) {
            if ($v === null) return $v;
            $v = trim($v);
            return preg_replace('/\s+/', ' ', $v);
        };
        $request->merge([
            'NombreProducto' => $clean($request->input('NombreProducto')),
            'Descripcion'    => $clean($request->input('Descripcion')),
        ]);

        // Detectar nombres reales de tablas desde los modelos
        $productoTable  = (new Producto)->getTable();
        $proveedorTable = (new Proveedor)->getTable();

        $validated = $request->validate(
            [
                'NombreProducto' => [
                    'required',
                    'string',
                    'min:3',
                    'max:60',
                    'regex:/^[A-Za-z ]+$/',
                    Rule::unique($productoTable, 'NombreProducto'),
                ],
                'Descripcion' => [
                    'required',
                    'string',
                    'min:10',
                    'max:200',
                    'regex:/^[A-Za-z0-9 ]+$/',
                ],
                'PrecioCompra' => ['required', 'numeric', 'min:0'],
                'PrecioVenta'  => ['required', 'numeric', 'gte:PrecioCompra'],
                'Stock'        => ['required', 'integer', 'min:0'],
                'ProveedorID'  => ['required', 'integer', "exists:{$proveedorTable},ProveedorID"],
            ],
            [
                'NombreProducto.required' => 'El nombre es obligatorio.',
                'NombreProducto.min'      => 'El nombre debe tener al menos 3 caracteres.',
                'NombreProducto.max'      => 'El nombre no debe exceder 60 caracteres.',
                'NombreProducto.regex'    => 'El nombre solo puede contener letras A-Z y espacios.',
                'NombreProducto.unique'   => 'Ya existe un producto con ese nombre.',

                'Descripcion.required' => 'La descripcion es obligatoria.',
                'Descripcion.min'      => 'La descripcion debe tener al menos 10 caracteres.',
                'Descripcion.max'      => 'La descripcion no debe exceder 200 caracteres.',
                'Descripcion.regex'    => 'La descripcion solo puede contener letras, numeros y espacios.',

                'PrecioVenta.gte'      => 'El precio de venta no puede ser menor que el de compra.',
                'ProveedorID.exists'   => 'El proveedor seleccionado no existe.',
            ]
        );

        $producto = Producto::create([
            'NombreProducto' => $validated['NombreProducto'],
            'Descripcion'    => $validated['Descripcion'],
            'PrecioCompra'   => $validated['PrecioCompra'],
            'PrecioVenta'    => $validated['PrecioVenta'],
            'Stock'          => $validated['Stock'],
            'ProveedorID'    => $validated['ProveedorID'],
        ]);

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
        // Limpieza previa
        $clean = function ($v) {
            if ($v === null) return $v;
            $v = trim($v);
            return preg_replace('/\s+/', ' ', $v);
        };
        $request->merge([
            'NombreProducto' => $clean($request->input('NombreProducto')),
            'Descripcion'    => $clean($request->input('Descripcion')),
        ]);

        $productoTable  = (new Producto)->getTable();
        $proveedorTable = (new Proveedor)->getTable();

        $validated = $request->validate(
            [
                'NombreProducto' => [
                    'required',
                    'string',
                    'min:3',
                    'max:60',
                    'regex:/^[A-Za-z ]+$/',
                    Rule::unique($productoTable, 'NombreProducto')->ignore($id, 'ProductoID'),
                ],
                'Descripcion' => [
                    'required',
                    'string',
                    'min:10',
                    'max:200',
                    'regex:/^[A-Za-z0-9 ]+$/',
                ],
                'PrecioCompra' => ['required', 'numeric', 'min:0'],
                'PrecioVenta'  => ['required', 'numeric', 'gte:PrecioCompra'],
                'Stock'        => ['required', 'integer', 'min:0'],
                'ProveedorID'  => ['required', 'integer', "exists:{$proveedorTable},ProveedorID"],
            ],
            [
                'NombreProducto.required' => 'El nombre es obligatorio.',
                'NombreProducto.min'      => 'El nombre debe tener al menos 3 caracteres.',
                'NombreProducto.max'      => 'El nombre no debe exceder 60 caracteres.',
                'NombreProducto.regex'    => 'El nombre solo puede contener letras A-Z y espacios.',
                'NombreProducto.unique'   => 'Ya existe un producto con ese nombre.',

                'Descripcion.required' => 'La descripcion es obligatoria.',
                'Descripcion.min'      => 'La descripcion debe tener al menos 10 caracteres.',
                'Descripcion.max'      => 'La descripcion no debe exceder 200 caracteres.',
                'Descripcion.regex'    => 'La descripcion solo puede contener letras, numeros y espacios.',

                'PrecioVenta.gte'      => 'El precio de venta no puede ser menor que el de compra.',
                'ProveedorID.exists'   => 'El proveedor seleccionado no existe.',
            ]
        );

        $producto = Producto::findOrFail($id);
        $anterior = $producto->toArray();

        $producto->update([
            'NombreProducto' => $validated['NombreProducto'],
            'Descripcion'    => $validated['Descripcion'],
            'PrecioCompra'   => $validated['PrecioCompra'],
            'PrecioVenta'    => $validated['PrecioVenta'],
            'Stock'          => $validated['Stock'],
            'ProveedorID'    => $validated['ProveedorID'],
        ]);

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
        $registroEliminado = $producto->toArray();

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

            $query->where(function ($q) use ($search) {
                $q->where('NombreProducto', 'LIKE', "%{$search}%")
                  ->orWhere('Descripcion', 'LIKE', "%{$search}%")
                  ->orWhere('PrecioCompra', 'LIKE', "%{$search}%")
                  ->orWhere('PrecioVenta', 'LIKE', "%{$search}%")
                  ->orWhere('Stock', 'LIKE', "%{$search}%")
                  ->orWhereHas('proveedor', function ($qp) use ($search) {
                      $qp->where('Descripcion', 'LIKE', "%{$search}%");
                  });
            });
        }

        $productos = $query->get();

        $pdf = Pdf::loadView('producto.pdf', compact('productos'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('productos.pdf');
    }
}