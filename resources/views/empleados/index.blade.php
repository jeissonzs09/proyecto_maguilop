<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold flex items-center gap-2">
            <i class="fas fa-users"></i> Empleados
        </h2>
    </x-slot>

    @php
        $permisos = \App\Helpers\PermisosHelper::class;
    @endphp

    <div x-data="empleadosModal()" class="p-4" >

    

        {{-- Búsqueda y botón Nuevo --}}
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="relative max-w-xs w-full">
                    <input
                        type="text"
                        x-model="search"
                        @input.debounce.500="window.location.href = '?search=' + encodeURIComponent(search)"
                        placeholder="Buscar empleado..."
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

                <a href="{{ route('empleados.exportarPDF', ['search' => request('search')]) }}"
                   class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md shadow whitespace-nowrap">
                    <i class="fas fa-file-pdf"></i> Exportar PDF
                </a>
            </div>

            @if($permisos::tienePermiso('Empleados', 'crear'))
                <button
                    @click="openCreateModal()"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow whitespace-nowrap"
                >
                    <i class="fas fa-plus"></i> Nuevo empleado
                </button>
            @endif
        </div>

        {{-- Tabla de empleados --}}
        <div class="overflow-x-auto bg-white rounded-lg shadow mt-4">
            <table class="min-w-full text-sm text-gray-800">
                <thead class="bg-orange-500 text-white text-sm uppercase">
                    <tr>
                        <th class="px-4 py-3 text-center">Empleado ID</th>
                        <th class="px-4 py-3 text-center">Nombre Completo</th>
                        <th class="px-4 py-3 text-center">Departamento</th>
                        <th class="px-4 py-3 text-center">Cargo</th>
                        <th class="px-4 py-3 text-center">Fecha Contratación</th>
                        <th class="px-4 py-3 text-center">Salario</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($empleados as $empleado)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-2 text-center">{{ $empleado->EmpleadoID }}</td>
                            <td>{{ $empleado->persona->Nombre }} {{ $empleado->persona->Apellido }}</td>
                            <td class="px-4 py-2">{{ $empleado->Departamento ?? '—' }}</td>
                            <td class="px-4 py-2 text-center">{{ $empleado->Cargo }}</td>
                            <td class="px-4 py-2 text-center">{{ \Carbon\Carbon::parse($empleado->FechaContratacion)->format('d/m/Y') }}</td>
                            <td class="px-4 py-2 text-right">L. {{ number_format($empleado->Salario, 2) }}</td>
                            <td class="px-4 py-2 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    @if($permisos::tienePermiso('Empleados', 'editar'))
                                        <button
                                            @click="openEditModal({
                                                EmpleadoID: '{{ $empleado->EmpleadoID }}',
                                                PersonaID: '{{ $empleado->PersonaID }}',
                                                Departamento: '{{ $empleado->Departamento ?? '' }}',
                                                Cargo: '{{ $empleado->Cargo }}',
                                                FechaContratacion: '{{ $empleado->FechaContratacion }}',
                                                Salario: '{{ $empleado->Salario }}'
                                            })"
                                            class="bg-yellow-400 hover:bg-yellow-500 text-white p-2 rounded-full"
                                            title="Editar"
                                        >
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    @endif

                                    @if($permisos::tienePermiso('Empleados', 'eliminar'))
                                        <form action="{{ route('empleados.destroy', $empleado->EmpleadoID) }}" method="POST" onsubmit="return confirm('¿Seguro de eliminar este empleado?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white p-2 rounded-full" title="Eliminar">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-4 text-gray-500">No se encontraron empleados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div class="mt-4">
            {{ $empleados->appends(['search' => request('search')])->links() }}
        </div>

        
{{-- Modal Crear Empleado --}}
<div
    x-show="isCreateModalOpen"
    x-transition
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    @keydown.escape.window="closeCreateModal()"
    style="display: none;"
    x-cloak
