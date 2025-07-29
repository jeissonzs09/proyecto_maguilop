
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold flex items-center gap-2">
            <i class="fas fa-users"></i> Clientes
        </h2>
    </x-slot>

    @php
        $permisos = \App\Helpers\PermisosHelper::class;
    @endphp

    <div class="p-4" x-data="clienteModales()" x-cloak>
        {{-- Mensajes --}}
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-800 rounded">
                {{ session('error') }}
            </div>
        @endif

        {{-- Controles superiores --}}
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                {{-- Buscador --}}
                <div class="relative max-w-xs w-full">
                    <input
                        type="text"
                        x-data="{ search: '{{ request('search') }}' }"
                        x-model="search"
                        @input.debounce.500="window.location.href = '?search=' + encodeURIComponent(search)"
                        placeholder="Buscar cliente..."
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
                @if($permisos::tienePermiso('Clientes', 'ver'))
                    <a href="{{ route('clientes.exportarPDF', ['search' => request('search')]) }}"
                       class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md shadow whitespace-nowrap">
                        <i class="fas fa-file-pdf"></i> Exportar PDF
                    </a>
                @endif
            </div>

            {{-- Botón Nuevo cliente --}}
            @if($permisos::tienePermiso('Clientes', 'crear'))
                <button @click="abrirCrear()"
                   class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow font-bold">
                    <i class="fas fa-plus"></i> Nuevo cliente
                </button>
            @endif
        </div>

        {{-- Tabla --}}
        <div class="overflow-x-auto bg-white rounded-lg shadow mt-4">
            <table class="min-w-full text-sm text-gray-800">
                <thead class="bg-orange-500 text-white text-sm uppercase">
                    <tr>
                        <th class="px-4 py-3 text-center">Cliente ID</th>
                        <th class="px-4 py-3 text-left">Nombre Cliente</th>
                        <th class="px-4 py-3 text-left">Persona</th>
                        <th class="px-4 py-3 text-left">Categoría</th>
                        <th class="px-4 py-3 text-left">Fecha Registro</th>
                        <th class="px-4 py-3 text-left">Estado</th>
                        <th class="px-4 py-3 text-left">Notas</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($clientes as $cliente)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-2 text-center">{{ $cliente->ClienteID }}</td>
                            <td class="px-4 py-2">{{ $cliente->NombreCliente ?? '—' }}</td>
                            <td class="px-4 py-2">{{ optional($cliente->persona)->Nombre ?? '' }} {{ optional($cliente->persona)->Apellido ?? '' }}</td>
                            <td class="px-4 py-2">{{ $cliente->Categoria }}</td>
                            <td class="px-4 py-2">{{ \Carbon\Carbon::parse($cliente->FechaRegistro)->format('d/m/Y') }}</td>
                            <td class="px-4 py-2">{{ $cliente->Estado }}</td>
                            <td class="px-4 py-2">{{ $cliente->Notas ?? '—' }}</td>
                            <td class="px-4 py-2 text-center space-x-2">
                                @if($permisos::tienePermiso('Clientes', 'editar'))
                                    <button @click="abrirEditar(@json($cliente))"
                                       class="bg-yellow-400 hover:bg-yellow-500 text-white p-2 rounded-full"
                                       title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                @endif
                                @if($permisos::tienePermiso('Clientes', 'eliminar'))
                                    <form action="{{ route('clientes.destroy', $cliente->ClienteID) }}" method="POST" class="inline-block"
                                          onsubmit="return confirm('¿Estás seguro de eliminar este cliente?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="bg-red-600 hover:bg-red-700 text-white p-2 rounded-full"
                                                title="Eliminar">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div class="mt-4">
            {{ $clientes->appends(['search' => request('search')])->links() }}
        </div>

        <!-- MODAL CREAR CLIENTE -->
        <div
            x-show="modalCrear"
            x-transition
            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
            x-cloak
        >
            <div class="bg-white w-full max-w-3xl rounded-lg shadow-lg p-6 relative">
                <!-- Botón cerrar X -->
                <button
                    @click="modalCrear = false"
                    class="absolute top-3 right-3 text-gray-600 hover:text-gray-900 font-bold text-xl"
                    title="Cerrar"
                    aria-label="Cerrar modal"
                >
                    &times;
                </button>

                <h2 class="text-xl font-bold mb-4">👥 Nuevo Cliente</h2>

                <form action="{{ route('clientes.store') }}" method="POST" @submit="validarCrear">
                    @csrf

                    <div class="mb-4">
                        <label for="NombreCliente" class="block text-gray-700 font-bold mb-2">Nombre del Cliente</label>
                        <input type="text" name="NombreCliente" id="NombreCliente" placeholder="Ej: Juan Pérez"
                            class="w-full border rounded px-3 py-2" x-model="cliente.NombreCliente" required>
                    </div>

                    <!-- Campo Empleado visible solo como texto -->
<div class="mb-4">
    <label class="block font-semibold">Empleado asignado:</label>
    <input type="text" value="{{ $empleadoNombre }}" disabled class="form-input bg-gray-100">
</div>

