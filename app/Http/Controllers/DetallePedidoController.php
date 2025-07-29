<?php

namespace App\Http\Controllers;

use App\Models\DetallePedido;
use Illuminate\Http\Request;

class DetallePedidoController extends Controller
{
    public function create($pedidoId)
    {
        return view('detalle_pedidos.create', compact('pedidoId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'PedidoID' => 'required|integer|exists:pedidos,PedidoID',
            'ProductoID' => 'required|integer',
            'Cantidad' => 'required|integer',
            'PrecioUnitario' => 'required|numeric',
            'Subtotal' => 'required|numeric',
        ]);

        DetallePedido::create($request->all());

        return redirect()->route('pedidos.index')->with('success', 'Detalle de pedido creado correctamente.');
    }

    public function edit($id)
    {
        $detalle = DetallePedido::findOrFail($id);
        return view('detalle_pedidos.edit', compact('detalle'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'PedidoID' => 'required|integer|exists:pedidos,PedidoID',
            'ProductoID' => 'required|integer',
            'Cantidad' => 'required|integer',
            'PrecioUnitario' => 'required|numeric',
            'Subtotal' => 'required|numeric',
        ]);

        $detalle = DetallePedido::findOrFail($id);
        $detalle->update($request->all());

        return redirect()->route('pedidos.index')->with('success', 'Detalle de pedido actualizado correctamente.');
    }

    public function destroy($id)
    {
        $detalle = DetallePedido::findOrFail($id);
        $detalle->delete();

        return redirect()->route('pedidos.index')->with('success', 'Detalle de pedido eliminado correctamente.');
    }
}