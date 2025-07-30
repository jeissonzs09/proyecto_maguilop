<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold flex items-center gap-2">
            <i class="fas fa-clipboard-list"></i> Pedidos y Detalles
        </h2>
    </x-slot>

    @php
        $permisos = \App\Helpers\PermisosHelper::class;
    @endphp

    <div x-data="pedidoModal()" class="p-4">

        {{-- Barra superior --}}
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                {{-- Buscador --}}
                <div class="relative max-w-xs w-full">
                    <input
                        type="text"
                        x-model="search"
                        @input.debounce.500="buscar()"
                        placeholder="Buscar pedido..."
                        class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:outline-none text-sm"
                    />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M21 21l-4.35-4.35m1.44-5.4a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>

                {{-- Exportar PDF --}}
                <a :href="`{{ route('pedidos.exportarPDF') }}?search=${encodeURIComponent(search)}`"
                   class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md shadow whitespace-nowrap">
                    <i class="fas fa-file-pdf"></i> Exportar PDF
                </a>
            </div>

            {{-- Botón Nuevo pedido --}}
            @if($permisos::tienePermiso('Pedidos', 'crear'))
                <button
                    @click="openCreateModal()"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow whitespace-nowrap"
                >
                    <i class="fas fa-plus"></i> Nuevo pedido
                </button>
            @endif
        </div>

        {{-- Tabla --}}
        <div class="overflow-x-auto bg-white rounded-lg shadow mt-4">
            <table class="min-w-full text-sm text-gray-800">
                <thead class="bg-orange-500 text-white text-sm uppercase">
                    <tr>
                        <th class="px-4 py-3 text-center">Pedido ID</th>
                        <th class="px-4 py-3 text-center">Cliente</th>
                        <th class="px-4 py-3 text-center">Empleado</th>
                        <th class="px-4 py-3 text-center">Fecha Pedido</th>
                        <th class="px-4 py-3 text-center">Fecha Entrega</th>
                        <th class="px-4 py-3 text-center">Estado</th>
                        <th class="px-4 py-3 text-center">Producto</th>
                        <th class="px-4 py-3 text-center">Cantidad</th>
                        <th class="px-4 py-3 text-right">Precio Unitario</th>
                        <th class="px-4 py-3 text-right">Subtotal</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($pedidos as $pedido)
                        @foreach($pedido->detalles as $detalle)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-2 text-center">{{ $pedido->PedidoID }}</td>
                                <td>{{ $pedido->cliente->NombreCliente ?? '—' }}</td>
                                <td>{{ $pedido->empleado->persona->NombreCompleto ?? '—' }}</td>
                                <td class="px-4 py-2 text-center">{{ $pedido->FechaPedido }}</td>
                                <td class="px-4 py-2 text-center">{{ $pedido->FechaEntrega }}</td>
                                <td class="px-4 py-2 text-center">{{ $pedido->Estado }}</td>
                                <td>
                                    @foreach ($pedido->detalles as $d)
                                        {{ $d->producto->NombreProducto ?? '—' }}<br>
                                    @endforeach
                                </td>
                                <td class="px-4 py-2 text-center">{{ $detalle->Cantidad }}</td>
                                <td class="px-4 py-2 text-right">L. {{ number_format($detalle->PrecioUnitario, 2) }}</td>
                                <td class="px-4 py-2 text-right">L. {{ number_format($detalle->Subtotal, 2) }}</td>
                                <td class="px-4 py-2 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        @if($permisos::tienePermiso('Pedidos', 'editar'))
                                            <button
                                                @click="openEditModal(@js($pedido))"
                                                class="bg-yellow-400 hover:bg-yellow-500 text-white p-2 rounded-full"
                                                title="Editar"
                                            >
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @endif
                                        @if($permisos::tienePermiso('Pedidos', 'eliminar'))
                                            <form action="{{ route('pedidos.destroy', $pedido->PedidoID) }}" method="POST"
                                                  onsubmit="return confirm('¿Seguro de eliminar este pedido?')">
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
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $pedidos->appends(['search' => request('search')])->links() }}
        </div>

        {{-- Modal Crear Pedido --}}
        <div
            x-show="showCreate"
            style="display: none"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
            @keydown.window.escape="showCreate = false"
        >
            <div class="bg-white w-full max-w-3xl rounded-lg p-6 relative max-h-[90vh] overflow-auto">
                <button
                    @click="showCreate = false"
                    class="absolute top-2 right-3 text-gray-600 hover:text-red-500 text-xl font-bold"
                    aria-label="Cerrar modal"
                >&times;</button>

                <h2 class="text-xl font-bold">🛒 Nuevo Pedido</h2>

                <form action="{{ route('pedidos.store') }}" method="POST" id="formPedidoCreate" class="space-y-4">
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
    <label class="block text-gray-700 font-bold mb-2">Empleado</label>
    
    {{-- Campo oculto con el ID del empleado logueado --}}
    <input type="hidden" name="EmpleadoID" value="{{ $empleadoID }}">

    {{-- Mostrar el nombre del empleado como texto plano --}}
    <div class="px-3 py-2 border rounded bg-gray-100">
        {{ $empleadoNombre }}
    </div>
