<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold flex items-center gap-2">
            <i class="fas fa-tools"></i> Reparaciones
        </h2>
    </x-slot>

    @php
        $permisos = \App\Helpers\PermisosHelper::class;
    @endphp

    <div class="p-4"
         x-data="{
            crearModal: false,
            editarModal: false,
            reparacionEditar: {},
            abrirEditarModal(reparacion) {
                this.reparacionEditar = reparacion;
                this.editarModal = true;
            }
        }">
        <style>
            .pagination .page-link {
                color: #f97316;
                border: 1px solid #f97316;
            }

            .pagination .page-link:hover {
                background-color: #f97316;
                color: white;
                border-color: #f97316;
            }

            .pagination .active .page-link {
                background-color: #f97316;
                border-color: #f97316;
                color: white;
            }
        </style>

        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="relative max-w-xs w-full">
                    <input type="text" x-data="{ search: '{{ request('search') }}' }" x-model="search"
                           @input.debounce.500="window.location.href = '?search=' + encodeURIComponent(search)"
                           placeholder="Buscar reparación..."
                           class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm" />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M21 21l-4.35-4.35m1.44-5.4a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>

                <a href="{{ route('reparaciones.exportarPDF', ['search' => request('search')]) }}"
                   class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md shadow whitespace-nowrap">
                    <i class="fas fa-file-pdf"></i> Exportar PDF
                </a>
            </div>
 
            @if($permisos::tienePermiso('Reparaciones', 'crear'))
                <button @click="crearModal = true"
                        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow">
                    <i class="fas fa-plus"></i> Nueva reparación
                </button>
            @endif
        </div>

        <div class="overflow-x-auto bg-white rounded-lg shadow mt-4">
            <table class="min-w-full text-sm text-gray-800">
                <thead class="bg-orange-500 text-white text-sm uppercase">
                    <tr>
                        <th class="px-4 py-3">ID</th>
                        <th class="px-4 py-3">Cliente</th>
                        <th class="px-4 py-3">Producto</th>
                        <th class="px-4 py-3">Entrada</th>
                        <th class="px-4 py-3">Salida</th>
                        <th class="px-4 py-3">Problema</th>
                        <th class="px-4 py-3">Estado</th>
                        <th class="px-4 py-3">Costo</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($reparaciones as $reparacion)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-2">{{ $reparacion->ReparacionID }}</td>
                            <td class="px-4 py-2">{{ $reparacion->cliente->NombreCliente }}</td>
                            <td class="px-4 py-2">{{ $reparacion->producto->NombreProducto }}</td>
                            <td class="px-4 py-2">{{ $reparacion->FechaEntrada }}</td>
                            <td class="px-4 py-2">{{ $reparacion->FechaSalida }}</td>
                            <td class="px-4 py-2">{{ $reparacion->DescripcionProblema }}</td>
                            <td class="px-4 py-2">{{ $reparacion->Estado }}</td>
                            <td class="px-4 py-2">L. {{ number_format($reparacion->Costo, 2) }}</td>
                            <td class="px-4 py-2 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    @if($permisos::tienePermiso('Reparaciones', 'editar'))
                                        <button @click='abrirEditarModal(@json($reparacion))'
                                                class="bg-yellow-400 hover:bg-yellow-500 text-white p-2 rounded-full"
                                                title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    @endif

                                    @if($permisos::tienePermiso('Reparaciones', 'eliminar'))
                                        <form action="{{ route('reparaciones.destroy', $reparacion->ReparacionID) }}" method="POST"
                                              onsubmit="return confirm('¿Estás seguro de eliminar esta reparación?')">
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
            {{ $reparaciones->appends(['search' => request('search')])->links() }}
        </div>

        {{-- ✅ MODAL CREAR --}}
<div
    x-show="crearModal"
    x-transition
    style="display: none;"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
