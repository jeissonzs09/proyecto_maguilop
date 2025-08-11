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
                                    <button
    type="button"
    @click='abrirEditar({
        ClienteID: {{ $cliente->ClienteID }},
        NombreCliente: @json($cliente->NombreCliente),
        PersonaID: {{ $cliente->PersonaID }},
        Categoria: @json($cliente->Categoria),
        FechaRegistro: @json($cliente->FechaRegistro),
        Estado: @json($cliente->Estado),
        Notas: @json($cliente->Notas),
    })'
    class="bg-yellow-400 hover:bg-yellow-500 text-white p-2 rounded-full"
    title="Editar"
>
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
                        <input
    type="text"
    name="NombreCliente"
    id="NombreCliente"
    x-model="cliente.NombreCliente"
    maxlength="100"
    title="Solo letras y espacios (2 a 100 caracteres)"
    @input="
        let val = $event.target.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ ]+/g, '');
        val = val.replace(/\s+/g, ' ').trimStart();
        cliente.NombreCliente = val;
        $event.target.value = val;
    "
    required
    class="w-full border rounded px-3 py-2"
    :class="{ 'border-red-500': cliente.NombreCliente.length < 2 }"
/>

                    </div>

                    <div class="mb-4">
    <label for="PersonaID" class="block text-gray-700 font-bold mb-2">Persona</label>
    <select name="PersonaID" id="PersonaID" class="w-full border rounded px-3 py-2" x-model="cliente.PersonaID" required>
        <option value="">Seleccione una persona</option>
        @foreach($personas as $persona)
            <option value="{{ $persona->PersonaID }}">{{ $persona->NombreCompleto }}</option>
        @endforeach
    </select>
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
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
    x-cloak
>
  <div
    @click.away="modalEditar = false"
    class="bg-white p-6 rounded-md w-full max-w-2xl shadow-lg overflow-y-auto max-h-[90vh] relative"
  >
    <!-- Cerrar -->
    <button
      @click="modalEditar = false"
      class="absolute top-3 right-3 text-gray-600 hover:text-gray-900 font-bold text-xl"
      aria-label="Cerrar modal"
    >&times;</button>

    <h2 class="text-xl font-bold mb-4">
      ✏ Editar Cliente #<span x-text="cliente.ClienteID"></span>
    </h2>

    {{-- Errores globales (validación Laravel) --}}
    @if ($errors->any())
      <div class="mb-4 rounded border border-red-300 bg-red-50 text-red-700 p-3 text-sm">
        <ul class="list-disc pl-5">
          @foreach ($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form
      id="formEditarCliente"
      method="POST"
      :action="`{{ url('clientes') }}/${cliente.ClienteID}`"
      @submit="validarEditar"
      class="space-y-6"
    >
      @csrf
      @method('PUT')

      <div>
        <label for="NombreCliente" class="block text-gray-700 font-semibold mb-2">Nombre Cliente</label>
        <input
          type="text"
          name="NombreCliente"
          id="NombreCliente"
          x-model="cliente.NombreCliente"
          required
          class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200"
        />
        @error('NombreCliente')
          <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
      </div>

      <div>
        <label for="PersonaID" class="block text-gray-700 font-semibold mb-2">Persona</label>
        <select
          name="PersonaID"
          id="PersonaID"
          x-model="cliente.PersonaID"
          required
          class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200"
        >
          <option value="">Seleccione una persona</option>
          @foreach ($personas as $persona)
            <option value="{{ $persona->PersonaID }}">{{ $persona->NombreCompleto }}</option>
          @endforeach
        </select>
        @error('PersonaID')
          <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
      </div>

      <div>
        <label for="Categoria" class="block text-gray-700 font-semibold mb-2">Categoría</label>
        <select
          name="Categoria"
          id="Categoria"
          x-model="cliente.Categoria"
          required
          class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200"
        >
          <option value="" disabled>Selecciona una categoría</option>
          <option value="Regular">Regular</option>
          <option value="Premium">Premium</option>
          <option value="VIP">VIP</option>
        </select>
        @error('Categoria')
          <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
      </div>

      <div>
        <label for="FechaRegistro" class="block text-gray-700 font-semibold mb-2">Fecha de Registro</label>
        <input
          type="date"
          name="FechaRegistro"
          id="FechaRegistro"
          x-model="cliente.FechaRegistro"
          required
          class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200"
        />
        @error('FechaRegistro')
          <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
      </div>

      <div>
        <label for="Estado" class="block text-gray-700 font-semibold mb-2">Estado</label>
        <select
          name="Estado"
          id="Estado"
          x-model="cliente.Estado"
          class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200"
        >
          <option value="Activo">Activo</option>
          <option value="Inactivo">Inactivo</option>
        </select>
        @error('Estado')
          <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
      </div>

      <div>
        <label for="Notas" class="block text-gray-700 font-semibold mb-2">Notas</label>
        <textarea
          name="Notas"
          id="Notas"
          rows="3"
          x-model="cliente.Notas"
          class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200"
        ></textarea>
        @error('Notas')
          <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
      </div>

      <div class="mt-6 text-end">
        <button
          type="button"
          @click="modalEditar = false"
          class="bg-red-600 text-white font-bold px-4 py-2 rounded mr-2"
        >
          ❌ Cancelar
        </button>
        <button
          type="submit"
          class="bg-blue-600 text-white font-bold px-4 py-2 rounded"
        >
          💾 Actualizar Cliente
        </button>
      </div>
    </form>
  </div>
</div>



<script>
function clienteModales() {
  return {
    modalCrear: false,
    modalEditar: false,
    cliente: {},

    abrirCrear() {
      this.cliente = {
        NombreCliente: '',
        PersonaID: '',
        EmpleadoID: '{{ auth()->user()->empleado->EmpleadoID ?? '' }}',
        Categoria: '',
        FechaRegistro: '',
        Estado: 'Activo',
        Notas: ''
      };
      this.modalCrear = true;
    },

    abrirEditar(data) {
      // Normaliza fecha para <input type="date">
      if (data.FechaRegistro && typeof data.FechaRegistro === 'string') {
        data.FechaRegistro = data.FechaRegistro.split('T')[0].split(' ')[0];
      }
      // Asegura que el valor coincida con el value del <option> (string vs number)
      if (data.PersonaID !== null && data.PersonaID !== undefined) {
        data.PersonaID = String(data.PersonaID);
      }

      this.cliente = { ...data };
      this.modalEditar = true;
    },

    validarCrear(e) {
      if (!this.cliente.NombreCliente || !this.cliente.PersonaID || !this.cliente.Categoria || !this.cliente.FechaRegistro) {
        e.preventDefault();
        alert('Por favor completa todos los campos requeridos.');
      }
    },

    validarEditar(e) {
      // Solo prevenir si falta algo
      if (!this.cliente.NombreCliente || !this.cliente.PersonaID || !this.cliente.Categoria || !this.cliente.FechaRegistro) {
        e.preventDefault();
        alert('Por favor completa todos los campos requeridos.');
      }
      // Si todo está, NO hacemos preventDefault: Laravel lo envía normal al PUT /clientes/{id}
    },
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