<!-- Campo oculto con EmpleadoID -->
<input type="hidden" name="EmpleadoID" value="{{ auth()->user()->empleado->EmpleadoID ?? '' }}">




                    <div class="mb-4">
                        <label for="Categoria" class="block text-gray-700 font-bold mb-2">Categoría</label>
                        <select name="Categoria" id="Categoria" class="w-full border rounded px-3 py-2" x-model="cliente.Categoria" required>
                            <option value="" disabled>Selecciona una categoría</option>
                            <option value="Regular">Regular</option>
                            <option value="Premium">Premium</option>
                            <option value="VIP">VIP</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="FechaRegistro" class="block text-gray-700 font-bold mb-2">Fecha de Registro</label>
                        <input type="date" name="FechaRegistro" id="FechaRegistro" class="w-full border rounded px-3 py-2" x-model="cliente.FechaRegistro" required>
                    </div>

                    <div class="mb-4">
                        <label for="Estado" class="block text-gray-700 font-bold mb-2">Estado</label>
                        <select name="Estado" id="Estado" class="w-full border rounded px-3 py-2" x-model="cliente.Estado">
                            <option value="Activo">Activo</option>
                            <option value="Inactivo">Inactivo</option>
                        </select>
                    </div>

                    <div class="mb-6">
                        <label for="Notas" class="block text-gray-700 font-bold mb-2">Notas</label>
                        <textarea name="Notas" id="Notas" rows="3" class="w-full border rounded px-3 py-2" x-model="cliente.Notas" placeholder="Notas adicionales"></textarea>
                    </div>

                    <div class="flex justify-between">
                        <button type="button" @click="modalCrear = false" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                            ❌ Cancelar
                        </button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            💾 Guardar Cliente
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL EDITAR CLIENTE -->
        <div
            x-show="modalEditar"
            x-transition
            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
            x-cloak
        >
            <div
                @click.away="modalEditar = false"
                class="bg-white p-6 rounded-md w-full max-w-2xl shadow-lg overflow-y-auto max-h-[90vh] relative"
            >
                <!-- Botón cerrar X -->
                <button
                    @click="modalEditar = false"
                    class="absolute top-3 right-3 text-gray-600 hover:text-gray-900 font-bold text-xl"
                    title="Cerrar"
                    aria-label="Cerrar modal"
                >
                    &times;
                </button>

                <h2 class="text-xl font-bold mb-4">✏ Editar Cliente #<span x-text="cliente.ClienteID"></span></h2>

                <form id="formEditarCliente" method="POST" @submit="validarEditar" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="NombreCliente" class="block text-gray-700 font-semibold mb-2">Nombre Cliente</label>
                        <input type="text" name="NombreCliente" id="NombreCliente" x-model="cliente.NombreCliente" required
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200" />
                    </div>

                    <div>
                        <label for="PersonaID" class="block text-gray-700 font-semibold mb-2">Persona</label>
                        <select name="PersonaID" id="PersonaID" x-model="cliente.PersonaID" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200">
                            <option value="">Seleccione una persona</option>
                            @foreach ($personas as $persona)
                                <option value="{{ $persona->PersonaID }}">{{ $persona->NombreCompleto }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="Categoria" class="block text-gray-700 font-semibold mb-2">Categoría</label>
                        <select name="Categoria" id="Categoria" x-model="cliente.Categoria"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200" required>
                            <option value="" disabled>Selecciona una categoría</option>
                            <option value="Regular">Regular</option>
                            <option value="Premium">Premium</option>
                            <option value="VIP">VIP</option>
                        </select>
                    </div>

                    <div>
                        <label for="FechaRegistro" class="block text-gray-700 font-semibold mb-2">Fecha de Registro</label>
                        <input type="date" name="FechaRegistro" id="FechaRegistro" x-model="cliente.FechaRegistro" required
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200" />
                    </div>

                    <div>
                        <label for="Estado" class="block text-gray-700 font-semibold mb-2">Estado</label>
                        <select name="Estado" id="Estado" x-model="cliente.Estado"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200">
                            <option value="Activo">Activo</option>
                            <option value="Inactivo">Inactivo</option>
                        </select>
                    </div>

                    <div>
                        <label for="Notas" class="block text-gray-700 font-semibold mb-2">Notas</label>
                        <textarea name="Notas" id="Notas" rows="3" x-model="cliente.Notas"
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200"></textarea>
                    </div>

                    <div class="mt-6 text-end">
                        <button type="button" @click="modalEditar = false"
                                class="bg-red-600 text-white font-bold px-4 py-2 rounded mr-2">
                            ❌ Cancelar
                        </button>
                        <button type="submit"
                                class="bg-blue-600 text-white font-bold px-4 py-2 rounded">
                            💾 Actualizar Cliente
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <!-- Script AlpineJS -->
    <script>
        
        function clienteModales() {
            return {
                modalCrear: false,
                modalEditar: false,
                cliente: {},

                abrirCrear() {
                    this.cliente = {
                       NombreCliente: '',
                        PersonaID: '{{ auth()->user()->PersonaID }}',
                        EmpleadoID: '{{ auth()->user()->EmpleadoID }}',
                        Categoria: '',
                        FechaRegistro: '',
                        Estado: 'Activo',
                         Notas: ''
                     };
                    this.modalCrear = true;
                },

                abrirEditar(clienteData) {
                    if(clienteData.FechaRegistro){
                        clienteData.FechaRegistro = clienteData.FechaRegistro.split(' ')[0];
                    }
                    this.cliente = {...clienteData};
                    this.modalEditar = true;
                },

                validarCrear(event) {
              if (!this.cliente.NombreCliente || !this.cliente.Categoria || !this.cliente.FechaRegistro) {
        alert('Por favor completa todos los campos requeridos.');
        event.preventDefault();
    }
},


                validarEditar(event) {
                    if (!this.cliente.NombreCliente || !this.cliente.PersonaID || !this.cliente.Categoria || !this.cliente.FechaRegistro) {
                        alert('Por favor completa todos los campos requeridos.');
                        event.preventDefault();
                        return;
                    }
                    const form = document.getElementById('formEditarCliente');
                    form.action = /clientes/${this.cliente.ClienteID};
                },
            }
        }
    </script>
</x-app-layout>
