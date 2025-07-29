<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold">🧾 Nueva Factura</h2>
    </x-slot>

    <div class="max-w-5xl mx-auto mt-6 bg-white p-6 rounded shadow">
        <form action="{{ route('facturas.store') }}" method="POST" id="factura-form">
            @csrf

            {{-- Cliente --}}
            <div class="mb-4">
                <label class="block font-bold text-gray-700 mb-1">Cliente</label>
                <select name="ClienteID" class="w-full border rounded px-3 py-2" required>
                    <option value="">Seleccione un cliente</option>
                    @foreach ($clientes as $cliente)
                        <option value="{{ $cliente->ClienteID }}">{{ $cliente->NombreCliente }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Detalles de la factura --}}
            <div class="mb-4">
                <h3 class="text-lg font-semibold mb-2">Detalles de la Factura</h3>

                <table class="w-full text-sm" id="detalles-table">
                    <thead>
                        <tr class="bg-gray-100 text-left">
                            <th class="p-2">Producto</th>
                            <th class="p-2">Cantidad</th>
                            <th class="p-2">Precio Unitario</th>
                            <th class="p-2">Subtotal</th>
                            <th class="p-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="p-2">
                                <select name="detalles[0][ProductoID]" class="w-full border rounded px-2 py-1 producto" required>
                                    <option value="">--</option>
                                    @foreach ($productos as $producto)
                                        <option value="{{ $producto->ProductoID }}" data-precio="{{ $producto->PrecioVenta }}">
                                            {{ $producto->NombreProducto }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="p-2"><input type="number" name="detalles[0][Cantidad]" class="w-full border rounded cantidad px-2 py-1" min="1" required></td>
                            <td class="p-2"><input type="number" step="0.01" name="detalles[0][PrecioUnitario]" class="w-full border rounded precio px-2 py-1" required></td>
                            <td class="p-2"><input type="number" step="0.01" name="detalles[0][Subtotal]" class="w-full border rounded subtotal px-2 py-1 bg-gray-100" readonly></td>
                            <td class="p-2 text-center"><button type="button" class="text-red-600 remove-row">✖</button></td>
                        </tr>
                    </tbody>
                </table>

                <button type="button" class="mt-3 text-blue-600" id="add-row">+ Agregar Detalle</button>
            </div>

            {{-- Total --}}
            <div class="mb-4">
                <label class="block font-bold text-gray-700 mb-1">Total (Lps.)</label>
                <input type="number" step="0.01" name="Total" id="total" class="w-full border rounded px-3 py-2 bg-gray-100" readonly>
            </div>

            {{-- Botones --}}
            <div class="flex justify-between mt-6">
                <a href="{{ route('facturas.index') }}"
                    class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                    Cancelar
                </a>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-flex items-center gap-2">
                    Guardar Factura
                </button>
            </div>
        </form>
    </div>

    {{-- Script para dinámica --}}
    <script>
        let rowIdx = 1;

        document.getElementById('add-row').addEventListener('click', function () {
            const table = document.querySelector('#detalles-table tbody');
            const newRow = table.rows[0].cloneNode(true);

            newRow.querySelectorAll('input, select').forEach(el => {
                if (el.name.includes('[ProductoID]')) el.name = `detalles[${rowIdx}][ProductoID]`;
                if (el.name.includes('[Cantidad]')) el.name = `detalles[${rowIdx}][Cantidad]`;
                if (el.name.includes('[PrecioUnitario]')) el.name = `detalles[${rowIdx}][PrecioUnitario]`;
                if (el.name.includes('[Subtotal]')) el.name = `detalles[${rowIdx}][Subtotal]`;

                el.value = '';
                if (el.classList.contains('subtotal')) el.readOnly = true;
            });

            table.appendChild(newRow);
            rowIdx++;
        });

        document.addEventListener('input', function (e) {
            if (e.target.classList.contains('cantidad') || e.target.classList.contains('precio')) {
                const row = e.target.closest('tr');
                const cantidad = parseFloat(row.querySelector('.cantidad').value) || 0;
                const precio = parseFloat(row.querySelector('.precio').value) || 0;
                row.querySelector('.subtotal').value = (cantidad * precio).toFixed(2);
                actualizarTotal();
            }
        });

        document.addEventListener('change', function (e) {
            if (e.target.classList.contains('producto')) {
                const precio = e.target.selectedOptions[0].getAttribute('data-precio');
                const row = e.target.closest('tr');
                row.querySelector('.precio').value = precio || 0;
                row.querySelector('.cantidad').value = 1;
                row.querySelector('.subtotal').value = parseFloat(precio || 0).toFixed(2);
                actualizarTotal();
            }
        });

        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-row')) {
                const row = e.target.closest('tr');
                if (document.querySelectorAll('#detalles-table tbody tr').length > 1) {
                    row.remove();
                    actualizarTotal();
                }
            }
        });

        function actualizarTotal() {
            let total = 0;
            document.querySelectorAll('.subtotal').forEach(input => {
                total += parseFloat(input.value) || 0;
            });
            document.getElementById('total').value = total.toFixed(2);
        }
    </script>
</x-app-layout>