>
    <div
        @click.away="crearModal = false"
        @keydown.escape.window="crearModal = false"
        class="bg-white p-6 rounded-lg shadow-lg w-full max-w-2xl max-h-[90vh] overflow-auto relative"
        role="dialog"
        aria-modal="true"
        aria-labelledby="modalCrearTitulo"
    >
        {{-- Botón X para cerrar --}}
        <button
            @click="crearModal = false"
            aria-label="Cerrar modal"
            class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-2xl font-bold"
            type="button"
        >
            &times;
        </button>

        <h2 id="modalCrearTitulo" class="text-xl font-bold mb-4">➕ Nueva Reparación</h2>

        <form action="{{ route('reparaciones.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Aquí van los campos del formulario (igual que antes) -->

            <div>
                <label for="ClienteID" class="block font-semibold mb-1">Cliente</label>
                <select
                    id="ClienteID"
                    name="ClienteID"
                    required
                    class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500"
                >
                    <option value="">Selecciona un cliente</option>
                    @foreach ($clientes as $cliente)
                        <option value="{{ $cliente->ClienteID }}" {{ old('ClienteID') == $cliente->ClienteID ? 'selected' : '' }}>
                            {{ $cliente->NombreCliente }}
                        </option>
                    @endforeach
                </select>
                @error('ClienteID') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="ProductoID" class="block font-semibold mb-1">Producto</label>
                <select
                    id="ProductoID"
                    name="ProductoID"
                    required
                    class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500"
                >
                    <option value="">Selecciona un producto</option>
                    @foreach ($productos as $producto)
                        <option value="{{ $producto->ProductoID }}" {{ old('ProductoID') == $producto->ProductoID ? 'selected' : '' }}>
                            {{ $producto->NombreProducto }}
                        </option>
                    @endforeach
                </select>
                @error('ProductoID') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="FechaEntrada" class="block font-semibold mb-1">Fecha de Entrada</label>
                <input
                    id="FechaEntrada"
                    type="date"
                    name="FechaEntrada"
                    value="{{ old('FechaEntrada') }}"
                    required
                    class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500"
                >
                @error('FechaEntrada') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="FechaSalida" class="block font-semibold mb-1">Fecha de Salida</label>
                <input
                    id="FechaSalida"
                    type="date"
                    name="FechaSalida"
                    value="{{ old('FechaSalida') }}"
                    class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500"
                >
                @error('FechaSalida') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="DescripcionProblema" class="block font-semibold mb-1">Descripción del Problema</label>
                <textarea
                    id="DescripcionProblema"
                    name="DescripcionProblema"
                    rows="3"
                    required
                    class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500"
                >{{ old('DescripcionProblema') }}</textarea>
                @error('DescripcionProblema') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="Estado" class="block font-semibold mb-1">Estado</label>
                <select
                    id="Estado"
                    name="Estado"
                    class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500"
                >
                    <option value="Pendiente" {{ old('Estado') == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="En proceso" {{ old('Estado') == 'En proceso' ? 'selected' : '' }}>En proceso</option>
                    <option value="Finalizado" {{ old('Estado') == 'Finalizado' ? 'selected' : '' }}>Finalizado</option>
                </select>
                @error('Estado') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="Costo" class="block font-semibold mb-1">Costo (Lps.)</label>
                <input
                    id="Costo"
                    type="number"
                    step="0.01"
                    name="Costo"
                    value="{{ old('Costo') }}"
                    required
                    class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500"
                >
                @error('Costo') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end gap-2">
                <button
                    type="button"
                    @click="crearModal = false"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded"
                >Cancelar</button>
                <button
                    type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded"
                >Guardar</button>
            </div>
        </form>
    </div>
</div>



{{-- MODAL EDITAR --}}
<div x-show="editarModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div @click.away="editarModal = false" class="bg-white p-6 rounded-lg shadow-lg w-full max-w-2xl relative">
        <h2 class="text-xl font-bold mb-4">✏️ Editar Reparación</h2>

        {{-- Botón X para cerrar --}}
        <button @click="editarModal = false"
                class="absolute top-4 right-4 text-gray-600 hover:text-gray-900 text-2xl font-bold"
                aria-label="Cerrar modal">
            &times;
        </button>

        <form :action="`/reparaciones/${reparacionEditar.ReparacionID}`" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- Aquí tus campos del formulario (cliente, producto, fechas, descripción, estado, costo) --}}

            <!-- Cliente -->
            <div>
                <label class="block font-semibold">Cliente</label>
                <select name="ClienteID" class="w-full border rounded-md px-3 py-2" required>
                    @foreach($clientes as $cliente)
                        <option :selected="reparacionEditar.ClienteID == {{ $cliente->ClienteID }}"
                                value="{{ $cliente->ClienteID }}">{{ $cliente->NombreCliente }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Producto -->
            <div>
                <label class="block font-semibold">Producto</label>
                <select name="ProductoID" class="w-full border rounded-md px-3 py-2" required>
                    @foreach($productos as $producto)
                        <option :selected="reparacionEditar.ProductoID == {{ $producto->ProductoID }}"
                                value="{{ $producto->ProductoID }}">{{ $producto->NombreProducto }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Fecha Entrada -->
            <div>
                <label class="block font-semibold">Fecha Entrada</label>
                <input type="date" name="FechaEntrada"
                       class="w-full border rounded-md px-3 py-2"
                       :value="reparacionEditar.FechaEntrada?.slice(0,10)" required>
            </div>

            <!-- Fecha Salida -->
            <div>
                <label class="block font-semibold">Fecha Salida</label>
                <input type="date" name="FechaSalida"
                       class="w-full border rounded-md px-3 py-2"
                       :value="reparacionEditar.FechaSalida?.slice(0,10)">
            </div>

            <!-- Descripción del Problema -->
            <div>
                <label class="block font-semibold">Descripción del Problema</label>
                <textarea name="DescripcionProblema" rows="3"
                          class="w-full border rounded-md px-3 py-2"
                          x-text="reparacionEditar.DescripcionProblema"></textarea>
            </div>

            <!-- Estado -->
            <div>
                <label class="block font-semibold">Estado</label>
                <select name="Estado" class="w-full border rounded-md px-3 py-2">
                    <option value="Pendiente" :selected="reparacionEditar.Estado === 'Pendiente'">Pendiente</option>
                    <option value="En proceso" :selected="reparacionEditar.Estado === 'En proceso'">En proceso</option>
                    <option value="Finalizado" :selected="reparacionEditar.Estado === 'Finalizado'">Finalizado</option>
                </select>
            </div>

            <!-- Costo -->
            <div>
                <label class="block font-semibold">Costo (Lps.)</label>
                <input type="number" step="0.01" name="Costo"
                       class="w-full border rounded-md px-3 py-2"
                       :value="reparacionEditar.Costo" required>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" @click="editarModal = false" class="bg-gray-500 text-white px-4 py-2 rounded">Cancelar</button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

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
