<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold flex items-center gap-2">
            <i class="fas fa-users"></i> Personas
        </h2>
    </x-slot>

    @php
        $permisos = \App\Helpers\PermisosHelper::class;
    @endphp

    <div x-data="personasModal()" class="p-4">

        {{-- Búsqueda y botón Nuevo --}}
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="relative max-w-xs w-full">
                    <input
                        type="text"
                        x-model="search"
                        @input.debounce.500="window.location.href = '?search=' + encodeURIComponent(search)"
                        placeholder="Buscar persona..."
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
                @if($permisos::tienePermiso('Persona', 'crear'))
                    <button
                        @click="openCreateModal()"
                        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow whitespace-nowrap"
                    >
                        <i class="fas fa-plus"></i> Nueva Persona
                    </button>
                @endif
            </div>
        </div>

        {{-- Tabla de personas --}}
        <div class="overflow-x-auto bg-white rounded-lg shadow mt-4">
            <table class="min-w-full text-sm text-gray-800">
                <thead class="bg-orange-500 text-white text-sm uppercase">
                    <tr>
                        <th class="px-4 py-3 text-center">ID</th>
                        <th class="px-4 py-3 text-center">Nombre</th>
                        <th class="px-4 py-3 text-center">Apellido</th>
                        <th class="px-4 py-3 text-center">Fecha Nac.</th>
                        <th class="px-4 py-3 text-center">Género</th>
                        <th class="px-4 py-3 text-center">Correo Electrónico</th>
                        <th class="px-4 py-3 text-center">Teléfonos</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($personas as $persona)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-2 text-center">{{ $persona->PersonaID }}</td>
                            <td class="px-4 py-2">{{ $persona->Nombre }}</td>
                            <td class="px-4 py-2">{{ $persona->Apellido }}</td>
                            <td class="px-4 py-2 text-center">{{ \Carbon\Carbon::parse($persona->FechaNacimiento)->format('d/m/Y') }}</td>
                            <td class="px-4 py-2 text-center">{{ $persona->Genero }}</td>
                            <td class="px-4 py-2">{{ $persona->CorreoElectronico }}</td>
                            <td class="px-4 py-2">
                                <ul>
                                    @foreach($persona->telefonos as $telefono)
                                        <li>{{ $telefono->Tipo }}: {{ $telefono->Numero }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="px-4 py-2 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    @if($permisos::tienePermiso('Persona', 'editar'))
                                      <button
                                       @click="openEditModal({{ $persona->toJson() }})"
                                        class="bg-yellow-400 hover:bg-yellow-500 text-white p-2 rounded-full"
                                        title="Editar"
                                      >
                                        <i class="fas fa-edit"></i>
                                      </button>
                                    @endif

                                    @if($permisos::tienePermiso('Persona', 'eliminar'))
                                        <form 
    action="{{ route('persona.destroy', $persona->PersonaID) }}" 
    method="POST" 
    onsubmit="return confirm('¿Seguro de eliminar esta persona?')" 
    style="display:inline-block;"
>
    @csrf
    @method('DELETE')
    <button 
        type="submit" 
        class="bg-red-600 hover:bg-red-700 text-white p-2 rounded-full" 
        title="Eliminar"
    >
        <i class="fas fa-trash-alt"></i>
    </button>
</form>

                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center py-4 text-gray-500">No se encontraron personas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div class="mt-4">
            {{ $personas->appends(['search' => request('search')])->links() }}
        </div>

        {{-- Modal Crear --}}
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
                    <h3 class="font-semibold">Nueva Persona</h3>
                </div>

                <form method="POST" action="{{ route('persona.store') }}" class="space-y-4" novalidate>
                    @csrf

                    <div>
                        <label for="Nombre" class="block text-gray-700 font-bold mb-2">Nombre</label>
                        <input type="text" name="Nombre" id="Nombre" required
                               value="{{ old('Nombre') }}"
                               class="w-full border rounded px-3 py-2 @error('Nombre') border-red-500 @enderror" />
                        @error('Nombre')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="Apellido" class="block text-gray-700 font-bold mb-2">Apellido</label>
                        <input type="text" name="Apellido" id="Apellido" required
                               value="{{ old('Apellido') }}"
                               class="w-full border rounded px-3 py-2 @error('Apellido') border-red-500 @enderror" />
                        @error('Apellido')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="FechaNacimiento" class="block text-gray-700 font-bold mb-2">Fecha de Nacimiento</label>
                        <input type="date" name="FechaNacimiento" id="FechaNacimiento" required
                               value="{{ old('FechaNacimiento') }}"
                               class="w-full border rounded px-3 py-2 @error('FechaNacimiento') border-red-500 @enderror" />
                        @error('FechaNacimiento')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-gray-700 font-bold mb-2">Género</label>
                        <label class="inline-flex items-center mr-4">
                            <input type="radio" name="Genero" value="F" required
                                   {{ old('Genero') == 'F' ? 'checked' : '' }}
                                   class="form-radio text-pink-600">
                            <span class="ml-2">Femenino</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="Genero" value="M" required
                                   {{ old('Genero') == 'M' ? 'checked' : '' }}
                                   class="form-radio text-blue-600">
                            <span class="ml-2">Masculino</span>
                        </label>

                        @error('Genero')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="CorreoElectronico" class="block text-gray-700 font-bold mb-2">Correo Electrónico</label>
                        <input type="email" name="CorreoElectronico" id="CorreoElectronico" required
                               value="{{ old('CorreoElectronico') }}"
                               class="w-full border rounded px-3 py-2 @error('CorreoElectronico') border-red-500 @enderror" />
                        @error('CorreoElectronico')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Campos de Teléfonos --}}
                    <div class="border-t border-gray-300 pt-4">
                        <h4 class="text-md font-semibold mb-2 text-gray-700">Teléfonos</h4>

                        <template x-for="(telefono, index) in telefonos" :key="index">
                            <div class="grid grid-cols-2 gap-2 mb-3">
                                <div>
                                    <label :for="'Tipo' + index" class="block text-gray-600 text-sm font-bold mb-1">Tipo</label>
                                    <select :name="'telefonos[' + index + '][Tipo]'" class="w-full border rounded px-2 py-1" x-model="telefono.Tipo">
                                        <option value="Personal">Personal</option>
                                        <option value="Trabajo">Trabajo</option>
                                        <option value="Otro">Otro</option>
                                    </select>
                                </div>
                                <div>
                                    <label :for="'Numero' + index" class="block text-gray-600 text-sm font-bold mb-1">Número</label>
                                    <input type="text" :name="'telefonos[' + index + '][Numero]'" class="w-full border rounded px-2 py-1" x-model="telefono.Numero" />
                                </div>
                                <div class="col-span-2 flex justify-end">
                                    <button type="button" @click="removeTelefono(index)" class="text-red-600 hover:underline text-sm">Eliminar</button>
                                </div>
                            </div>
                        </template>

                        <button type="button" @click="addTelefono()" class="text-sm text-blue-600 hover:underline mt-2">
                            + Agregar otro teléfono
                        </button>
                    </div>

                    <div class="flex justify-end gap-2 mt-4">
                        <button type="button" @click="closeCreateModal()"
                                class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400">Cancelar
                        </button>
                        <button type="submit"
                                class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Editar --}}
        <div
            x-show="isEditModalOpen"
            x-transition
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
            @keydown.escape.window="closeEditModal()"
            style="display: none;"
            x-cloak
        >
            <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative max-h-[90vh] overflow-y-auto">
                <button
                    @click="closeEditModal()"
                    class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-2xl font-bold"
                    title="Cerrar"
                >&times;</button>

                <div class="flex items-center gap-2 mb-4 text-yellow-600 text-2xl">
                    <i class="fas fa-user-edit"></i>
                    <h3 class="font-semibold">Editar Persona</h3>
                </div>

                <form method="POST" :action="actionUrl()" class="space-y-4" novalidate>
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="Nombre" class="block text-gray-700 font-bold mb-2">Nombre</label>
                        <input type="text" name="Nombre" id="Nombre" required
                               x-model="editForm.Nombre"
                               class="w-full border rounded px-3 py-2" />
                    </div>

                    <div>
                        <label for="Apellido" class="block text-gray-700 font-bold mb-2">Apellido</label>
                        <input type="text" name="Apellido" id="Apellido" required
                               x-model="editForm.Apellido"
                               class="w-full border rounded px-3 py-2" />
                    </div>

                    <div>
                        <label for="FechaNacimiento" class="block text-gray-700 font-bold mb-2">Fecha de Nacimiento</label>
                        <input type="date" name="FechaNacimiento" id="FechaNacimiento" required
                               x-model="editForm.FechaNacimiento"
                               class="w-full border rounded px-3 py-2" />
                    </div>

                    <div>
                        <label class="block text-gray-700 font-bold mb-2">Género</label>
                        <label class="inline-flex items-center mr-4">
                            <input type="radio" name="Genero" value="F" x-model="editForm.Genero"
                                   class="form-radio text-pink-600">
                            <span class="ml-2">Femenino</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="Genero" value="M" x-model="editForm.Genero"
                                   class="form-radio text-blue-600">
                            <span class="ml-2">Masculino</span>
                        </label>
                    </div>

                    <div>
                        <label for="CorreoElectronico" class="block text-gray-700 font-bold mb-2">Correo Electrónico</label>
                        <input type="email" name="CorreoElectronico" id="CorreoElectronico" required
                               x-model="editForm.CorreoElectronico"
                               class="w-full border rounded px-3 py-2" />
                    </div>

                    {{-- Teléfonos --}}
                    <div class="border-t border-gray-300 pt-4">
                        <h4 class="text-md font-semibold mb-2 text-gray-700">Teléfonos</h4>

                        <template x-for="(telefono, index) in editForm.telefonos" :key="index">
                            <div class="grid grid-cols-2 gap-2 mb-3">
                                <div>
                                    <label :for="'TipoEditar' + index" class="block text-gray-600 text-sm font-bold mb-1">Tipo</label>
                                    <select :name="'telefonos[' + index + '][Tipo]'" class="w-full border rounded px-2 py-1" x-model="telefono.Tipo">
                                        <option value="Personal">Personal</option>
                                        <option value="Trabajo">Trabajo</option>
                                        <option value="Otro">Otro</option>
                                    </select>
                                </div>
                                <div>
                                    <label :for="'NumeroEditar' + index" class="block text-gray-600 text-sm font-bold mb-1">Número</label>
                                    <input type="text" :name="'telefonos[' + index + '][Numero]'" class="w-full border rounded px-2 py-1" x-model="telefono.Numero" />
                                </div>
                                <div class="col-span-2 flex justify-end">
                                    <button type="button" @click="removeTelefonoEdit(index)" class="text-red-600 hover:underline text-sm">Eliminar</button>
                                </div>
                            </div>
                        </template>

                        <button type="button" @click="addTelefonoEdit()" class="text-sm text-blue-600 hover:underline mt-2">
                            + Agregar otro teléfono
                        </button>
                    </div>

                    <div class="flex justify-end gap-2 mt-4">
                        <button type="button" @click="closeEditModal()"
                                class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400">Cancelar
                        </button>
                        <button type="submit"
                                class="px-4 py-2 rounded bg-yellow-600 text-white hover:bg-yellow-700">Actualizar
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    {{-- Alpine.js --}}
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <script>
    function personasModal() {
        return {
            search: @json(request('search')),
            isCreateModalOpen: false,
            isEditModalOpen: false,

            telefonos: [
                { Tipo: 'Personal', Numero: '' }
            ],

            editForm: {
                PersonaID: '',
                Nombre: '',
                Apellido: '',
                FechaNacimiento: '',
                Genero: '',
                CorreoElectronico: '',
                telefonos: []
            },

            openCreateModal() {
                this.isCreateModalOpen = true;
                this.telefonos = [{ Tipo: 'Personal', Numero: '' }];
            },

            closeCreateModal() {
                this.isCreateModalOpen = false;
            },

            openEditModal(persona) {
                this.editForm = {
                    PersonaID: persona.PersonaID,
                    Nombre: persona.Nombre,
                    Apellido: persona.Apellido,
                    FechaNacimiento: persona.FechaNacimiento,
                    Genero: persona.Genero.trim().toUpperCase(),
                    CorreoElectronico: persona.CorreoElectronico,
                    telefonos: persona.telefonos ? JSON.parse(JSON.stringify(persona.telefonos)) : []
                };
                this.isEditModalOpen = true;
            },

            closeEditModal() {
                this.isEditModalOpen = false;
            },

            addTelefono() {
                this.telefonos.push({ Tipo: 'Personal', Numero: '' });
            },

            removeTelefono(index) {
                this.telefonos.splice(index, 1);
            },

            addTelefonoEdit() {
                this.editForm.telefonos.push({ Tipo: 'Personal', Numero: '' });
            },

            removeTelefonoEdit(index) {
                this.editForm.telefonos.splice(index, 1);
            },

            actionUrl() {
                return `/persona/${this.editForm.PersonaID}`;
            }
        }
    }
    </script>
</x-app-layout>