>
    <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative max-h-[90vh] overflow-y-auto">
        <button
            @click="closeCreateModal()"
            class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-2xl font-bold"
            title="Cerrar"
        >&times;</button>

        <div class="flex items-center gap-2 mb-4 text-blue-600 text-2xl">
            <i class="fas fa-user-plus"></i>
            <h3 class="font-semibold">Nuevo Empleado</h3>
        </div>

        <form method="POST" action="{{ route('empleados.store') }}" class="space-y-4" novalidate>
            @csrf
            <input type="hidden" name="_action" value="create" />

            {{-- Persona --}}
            <div>
                <label for="PersonaID" class="block text-gray-700 font-bold mb-2">Persona</label>
                <select name="PersonaID" id="PersonaID" required
                        x-model="createForm.PersonaID"
                        class="w-full border rounded px-3 py-2"
                        :class="{ 'border-red-500': !createForm.PersonaID }">
                    <option value="">-- Selecciona una persona --</option>
                    @foreach($personas as $persona)
                        <option value="{{ $persona->PersonaID }}">{{ $persona->Nombre }} {{ $persona->Apellido }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Departamento --}}
            <div>
                <label for="Departamento" class="block text-gray-700 font-bold mb-2">Departamento</label>
                <input
                    type="text" name="Departamento" id="Departamento" required
                    x-model="createForm.Departamento"
                    maxlength="100"
                    pattern="^[A-Za-zÁÉÍÓÚáéíóúñÑ ]{2,}$"
                    title="Solo letras (A-Z), tildes y espacios. Mínimo 2 caracteres"
                    @input="
                        createForm.Departamento = $event.target.value
                            .replace(/[^A-Za-zÁÉÍÓÚáéíóúñÑ ]/g,'')
                            .replace(/\s+/g,' ')
                            .trimStart()
                    "
                    class="w-full border rounded px-3 py-2"
                    :class="{ 'border-red-500': createForm.Departamento.length < 2 }"
                />
            </div>

            {{-- Cargo --}}
            <div>
                <label for="Cargo" class="block text-gray-700 font-bold mb-2">Cargo</label>
                <input
                    type="text" name="Cargo" id="Cargo" required
                    x-model="createForm.Cargo"
                    maxlength="100"
                    pattern="^[A-Za-zÁÉÍÓÚáéíóúñÑ ]{2,}$"
                    title="Solo letras (A-Z), tildes y espacios. Mínimo 2 caracteres"
                    @input="
                        createForm.Cargo = $event.target.value
                            .replace(/[^A-Za-zÁÉÍÓÚáéíóúñÑ ]/g,'')
                            .replace(/\s+/g,' ')
                            .trimStart()
                    "
                    class="w-full border rounded px-3 py-2"
                    :class="{ 'border-red-500': createForm.Cargo.length < 2 }"
                />
            </div>

            {{-- Fecha de Contratación --}}
            <div>
                <label for="FechaContratacion" class="block text-gray-700 font-bold mb-2">Fecha de Contratación</label>
                <input type="date" name="FechaContratacion" id="FechaContratacion"
                       x-model="createForm.FechaContratacion"
                       required
                       class="w-full border rounded px-3 py-2"
                       :class="{ 'border-red-500': !createForm.FechaContratacion }"
                />
            </div>

            {{-- Salario --}}
            <div>
                <label for="Salario" class="block text-gray-700 font-bold mb-2">Salario (Lps.)</label>
                <input type="number" name="Salario" id="Salario" step="0.01" min="0"
                       x-model="createForm.Salario"
                       required
                       class="w-full border rounded px-3 py-2"
                       :class="{ 'border-red-500': !(parseFloat(createForm.Salario) > 0) }"
                />
            </div>

            {{-- Botones --}}
            <div class="flex justify-end gap-2 mt-4">
                <button type="button" @click="closeCreateModal()"
                        class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400">Cancelar
                </button>
                <button type="submit"
                        :disabled="!createForm.PersonaID ||
                                   !/^[A-Za-zÁÉÍÓÚáéíóúñÑ ]{2,}$/.test(createForm.Departamento || '') ||
                                   !/^[A-Za-zÁÉÍÓÚáéíóúñÑ ]{2,}$/.test(createForm.Cargo || '') ||
                                   !createForm.FechaContratacion ||
                                   !(parseFloat(createForm.Salario) > 0)"
                        class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>



        {{-- Modal Editar --}}
        <div
            x-show="isEditModalOpen"
            x-transition
            style="display: none;"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
            @keydown.escape.window="closeEditModal()"
        >
            <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative max-h-[90vh] overflow-y-auto">
                <button
                    @click="closeEditModal()"
                    class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-2xl font-bold"
                    title="Cerrar"
                >&times;</button>

                <div class="flex items-center gap-2 mb-4 text-yellow-500 text-2xl">
                    <i class="fas fa-user-edit"></i>
                    <h3 x-text="'Editar Empleado #' + editEmpleado.EmpleadoID" class="font-semibold"></h3>
                </div>

                <form method="POST" :action="`{{ url('empleados') }}/${editEmpleado.EmpleadoID}`" class="space-y-4" novalidate>
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="PersonaIDEdit" class="block text-gray-700 font-bold mb-2">Persona</label>
                        <select name="PersonaID" id="PersonaIDEdit" required
                                class="w-full border rounded px-3 py-2"
                                x-model="editEmpleado.PersonaID">
                            <option value="">-- Selecciona una persona --</option>
                            @foreach($personas as $persona)
                                <option value="{{ $persona->PersonaID }}">
                                    {{ $persona->Nombre }} {{ $persona->Apellido }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="DepartamentoEdit" class="block text-gray-700 font-bold mb-2">Departamento</label>
                        <input type="text" name="Departamento" id="DepartamentoEdit"
                        x-model="editEmpleado.Departamento"
                        maxlength="100"
                        title="Solo letras y espacios (2 a 100 caracteres)"
                         @input="
                         let val = $event.target.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ ]+/g, '');
                          val = val.replace(/\s+/g, ' ').trimStart();
                        editEmpleado.Departamento = val;
                          $event.target.value = val;
                         "
                          required
                     class="w-full border rounded px-3 py-2"
                     :class="{ 'border-red-500': editEmpleado.Departamento.length < 2 }"
                        />
                    </div>

                    <div>
                        <label for="CargoEdit" class="block text-gray-700 font-bold mb-2">Cargo</label>
                        <input type="text" name="Cargo" id="CargoEdit"
    x-model="editEmpleado.Cargo"
    maxlength="100"
    title="Solo letras y espacios (2 a 100 caracteres)"
    @input="
        let val = $event.target.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ ]+/g, '');
        val = val.replace(/\s+/g, ' ').trimStart();
        editEmpleado.Cargo = val;
        $event.target.value = val;
    "
    required
    class="w-full border rounded px-3 py-2"
    :class="{ 'border-red-500': editEmpleado.Cargo.length < 2 }"
