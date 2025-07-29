<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold">🛒 Nuevo Detalle de Compra</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto mt-6 bg-white p-6 rounded shadow">
        <form action="{{ route('detallecompras.store') }}" method="POST">
            @csrf

            {{-- Select Compra --}}
            <div class="mb-4">
                <label for="CompraID" class="block text-gray-700 font-bold mb-2">Compra</label>
                <select name="CompraID" id="CompraID" class="w-full border rounded px-3 py-2" required>
                    <option value="">-- Seleccione una compra --</option>
                    @foreach ($compras as $compra)
                        <option value="{{ $compra->CompraID }}" {{ old('CompraID') == $compra->CompraID ? 'selected' : '' }}>
                            Compra #{{ $compra->CompraID }} - {{ \Carbon\Carbon::parse($compra->FechaCompra)->format('d/m/Y') }} - {{ $compra->Estado }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Select Producto --}}
            <div class="mb-4">
                <label for="ProductoID" class="block text-gray-700 font-bold mb-2">Producto</label>
                <select name="ProductoID" id="ProductoID" class="w-full border rounded px-3 py-2" required>
                    <option value="">-- Seleccione un producto --</option>
                    @foreach ($productos as $producto)
                        <option value="{{ $producto->ProductoID }}" {{ old('ProductoID') == $producto->ProductoID ? 'selected' : '' }}>
                            {{ $producto->NombreProducto }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Cantidad --}}
            <div class="mb-4">
                <label for="Cantidad" class="block text-gray-700 font-bold mb-2">Cantidad</label>
                <input type="number" name="Cantidad" id="Cantidad" value="{{ old('Cantidad') }}" min="1"
                    class="w-full border rounded px-3 py-2" required oninput="calculateSubtotal()">
            </div>

            {{-- Precio Unitario --}}
            <div class="mb-4">
                <label for="PrecioUnitario" class="block text-gray-700 font-bold mb-2">Precio Unitario</label>
                <input type="number" step="0.01" name="PrecioUnitario" id="PrecioUnitario" value="{{ old('PrecioUnitario') }}" min="0"
                    class="w-full border rounded px-3 py-2" required oninput="calculateSubtotal()">
            </div>

            {{-- Subtotal (readonly) --}}
            <div class="mb-4">
                <label for="Subtotal" class="block text-gray-700 font-bold mb-2">Subtotal</label>
                <input type="number" step="0.01" name="Subtotal" id="Subtotal" value="{{ old('Subtotal') }}"
                    class="w-full border rounded px-3 py-2" readonly>
            </div>

            <div class="flex justify-between">
                <a href="{{ route('detallecompras.index') }}"
                    class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                    ❌ Cancelar
                </a>
                <button type="submit"
                        style="background-color: #2563eb; color: white; font-weight: bold; padding: 10px 20px; border-radius: 8px; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); border: none;">
                    💾 Guardar Detalle
                </button>
            </div>
        </form>
    </div>

    <script>
        function calculateSubtotal() {
            const cantidad = parseFloat(document.getElementById('Cantidad').value) || 0;
            const precioUnitario = parseFloat(document.getElementById('PrecioUnitario').value) || 0;
            const subtotal = cantidad * precioUnitario;
            document.getElementById('Subtotal').value = subtotal.toFixed(2);
        }
        window.onload = calculateSubtotal;
    </script>
</x-app-layout>