<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold flex items-center gap-2">
            <i class="fas fa-file-invoice"></i> Facturas
        </h2>
    </x-slot>

    @php
        $permisos = \App\Helpers\PermisosHelper::class;
    @endphp

    {{-- Contenedor con Alpine --}}
    <div x-data="{ openModal: false }" class="mb-6">

        {{-- Barra superior --}}
        <div class="flex justify-between items-center flex-wrap gap-3">

            {{-- Buscador --}}
            <div class="relative max-w-xs w-full">
                <input
                    type="text"
                    x-data="{ search: '{{ request('search') }}' }"
                    x-model="search"
                    @input.debounce.500="window.location.href = '?search=' + encodeURIComponent(search)"
                    placeholder="Buscar factura..."
                    class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:outline-none text-sm"
                />
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M21 21l-4.35-4.35m1.44-5.4a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            {{-- Botón Nueva factura --}}
            @if($permisos::tienePermiso('Factura', 'crear'))
                <button
                    @click="openModal = true"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow whitespace-nowrap"
                >
                    <i class="fas fa-plus"></i> Nueva factura
                </button>
            @endif
        </div>

        {{-- Modal --}}
        <div
            x-show="openModal"
            x-transition
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
            style="display: none;"
        >
            <div
                @click.away="openModal = false"
                class="bg-white rounded-lg shadow-lg max-w-5xl w-full p-6 relative max-h-[90vh] overflow-y-auto"
            >
                {{-- Botón cerrar --}}
                <button
                    @click="openModal = false"
                    class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 focus:outline-none"
                    aria-label="Cerrar modal"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                {{-- Título --}}
                <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                    <i class="fas fa-file-invoice"></i> Nueva Factura
                </h2>

                {{-- Formulario --}}
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

                    {{-- Empleado (solo mostrar, no editar) --}}
<div class="mb-4">
    <label class="block font-bold text-gray-700 mb-1">Empleado</label>
    <p class="border rounded px-3 py-2 bg-gray-100">
        {{ Auth::user()->empleado->persona->NombreCompleto ?? 'Empleado no asignado' }}
    </p>
</div>



                    {{-- Detalles --}}
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
                        <button
                            type="button"
                            @click="openModal = false"
                            class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded inline-flex items-center"
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-flex items-center gap-2"
                        >
                            Guardar Factura
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Tabla principal --}}
    <div class="overflow-x-auto bg-white rounded-lg shadow mt-4">
        <table class="min-w-full text-sm text-gray-800">
            <thead class="bg-orange-500 text-white text-sm uppercase">
                <tr>
                    <th class="px-4 py-3 text-center">Factura ID</th>
                    <th class="px-4 py-3 text-center">Cliente</th>
                    <th class="px-4 py-3 text-center">Empleado</th>
                    <th class="px-4 py-3 text-center">Fecha</th>
                    <th class="px-4 py-3 text-center">Total</th>
                    <th class="px-4 py-3 text-center">Producto</th>
                    <th class="px-4 py-3 text-center">Cantidad</th>
                    <th class="px-4 py-3 text-right">Precio Unitario</th>
                    <th class="px-4 py-3 text-right">Subtotal</th>
                    <th class="px-4 py-3 text-center">Acciones</th>
                    <th class="px-4 py-3 text-center">Estado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($facturas as $factura)
                    @foreach($factura->detalles as $detalle)
                        <tr class="transition {{ $factura->Estado === 'Cancelada' ? 'bg-red-100 text-red-700 font-semibold' : 'hover:bg-gray-50' }}">
                            @if ($loop->first)
                                <td class="px-4 py-2 text-center" rowspan="{{ $factura->detalles->count() }}">{{ $factura->FacturaID }}</td>
                                <td class="px-4 py-2 text-center" rowspan="{{ $factura->detalles->count() }}">{{ $factura->cliente->NombreCliente ?? '—' }}</td>
                                <td class="px-4 py-2 text-center" rowspan="{{ $factura->detalles->count() }}">{{ $factura->empleado->persona->NombreCompleto ?? '—' }}</td>
                                <td class="px-4 py-2 text-center" rowspan="{{ $factura->detalles->count() }}">{{ $factura->Fecha }}</td>
                                <td class="px-4 py-2 text-center" rowspan="{{ $factura->detalles->count() }}">L. {{ number_format($factura->Total, 2) }}</td>
                            @endif

                            <td class="px-4 py-2 text-center">{{ $detalle->producto->NombreProducto ?? '—' }}</td>
                            <td class="px-4 py-2 text-center">{{ $detalle->Cantidad }}</td>
                            <td class="px-4 py-2 text-right">L. {{ number_format($detalle->PrecioUnitario, 2) }}</td>
                            <td class="px-4 py-2 text-right">L. {{ number_format($detalle->Subtotal, 2) }}</td>

                            @if ($loop->first)
                                <td class="text-center align-middle" rowspan="{{ $factura->detalles->count() }}">
                                    <div class="flex justify-center items-center gap-2 h-full">
                                        @if($permisos::tienePermiso('Factura', 'eliminar') && $factura->Estado === 'Activa')
                                            <form action="{{ route('facturas.cancelar', $factura->FacturaID) }}" method="POST" onsubmit="return confirm('¿Cancelar esta factura?');">
                                                @csrf
                                                @method('PUT')
                                                <button class="bg-red-600 hover:bg-red-700 text-white p-2 rounded-circle" title="Cancelar">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            </form>
                                        @endif

                                        @if($permisos::tienePermiso('Factura', 'exportar'))
                                            <a href="{{ route('facturas.pdf', $factura->FacturaID) }}"
                                               class="bg-blue-700 hover:bg-blue-900 text-white p-2 rounded-circle"
                                               title="Generar PDF">
                                                <i class="fas fa-file-invoice"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-4 py-2 text-center align-middle" rowspan="{{ $factura->detalles->count() }}">
                                    {{ $factura->Estado }}
                                </td>
                            @endif
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $facturas->appends(['search' => request('search')])->links() }}
    </div>

    {{-- Scripts --}}
    {{-- Alpine.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    {{-- Script para dinámica filas y total --}}
    <script>
        let rowIdx = 1;

        document.addEventListener('DOMContentLoaded', () => {
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

</x-app-layout> 