<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold flex items-center gap-2">
            <i class="fas fa-truck-loading"></i> Compras
        </h2>
    </x-slot>

    @php
        $permisos = \App\Helpers\PermisosHelper::class;
    @endphp

    <div class="p-4" x-data="comprasModal()" x-init="init()">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="relative max-w-xs w-full">
                    <input
                        type="text"
                        x-model="search"
                        @input.debounce.500="buscar()"
                        placeholder="Buscar compra..."
                        class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:outline-none text-sm"
                    />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>

                 <a :href="`{{ route('compras.exportarPDF') }}?search=${search}`"
                   class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md shadow whitespace-nowrap">
                    <i class="fas fa-file-pdf"></i> Exportar PDF
                </a>
            </div>

            @if($permisos::tienePermiso('Compras', 'crear'))
                <button
                    @click="abrirModalCrear()"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow"
                >
                    <i class="fas fa-plus"></i> Nueva compra
                </button>
            @endif
        </div>

        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full text-sm text-gray-800">
                <thead class="bg-orange-600 text-white text-sm uppercase">

                    <tr>
                        <th class="px-4 py-3 text-center">Compra ID</th>
                        <th class="px-4 py-3 text-center">Proveedor</th>
                        <th class="px-4 py-3 text-center">Empleado</th>
                        <th class="px-4 py-3 text-center">Fecha Compra</th>
                        <th class="px-4 py-3 text-center">Total Compra</th>
                        <th class="px-4 py-3 text-center">Estado</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($compras as $compra)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-2 text-center">{{ $compra->CompraID }}</td>
                            <td class="px-4 py-2 text-center">{{ $compra->proveedor->Descripcion ?? '—' }}</td>
                            <td class="px-4 py-2 text-center">{{ $compra->empleado->persona->NombreCompleto ?? '—' }}</td>
                            <td class="px-4 py-2 text-center">{{ \Carbon\Carbon::parse($compra->FechaCompra)->format('d/m/Y') }}</td>
                            <td class="px-4 py-2 text-center">L. {{ number_format($compra->TotalCompra, 2) }}</td>
                            <td class="px-4 py-2 text-center">{{ $compra->Estado }}</td>
                            <td class="px-4 py-2 text-center">
                                <div class="flex justify-center gap-2">
                                    @if($permisos::tienePermiso('Compras', 'editar'))
                                        <button
                                            @click="abrirModalEditar({
                                                CompraID: {{ $compra->CompraID }},
                                                ProveedorID: {{ $compra->ProveedorID }},
                                                EmpleadoID: {{ $compra->EmpleadoID }},
                                                FechaCompra: '{{ \Carbon\Carbon::parse($compra->FechaCompra)->format('Y-m-d\TH:i') }}',
                                                TotalCompra: {{ $compra->TotalCompra }},
                                                Estado: '{{ $compra->Estado }}'
                                            })"
                                            class="bg-blue-600 hover:bg-blue-700 text-white p-2 rounded-full"
                                            title="Editar"
                                        >
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    @endif
                                    @if($permisos::tienePermiso('Compras', 'eliminar'))
                                        <form action="{{ route('compras.destroy', $compra->CompraID) }}" method="POST" onsubmit="return confirm('¿Eliminar esta compra?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="bg-red-600 hover:bg-red-700 text-white p-2 rounded-full" title="Eliminar">
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
            {{ $compras->appends(['search' => request('search')])->links() }}
        </div>

        {{-- Modal Crear --}}
        <div
            x-show="modalCrear"
            x-transition.opacity
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
            style="display:none"
        >
            <div @click.away="modalCrear = false" class="bg-white rounded-lg shadow-lg max-w-3xl w-full p-6 relative overflow-auto max-h-[90vh]">
                <button @click="modalCrear = false" class="absolute top-3 right-3 text-gray-600 hover:text-gray-900" title="Cerrar">
                    <i class="fas fa-times"></i>
                </button>

                <h2 class="text-xl font-bold mb-4">🛒 Nueva Compra</h2>

                <form action="{{ route('compras.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label for="ProveedorID" class="block text-gray-700 font-semibold mb-2">Proveedor</label>
                        <select name="ProveedorID" id="ProveedorID" required class="w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-200 focus:ring focus:ring-opacity-50">
                            <option value="" disabled selected>Seleccionar proveedor</option>
                            @foreach ($proveedores as $proveedor)
                                <option value="{{ $proveedor->ProveedorID }}">{{ $proveedor->Descripcion }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="EmpleadoID" class="block text-gray-700 font-semibold mb-2">Empleado</label>
                        <select name="EmpleadoID" id="EmpleadoID" required class="w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-200 focus:ring focus:ring-opacity-50">
                            <option value="" disabled selected>Seleccionar empleado</option>
                            @foreach ($empleados as $empleado)
                                <option value="{{ $empleado->EmpleadoID }}">{{ $empleado->persona->NombreCompleto ?? 'Sin Nombre' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="FechaCompra" class="block text-gray-700 font-semibold mb-2">Fecha de Compra</label>
                        <input type="datetime-local" name="FechaCompra" id="FechaCompra" required class="w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-200 focus:ring focus:ring-opacity-50" />
                    </div>

                    <div>
                        <label for="TotalCompra" class="block text-gray-700 font-semibold mb-2">Total de Compra (Lps.)</label>
                        <input type="number" step="0.01" name="TotalCompra" id="TotalCompra" required placeholder="Ej: 5000.00" class="w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-200 focus:ring focus:ring-opacity-50" />
                    </div>

                    <div>
                        <label for="Estado" class="block text-gray-700 font-semibold mb-2">Estado</label>
                        <select name="Estado" id="Estado" required class="w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-200 focus:ring focus:ring-opacity-50">
                            <option value="Recibido">Recibido</option>
                            <option value="Pendiente">Pendiente</option>
                            <option value="Cancelado">Cancelado</option>
                        </select>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" @click="modalCrear = false" class="px-4 py-2 border rounded">Cancelar</button>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Guardar</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Editar --}}
        <div
            x-show="modalEditar"
            x-transition.opacity
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
            style="display:none"
        >
            <div @click.away="modalEditar = false" class="bg-white rounded-lg shadow-lg max-w-3xl w-full p-6 relative overflow-auto max-h-[90vh]">
                <button @click="modalEditar = false" class="absolute top-3 right-3 text-gray-600 hover:text-gray-900" title="Cerrar">
                    <i class="fas fa-times"></i>
                </button>

                <h2 class="text-xl font-bold mb-4">✏️ Editar Compra #<span x-text="compraEditar ? compraEditar.CompraID : ''"></span></h2>

                <form :action="compraEditar ? `/compras/${compraEditar.CompraID}` : '#'" method="POST" x-show="compraEditar" x-cloak class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="ProveedorID_edit" class="block text-gray-700 font-semibold mb-2">Proveedor</label>
                        <select name="ProveedorID" id="ProveedorID_edit" required class="w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-200 focus:ring focus:ring-opacity-50" x-model="compraEditar.ProveedorID">
                            <option value="" disabled>Seleccionar proveedor</option>
                            @foreach ($proveedores as $proveedor)
                                <option value="{{ $proveedor->ProveedorID }}">{{ $proveedor->Descripcion }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="EmpleadoID_edit" class="block text-gray-700 font-semibold mb-2">Empleado</label>
                        <select name="EmpleadoID" id="EmpleadoID_edit" required class="w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-200 focus:ring focus:ring-opacity-50" x-model="compraEditar.EmpleadoID">
                            <option value="" disabled>Seleccionar empleado</option>
                            @foreach ($empleados as $empleado)
                                <option value="{{ $empleado->EmpleadoID }}">{{ $empleado->persona->NombreCompleto ?? 'Sin Nombre' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="FechaCompra_edit" class="block text-gray-700 font-semibold mb-2">Fecha de Compra</label>
                        <input type="datetime-local" name="FechaCompra" id="FechaCompra_edit" required class="w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-200 focus:ring focus:ring-opacity-50" x-model="compraEditar.FechaCompra" />
                    </div>

                    <div>
                        <label for="TotalCompra_edit" class="block text-gray-700 font-semibold mb-2">Total de Compra (Lps.)</label>
                        <input type="number" step="0.01" name="TotalCompra" id="TotalCompra_edit" required class="w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-200 focus:ring focus:ring-opacity-50" x-model="compraEditar.TotalCompra" />
                    </div>

                    <div>
                        <label for="Estado_edit" class="block text-gray-700 font-semibold mb-2">Estado</label>
                        <select name="Estado" id="Estado_edit" required class="w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-200 focus:ring focus:ring-opacity-50" x-model="compraEditar.Estado">
                            <option value="Recibido">Recibido</option>
                            <option value="Pendiente">Pendiente</option>
                            <option value="Cancelado">Cancelado</option>
                        </select>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" @click="modalEditar = false" class="px-4 py-2 border rounded">Cancelar</button>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>





    {{-- Alpine.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <script>
        function comprasModal() {
            return {
                search: '{{ request("search") }}',
                modalCrear: false,
                modalEditar: false,
                compraEditar: null,

                init() {},

                buscar() {
                    window.location.href = '?search=' + encodeURIComponent(this.search);
                },

                abrirModalCrear() {
                    this.modalCrear = true;
                },

                abrirModalEditar(compra) {
                    this.compraEditar = compra;
                    this.modalEditar = true;
                }
            }
        }
    </script>
</x-app-layout>