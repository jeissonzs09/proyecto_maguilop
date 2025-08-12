<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use App\Helpers\PermisosHelper;
use App\Models\Proveedor;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Helpers\BitacoraHelper;
use Illuminate\Support\Facades\Storage;
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
                  ->orWhere('Codigo', 'LIKE', "%{$search}%")
                  ->orWhere('Area', 'LIKE', "%{$search}%")
                  ->orWhere('PrecioCompra', 'LIKE', "%{$search}%")
                  ->orWhere('PrecioVenta', 'LIKE', "%{$search}%")
                  ->orWhere('Stock', 'LIKE', "%{$search}%")
                  ->orWhereHas('proveedor', fn($q) => $q->where('Descripcion', 'LIKE', "%{$search}%"));
        }

        $productos = $query->paginate(10);
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
                'Codigo'         => ['nullable', 'string', 'max:30', "unique:{$productoTable},Codigo"],
                'NombreProducto' => [
                    'required', 'string', 'min:3', 'max:60',
                    'regex:/^[A-Za-z ]+$/',
                    Rule::unique($productoTable, 'NombreProducto'),
                ],
                'Descripcion'    => ['required', 'string', 'min:3', 'max:200', 'regex:/^[A-Za-z0-9 ]+$/'],
                'Area'           => ['required', 'in:Electronica,Refrigeracion'],
                'PrecioCompra'   => ['required', 'numeric', 'min:0'],
                'PrecioVenta'    => ['required', 'numeric', 'gte:PrecioCompra'],
                'Stock'          => ['required', 'integer', 'min:0'],
                'ProveedorID'    => ['required', 'integer', "exists:{$proveedorTable},ProveedorID"],
                'Foto'           => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            ],
            [
                'Area.required' => 'El área es obligatoria.',
                'Area.in'       => 'El área debe ser Electronica o Refrigeracion.',
                'Foto.image'    => 'El archivo debe ser una imagen.',
                'Foto.mimes'    => 'Formato no permitido. Usa JPG, PNG o WEBP.',
                'Foto.max'      => 'La imagen no debe superar 2MB.',
            ]
        );

        // Generar código si no vino
        if (empty($validated['Codigo'])) {
            $validated['Codigo'] = 'PRD-' . now()->format('ymd') . '-' . str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        }

        if ($request->hasFile('Foto')) {
            $validated['Foto'] = $request->file('Foto')->store('productos', 'public');
        }

        $producto = Producto::create($validated);

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
        $producto = Producto::findOrFail($id);

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
                'Codigo'         => ['required', 'string', 'max:30', "unique:{$productoTable},Codigo,{$producto->ProductoID},ProductoID"],
                'NombreProducto' => [
                    'required', 'string', 'min:3', 'max:60',
                    'regex:/^[A-Za-z ]+$/',
                    Rule::unique($productoTable, 'NombreProducto')->ignore($id, 'ProductoID'),
                ],
                'Descripcion'    => ['required', 'string', 'min:3', 'max:200', 'regex:/^[A-Za-z0-9 ]+$/'],
                'Area'           => ['required', 'in:Electronica,Refrigeracion'],
                'PrecioCompra'   => ['required', 'numeric', 'min:0'],
                'PrecioVenta'    => ['required', 'numeric', 'gte:PrecioCompra'],
                'Stock'          => ['required', 'integer', 'min:0'],
                'ProveedorID'    => ['required', 'integer', "exists:{$proveedorTable},ProveedorID"],
                'Foto'           => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            ]
        );

        if ($request->hasFile('Foto')) {
            if ($producto->Foto && Storage::disk('public')->exists($producto->Foto)) {
                Storage::disk('public')->delete($producto->Foto);
            }
            $validated['Foto'] = $request->file('Foto')->store('productos', 'public');
        }

        $anterior = $producto->toArray();
        $producto->update($validated);

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

        if ($producto->detallePedidos()->exists()) {
            return redirect()->route('producto.index')->with('error', 'No se puede eliminar este producto porque está asociado a un pedido.');
        }

        if ($producto->Foto && Storage::disk('public')->exists($producto->Foto)) {
            Storage::disk('public')->delete($producto->Foto);
        }

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
                  ->orWhere('Codigo', 'LIKE', "%{$search}%")
                  ->orWhere('Area', 'LIKE', "%{$search}%")
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