</div>


                    <div class="mb-4">
                        <label for="FechaPedido" class="block text-gray-700 font-bold mb-2">Fecha Pedido</label>
                        <input type="date" name="FechaPedido" id="FechaPedido"
                               class="w-full border rounded px-3 py-2" required>
                    </div>

                    <div class="mb-4">
                        <label for="FechaEntrega" class="block text-gray-700 font-bold mb-2">Fecha Entrega</label>
                        <input type="date" name="FechaEntrega" id="FechaEntrega"
                               class="w-full border rounded px-3 py-2" required>
                    </div>

                    <div class="mb-4">
                        <label for="Estado" class="block text-gray-700 font-bold mb-2">Estado</label>
                        <select id="Estado" name="Estado" class="w-full border rounded px-3 py-2" required>
                            @php
                                $estados = ['Pendiente', 'Enviado', 'Entregado', 'Cancelado'];
                            @endphp
                            @foreach($estados as $estado)
                                <option value="{{ $estado }}">{{ $estado }}</option>
                            @endforeach
                        </select>
                    </div>

                    <h3 class="text-lg font-bold mt-6">Detalles del Pedido</h3>

                    <div class="mb-4">
                        <label for="ProductoID" class="block text-gray-700 font-bold mb-2">Producto</label>
                        <select name="ProductoID" id="ProductoID" class="w-full border rounded px-3 py-2" required>
                            <option value="">Seleccione un producto</option>
                            @foreach ($productos as $producto)
                                <option value="{{ $producto->ProductoID }}" data-precio="{{ $producto->PrecioVenta }}">
                                    {{ $producto->NombreProducto }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="Cantidad" class="block text-gray-700 font-bold mb-2">Cantidad</label>
                        <input type="number" name="Cantidad" id="Cantidad" placeholder="Ej: 10" min="1"
                               class="w-full border rounded px-3 py-2" required oninput="calcularSubtotalCreate()">
                    </div>

                    <div class="mb-4">
                        <label for="PrecioUnitario" class="block text-gray-700 font-bold mb-2">Precio Unitario</label>
                        <input type="number" step="0.01" min="0" name="PrecioUnitario" id="PrecioUnitario" placeholder="Ej: 15.50"
                               class="w-full border rounded px-3 py-2" required oninput="calcularSubtotalCreate()">
                    </div>

                    <div class="mb-4">
                        <label for="Subtotal" class="block text-gray-700 font-bold mb-2">Subtotal</label>
                        <input type="number" step="0.01" name="Subtotal" id="Subtotal" placeholder="Ej: 155.00"
                               class="w-full border rounded px-3 py-2" readonly>
                    </div>

                    <div class="flex justify-between">
                        <button type="button" @click="showCreate = false"
                                class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                            ❌ Cancelar
                        </button>
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            💾 Guardar Pedido
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Editar Pedido --}}
        <div
            x-show="showEdit"
            style="display: none"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
            @keydown.window.escape="closeEditModal()"
        >
            <div class="bg-white w-full max-w-4xl rounded-lg p-6 relative max-h-[90vh] overflow-auto" x-cloak>
                <button
                    @click="closeEditModal()"
                    class="absolute top-2 right-3 text-gray-600 hover:text-red-500 text-xl font-bold"
                    aria-label="Cerrar modal"
                >&times;</button>

                <template x-if="pedidoEditar">
                    <form :action="`/pedidos/${pedidoEditar.PedidoID}`" method="POST" class="space-y-4" x-transition>
                        @csrf
                        @method('PUT')

                        <h2 class="text-xl font-bold">✏️ Editar Pedido #<span x-text="pedidoEditar.PedidoID"></span></h2>

                        <div class="mb-4">
                            <label for="ClienteIDEdit" class="block font-bold mb-1">Cliente</label>
                            <select id="ClienteIDEdit" name="ClienteID" class="w-full border rounded px-3 py-2" required x-model="pedidoEditar.ClienteID">
                                <option value="">Seleccione un cliente</option>
                                @foreach ($clientes as $cliente)
                                    <option value="{{ $cliente->ClienteID }}">{{ $cliente->NombreCliente }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
    <label class="block text-gray-700 font-bold mb-2">Empleado</label>

    {{-- Campo oculto con el ID --}}
    <input type="hidden" name="EmpleadoID" :value="pedidoEditar.EmpleadoID">

    {{-- Solo mostrar el nombre en texto plano --}}
    <div class="px-3 py-2 border rounded bg-gray-100 text-sm text-gray-700">
        <span x-text="pedidoEditar?.empleado?.persona?.NombreCompleto ?? 'Empleado no disponible'"></span>
    </div>
</div>


                        <div class="mb-4">
                            <label for="FechaPedidoEdit" class="block font-bold mb-1">Fecha Pedido</label>
                            <input type="date" id="FechaPedidoEdit" name="FechaPedido" class="w-full border rounded px-3 py-2" required
                                   x-model="pedidoEditar.FechaPedido" />
                        </div>

                        <div class="mb-4">
                            <label for="FechaEntregaEdit" class="block font-bold mb-1">Fecha Entrega</label>
                            <input type="date" id="FechaEntregaEdit" name="FechaEntrega" class="w-full border rounded px-3 py-2" required
                                   x-model="pedidoEditar.FechaEntrega" />
                        </div>

                        <div class="mb-4">
                            <label for="EstadoEdit" class="block font-bold mb-1">Estado</label>
                            <select id="EstadoEdit" name="Estado" class="w-full border rounded px-3 py-2" required
                                    x-model="pedidoEditar.Estado">
                                @php
                                    $estados = ['Pendiente', 'Enviado', 'Entregado', 'Cancelado'];
                                @endphp
                                @foreach($estados as $estado)
                                    <option value="{{ $estado }}">{{ $estado }}</option>
                                @endforeach
                            </select>
                        </div>

                        <h3 class="font-bold mt-6 mb-2">Detalles del Pedido</h3>
                        <div id="detallesEdit">
                            <template x-for="(detalle, index) in pedidoEditar.detalles" :key="index">
                                <div class="flex gap-2 mb-2">
                                    <select
                                        :name="`detalles[${index}][ProductoID]`"
                                        class="border rounded px-2 py-1 w-1/3"
                                        required
                                        x-model="detalle.ProductoID"
                                    >
                                        <option value="">Producto</option>
                                        @foreach ($productos as $producto)
                                            <option value="{{ $producto->ProductoID }}">{{ $producto->NombreProducto }}</option>
                                        @endforeach
                                    </select>

                                    <input
                                        type="number"
                                        :name="`detalles[${index}][Cantidad]`"
                                        class="border rounded px-2 py-1 w-1/4"
                                        placeholder="Cantidad"
                                        min="1"
                                        required
                                        x-model.number="detalle.Cantidad"
                                    >

                                    <input
                                        type="number"
                                        step="0.01"
                                        :name="`detalles[${index}][PrecioUnitario]`"
                                        class="border rounded px-2 py-1 w-1/4"
                                        placeholder="Precio Unitario"
                                        min="0"
                                        required
                                        x-model.number="detalle.PrecioUnitario"
                                    >

                                    <span class="px-2 py-1 w-1/4 text-right font-semibold" x-text="`L. ${(detalle.Cantidad * detalle.PrecioUnitario).toFixed(2)}`"></span>
                                </div>
                            </template>
                        </div>

                        <div class="flex justify-between mt-6">
                            <button type="button" @click="closeEditModal()"
                                    class="bg-red-600 hover:bg-red-700 text-white font-bold px-4 py-2 rounded">❌ Cancelar</button>
                            <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded">💾 Actualizar Pedido</button>
                        </div>
                    </form>
                </template>
            </div>
        </div>

    </div>

    <script>
        function calcularSubtotalCreate() {
            const cantidad = parseFloat(document.getElementById('Cantidad').value) || 0;
            const precioUnitario = parseFloat(document.getElementById('PrecioUnitario').value) || 0;
            const subtotalInput = document.getElementById('Subtotal');
            subtotalInput.value = (cantidad * precioUnitario).toFixed(2);
        }

        function pedidoModal() {
            return {
                search: '{{ request('search') }}',
                showCreate: false,
                showEdit: false,
                pedidoEditar: null,

                buscar() {
                    window.location.href = '?search=' + encodeURIComponent(this.search);
                },

                openCreateModal() {
                    this.showCreate = true;
                    // Reset form fields on open
                    setTimeout(() => {
                        const form = document.getElementById('formPedidoCreate');
                        if(form) form.reset();
                        document.getElementById('Subtotal').value = '';
                    }, 0);
                },

                openEditModal(pedido) {
                    // Normaliza fechas para input type="date" formato yyyy-MM-dd
                    function formatDate(dateStr) {
                        if (!dateStr) return '';
                        const d = new Date(dateStr);
                        if (isNaN(d)) return '';
                        return d.toISOString().split('T')[0];
                    }

                    // Clonar pedido para no mutar original
                    let clone = JSON.parse(JSON.stringify(pedido));
                    clone.FechaPedido = formatDate(clone.FechaPedido);
                    clone.FechaEntrega = formatDate(clone.FechaEntrega);

                    // Detalles puede que no venga con detalles, asegúrate que sí (si no, asigna [])
                    clone.detalles = clone.detalles || [];

                    this.pedidoEditar = clone;
                    this.showEdit = true;
                },

                closeEditModal() {
                    this.showEdit = false;
                    this.pedidoEditar = null;
                }
            }
        }
    </script>

        @php
    $toastType = session('error') ? 'error' : (session('success') ? 'success' : null);
    $toastMsg  = session('error') ?: session('success');
@endphp

@if($toastType)
    <div
        id="toast-empleado"
        role="status" aria-live="polite"
        class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2
               text-white px-10 py-6 rounded-full shadow-2xl flex items-center gap-5
               z-50 animate-fadeIn text-xl font-semibold ring-1 ring-white/20
               max-w-[90vw]"
        style="min-width: 420px; background-color: {{ $toastType === 'error' ? '#dc2626' : '#16a34a' }};"
        onclick="this.remove()"
    >
        @if($toastType === 'error')
            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 flex-shrink-0" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="12" cy="12" r="10" />
                <line x1="15" y1="9" x2="9" y2="15" />
                <line x1="9" y1="9" x2="15" y2="15" />
            </svg>
        @else
            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 flex-shrink-0" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="12" cy="12" r="10" />
                <path d="M9 12l2 2l4-4" />
            </svg>
        @endif

        <span class="leading-snug break-words">{{ $toastMsg }}</span>
    </div>

    <script>
        setTimeout(() => {
            const toast = document.getElementById('toast-empleado');
            if (toast) {
                toast.style.transition = 'opacity .5s ease, transform .5s ease';
                toast.style.opacity = '0';
                toast.style.transform = 'translate(-50%, -50%) scale(.95)';
                setTimeout(() => toast.remove(), 500);
            }
        }, 3200);
    </script>

    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translate(-50%, -48%) scale(.97); }
            to   { opacity: 1; transform: translate(-50%, -50%) scale(1); }
        }
        .animate-fadeIn { animation: fadeIn .28s ease forwards; }
    </style>
@endif

</x-app-layout>
