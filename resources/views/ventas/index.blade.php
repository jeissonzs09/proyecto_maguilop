<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold flex items-center gap-2">
            <i class="fas fa-shopping-cart"></i> Ventas
        </h2>
    </x-slot>

    @php
        $permisos = \App\Helpers\PermisosHelper::class;
    @endphp

    <div class="p-4" x-data="ventasModal()" x-init="init()">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="relative max-w-xs w-full">
                    <input
                        type="text"
                        x-model="search"
                        @input.debounce.500="buscar()"
                        placeholder="Buscar venta..."
                        class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:outline-none text-sm"
                    />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-4.35-4.35m1.44-5.4a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <a :href="`{{ route('ventas.exportarPDF') }}?search=${search}`"
                   class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md shadow whitespace-nowrap">
                    <i class="fas fa-file-pdf"></i> Exportar PDF
                </a>
            </div>

            @if($permisos::tienePermiso('Ventas', 'crear'))
                <button
                    @click="abrirModalCrear()"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow whitespace-nowrap"
                >
                    <i class="fas fa-plus"></i> Nueva venta
                </button>
            @endif
        </div>

        <div class="overflow-x-auto bg-white rounded-lg shadow mt-4">
            <table class="min-w-full text-sm text-gray-800">
                <thead class="bg-orange-500 text-white text-sm uppercase">
                    <tr>
                        <th class="px-4 py-3 text-center">Venta ID</th>
                        <th class="px-4 py-3 text-center">Cliente</th>
                        <th class="px-4 py-3 text-center">Empleado</th>
                        <th class="px-4 py-3 text-center">Fecha Venta</th>
                        <th class="px-4 py-3 text-left">Producto</th>
                        <th class="px-4 py-3 text-center">Total Venta</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($ventas as $venta)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-2 text-center">{{ $venta->VentaID }}</td>
                            <td class="px-4 py-2 text-center">{{ $venta->cliente->NombreCliente ?? '—' }}</td>
                            <td class="px-4 py-2 text-center">{{ $venta->empleado->persona->NombreCompleto ?? '—' }}</td>
                            <td class="px-4 py-2 text-center">{{ $venta->FechaVenta }}</td>
                            <td class="px-4 py-2">{{ $venta->producto->NombreProducto ?? '—' }}</td>
                            <td class="px-4 py-2 text-center">L. {{ number_format($venta->TotalVenta, 2) }}</td>
                            <td class="px-4 py-2 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    @if($permisos::tienePermiso('Ventas', 'editar'))
                                        <button
                                            @click="abrirModalEditar({
                                                VentaID: {{ $venta->VentaID }},
                                                ClienteID: {{ $venta->ClienteID }},
                                                EmpleadoID: {{ $venta->EmpleadoID }},
                                                FechaVenta: '{{ \Carbon\Carbon::parse($venta->FechaVenta)->format('Y-m-d') }}',
                                                ProductoID: {{ $venta->ProductoID }},
                                                TotalVenta: {{ $venta->TotalVenta }}
                                            })"
                                            class="bg-blue-600 hover:bg-blue-700 text-white p-2 rounded-full"
                                            title="Editar"
                                        >
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    @endif
                                    @if($permisos::tienePermiso('Ventas', 'eliminar'))
                                        <form action="{{ route('ventas.destroy', $venta->VentaID) }}" method="POST"
                                              onsubmit="return confirm('¿Estás seguro de eliminar esta venta?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="bg-red-600 hover:bg-red-700 text-white p-2 rounded-full"
                                                    title="Eliminar">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $ventas->appends(['search' => request('search')])->links() }}
        </div>

        {{-- Modal Crear --}}
        <div
            x-show="modalCrear"
            x-transition.opacity
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
            style="display:none;"
        >
            <div @click.away="modalCrear = false"
                 class="bg-white rounded-lg shadow-lg max-w-3xl w-full p-6 relative overflow-auto max-h-[90vh]">
                <button @click="modalCrear = false" class="absolute top-3 right-3 text-gray-600 hover:text-gray-900" title="Cerrar">
                    <i class="fas fa-times"></i>
                </button>

                <h2 class="text-xl font-bold mb-4">🛒 Nueva Venta</h2>

                <form action="{{ route('ventas.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="ClienteID" class="block text-gray-700 font-bold mb-2">Cliente</label>
                        <select name="ClienteID" id="ClienteID" class="w-full border rounded px-3 py-2" required>
                            <option value="">Seleccione un cliente</option>
                            @foreach ($clientes as $cliente)
                                <option value="{{ $cliente->ClienteID }}">{{ $cliente->NombreCliente }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="EmpleadoID" class="block text-gray-700 font-bold mb-2">Empleado</label>
                        <select name="EmpleadoID" id="EmpleadoID" class="w-full border rounded px-3 py-2" required>
                            <option value="">Seleccione un empleado</option>
                            @foreach ($empleados as $empleado)
                                <option value="{{ $empleado->EmpleadoID }}">
                                    {{ $empleado->persona->NombreCompleto ?? 'Empleado' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="FechaVenta" class="block text-gray-700 font-bold mb-2">Fecha Venta</label>
                        <input type="date" name="FechaVenta" id="FechaVenta" class="w-full border rounded px-3 py-2" required>
                    </div>

                    <div class="mb-4">
                        <label for="ProductoID" class="block text-gray-700 font-bold mb-2">Producto</label>
                        <select name="ProductoID" id="ProductoID" class="w-full border rounded px-3 py-2" required>
                            <option value="">Seleccione un producto</option>
                            @foreach ($productos as $producto)
                                <option value="{{ $producto->ProductoID }}">{{ $producto->NombreProducto }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="TotalVenta" class="block text-gray-700 font-bold mb-2">Total Venta</label>
                        <input type="number" step="0.01" name="TotalVenta" id="TotalVenta" class="w-full border rounded px-3 py-2" required>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" @click="modalCrear = false" class="px-4 py-2 border rounded">Cancelar</button>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Guardar</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Editar --}}
        <div
            x-show="modalEditar"
            x-transition.opacity
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
            style="display:none;"
        >
            <div @click.away="modalEditar = false"
                 class="bg-white rounded-lg shadow-lg max-w-3xl w-full p-6 relative overflow-auto max-h-[90vh]">
                <button @click="modalEditar = false" class="absolute top-3 right-3 text-gray-600 hover:text-gray-900" title="Cerrar">
                    <i class="fas fa-times"></i>
                </button>

                <h2 class="text-xl font-bold mb-4">🛒 Editar Venta #<span x-text="ventaEditar ? ventaEditar.VentaID : ''"></span></h2>

                <form :action="ventaEditar ? `/ventas/${ventaEditar.VentaID}` : '#'" method="POST" x-show="ventaEditar" x-cloak>
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="ClienteID_edit" class="block text-gray-700 font-bold mb-2">Cliente</label>
                        <select name="ClienteID" id="ClienteID_edit" class="w-full border rounded px-3 py-2" required x-model="ventaEditar.ClienteID">
                            <option value="">Seleccione un cliente</option>
                            @foreach ($clientes as $cliente)
                                <option value="{{ $cliente->ClienteID }}">{{ $cliente->NombreCliente }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="EmpleadoID_edit" class="block text-gray-700 font-bold mb-2">Empleado</label>
                        <select name="EmpleadoID" id="EmpleadoID_edit" class="w-full border rounded px-3 py-2" required x-model="ventaEditar.EmpleadoID">
                            <option value="">Seleccione un empleado</option>
                            @foreach ($empleados as $empleado)
                                <option value="{{ $empleado->EmpleadoID }}">
                                    {{ $empleado->persona->NombreCompleto ?? 'Empleado #' + $empleado->EmpleadoID }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="FechaVenta_edit" class="block text-gray-700 font-bold mb-2">Fecha Venta</label>
                        <input type="date" name="FechaVenta" id="FechaVenta_edit" class="w-full border rounded px-3 py-2" required x-model="ventaEditar.FechaVenta">
                    </div>

                    <div class="mb-4">
                        <label for="ProductoID_edit" class="block text-gray-700 font-bold mb-2">Producto</label>
                        <select name="ProductoID" id="ProductoID_edit" class="w-full border rounded px-3 py-2" required x-model="ventaEditar.ProductoID">
                            <option value="">Seleccione un producto</option>
                            @foreach ($productos as $producto)
                                <option value="{{ $producto->ProductoID }}">{{ $producto->NombreProducto }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="TotalVenta_edit" class="block text-gray-700 font-bold mb-2">Total Venta</label>
                        <input type="number" step="0.01" name="TotalVenta" id="TotalVenta_edit" class="w-full border rounded px-3 py-2" required x-model="ventaEditar.TotalVenta">
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" @click="modalEditar = false" class="px-4 py-2 border rounded">Cancelar</button>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Alpine.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <script>
        function ventasModal() {
            return {
                search: '{{ request("search") }}',
                modalCrear: false,
                modalEditar: false,
                ventaEditar: null,

                init() {
                    // Opcional
                },

                buscar() {
                    window.location.href = '?search=' + encodeURIComponent(this.search);
                },

                abrirModalCrear() {
                    this.modalCrear = true;
                },

                abrirModalEditar(venta) {
                    this.ventaEditar = venta;
                    this.modalEditar = true;
                },
            }
        }
    </script>
</x-app-layout>