/>



                    </div>

                    <div>
                        <label for="FechaContratacionEdit" class="block text-gray-700 font-bold mb-2">Fecha de Contratación</label>
                        <input type="date" name="FechaContratacion" id="FechaContratacionEdit" required
                               class="w-full border rounded px-3 py-2"
                               x-model="editEmpleado.FechaContratacion" />
                    </div>

                    <div>
                        <label for="SalarioEdit" class="block text-gray-700 font-bold mb-2">Salario (Lps.)</label>
                        <input type="number" step="0.01" name="Salario" id="SalarioEdit" required
                               class="w-full border rounded px-3 py-2"
                               x-model="editEmpleado.Salario" />
                    </div>

                    <div class="flex justify-end gap-2">
                        <button type="button" @click="closeEditModal()"
                                class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400">Cancelar
                        </button>
                        <button type="submit"
                                class="px-4 py-2 rounded bg-yellow-500 text-white hover:bg-yellow-600">Actualizar
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    {{-- Alpine.js --}}
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <script>
    function empleadosModal() {
        return {
            search: '{{ request('search') }}',
            isCreateModalOpen: {{ $errors->any() ? 'true' : 'false' }}, // abre modal crear si hay errores
            isEditModalOpen: false,
            editEmpleado: {
                EmpleadoID: '',
                PersonaID: '',
                Departamento: '',
                Cargo: '',
                FechaContratacion: '',
                Salario: ''
            },

                    createForm: {
            PersonaID: '',
            Departamento: '',
            Cargo: '',
            FechaContratacion: '',
            Salario: '',
        },


            openCreateModal() {
                this.isCreateModalOpen = true;
            },
            closeCreateModal() {
                this.isCreateModalOpen = false;
            },

            openEditModal(empleado) {
                this.editEmpleado = {
                    EmpleadoID: parseInt(empleado.EmpleadoID),
                    PersonaID: parseInt(empleado.PersonaID),
                    Departamento: empleado.Departamento ?? '',
                    Cargo: empleado.Cargo ?? '',
                    FechaContratacion: empleado.FechaContratacion ?? '',
                    Salario: parseFloat(empleado.Salario) ?? 0,
                };
                this.isEditModalOpen = true;
            },
            closeEditModal() {
                this.isEditModalOpen = false;
                this.editEmpleado = {
                    EmpleadoID: '',
                    PersonaID: '',
                    Departamento: '',
                    Cargo: '',
                    FechaContratacion: '',
                    Salario: ''
                };
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