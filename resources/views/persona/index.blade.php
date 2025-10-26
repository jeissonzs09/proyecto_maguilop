<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold flex items-center gap-2">
            <i class="fas fa-users"></i> Personas
        </h2>
    </x-slot>
<div x-data="personasModal()">
    @php
        $permisos = \App\Helpers\PermisosHelper::class;
    @endphp

    <div 
        x-data="personasModal()" 
        class="p-4"
        x-init="
            @if ($errors->any() && session()->getOldInput('_action') === 'create')
                isCreateModalOpen = true
            @elseif ($errors->any() && session()->getOldInput('_action') === 'edit')
                isEditModalOpen = true
            @endif
        "
    >

        {{-- Búsqueda y botón Nuevo --}}
        <div class="flex justify-between items-center mb-6">
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

                <a href="{{ route('persona.exportarPDF', ['search' => request('search')]) }}"
                   class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md shadow whitespace-nowrap">
                    <i class="fas fa-file-pdf"></i> Exportar PDF
                </a>

<!-- Botón Excel -->
    <a href="{{ route('persona.exportarExcel', ['search' => request('search')]) }}"
       class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md shadow whitespace-nowrap">
        <i class="fas fa-file-excel"></i> Exportar Excel
    </a>

            </div>



            
            @if($permisos::tienePermiso('Persona', 'crear'))
                <div>
                    <button
                        @click="openCreateModal()"
                        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow whitespace-nowrap"
                    >
                        <i class="fas fa-plus"></i> Nueva Persona
                    </button>
                </div>
            @endif
        </div>

       @if(request('success'))
<div 
    x-data="{ show: true }" 
    x-show="show" 
    x-transition:enter="transform ease-out duration-300 transition"
    x-transition:enter-start="scale-95 opacity-0"
    x-transition:enter-end="scale-100 opacity-100"
    x-transition:leave="transform ease-in duration-300 transition"
    x-transition:leave-start="scale-100 opacity-100"
    x-transition:leave-end="scale-95 opacity-0"
    x-init="setTimeout(() => show = false, 3000)" 
    class="fixed inset-0 flex items-center justify-center z-50"
>
    <div class="bg-green-600 text-white px-8 py-5 rounded-full shadow-2xl flex items-center space-x-6">
        <span class="flex items-center justify-center w-10 h-10 border-4 border-white rounded-full bg-transparent">
            <svg class="w-5 h-5" fill="none" stroke="white" stroke-width="5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
        </span>
        <span>{{ request('success') }}</span>
    </div>
</div>
@endif



        
        {{-- Tabla de personas --}}
        <div class="overflow-x-auto bg-white rounded-lg shadow mt-4">
            <table class="min-w-full text-sm text-gray-800">
                <thead class="bg-orange-500 text-white text-sm ">
                    <tr>
                        <th class="px-4 py-3 text-center">ID</th>
                        <th class="px-4 py-3 text-center">Nombre</th>
                        <th class="px-4 py-3 text-center">Apellido</th>
                        <th class="px-4 py-3 text-center">Fecha Nac.</th>
                        <th class="px-4 py-3 text-center">Género</th>
                        <th class="px-4 py-3 text-center">Correo Electrónico</th>
                        <th class="px-4 py-3 text-center">Teléfonos</th>
                        <th class="px-4 py-3 text-center">Estado</th>
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
                            <td class="px-4 py-2">{{ $persona->email }}</td>
                            <td class="px-4 py-2">
                                <ul>
                                    @foreach($persona->telefonos as $telefono)
                                        <li>{{ $telefono->Tipo }}: {{ $telefono->Numero }}</li>
                                    @endforeach
                                </ul>
                            </td>


<td class="px-4 py-2 text-center">
    @if($persona->Activo)
        <span class="text-green-600 font-semibold">Activa</span>
    @else
        <span class="text-red-600 font-semibold">Inactiva</span>
    @endif
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

<a href="{{ route('personas.toggle', $persona->PersonaID) }}"
   class="p-2 rounded-full text-white 
          {{ $persona->Activo ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }}"
   title="{{ $persona->Activo ? 'Desactivar' : 'Activar' }}">
    <i class="fas {{ $persona->Activo ? 'fa-ban' : 'fa-check' }}"></i>
</a>




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

      {{-- Controles de paginación y cantidad por página --}}
<div class="flex items-center justify-between mb-2">
    {{-- Número de registros por página --}}
    <form method="GET" class="flex items-center gap-2">
        <label for="perPage" class="font-semibold text-gray-700">Mostrar</label>
        <input type="number" name="perPage" id="perPage"
               class="border border-gray-300 rounded px-2 py-1 w-10"
               value="{{ request('perPage', 10) }}"
               min="1"
               onchange="this.form.submit()">
        <span class="text-gray-600">registros por página</span>

        {{-- Mantener búsqueda --}}
        <input type="hidden" name="search" value="{{ request('search') }}">
    </form>

    {{-- Paginación --}}
    <div>
        {{ $personas->appends(['search' => request('search'), 'perPage' => request('perPage', 10)])->links() }}
    </div>
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
                    <input type="hidden" name="_action" value="create" />

                    <div>
                        <label for="Nombre" class="block text-gray-700 font-bold mb-2">Nombre</label>
                        <input
    type="text" name="Nombre" id="Nombre" required
    x-model="createForm.Nombre"
    maxlength="60"
    pattern="^[A-Za-z ]{2,}$"
    title="Solo letras (A-Z) y espacios, mínimo 2 caracteres"
    @input="
    let input = $event.target;
    let valor = input.value.replace(/[^A-Za-z ]/g, '').replace(/\s+/g, ' ').trimStart();

    // Detectar 3 o más letras iguales seguidas
    if (/(.)\1\1+/i.test(valor)) {
        errorNombreRepetido = true;

        // Eliminar el último carácter que rompió la regla
        input.value = createForm.Nombre; // Restaurar valor anterior válido
    } else {
        createForm.Nombre = valor;
        errorNombreRepetido = false;
    }
"
    @keydown.enter="focusNext($event)"
    @keydown.tab="focusNext($event)"
    class="w-full border rounded px-3 py-2"
    :class="{ 'border-red-500': errorNombreRepetido }"
/>

<template x-if="errorNombreRepetido">
    <p class="text-red-500 text-sm mt-1">
        No se permiten más de 2 letras iguales seguidas.
    </p>
</template>


                        @error('Nombre')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="Apellido" class="block text-gray-700 font-bold mb-2">Apellido</label>
                        <input
                            type="text" name="Apellido" id="Apellido" required
                            value="{{ old('Apellido') }}"
                            x-model="createForm.Apellido"
                            maxlength="60"
                            pattern="^[A-Za-z ]{2,}$"
                            title="Solo letras (A-Z) y espacios, mínimo 2 caracteres"
                            @input="
                                createForm.Apellido = $event.target.value
                                  .replace(/[^A-Za-z ]/g,'')
                                  .replace(/\s+/g,' ')
                                  .trimStart()
                            "
                            @keydown.enter="focusNext($event)"
    @keydown.tab="focusNext($event)"
                            class="w-full border rounded px-3 py-2 @error('Apellido') border-red-500 @enderror"
                        />
                        @error('Apellido')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                        <template x-if="/(.)\1\1+/i.test(createForm.Apellido)">
    <p class="text-red-500 text-sm mt-1">
        No se permiten más de 2 letras iguales seguidas.
    </p>
</template>
                    </div>

                    <div>
                        <label for="FechaNacimiento" class="block text-gray-700 font-bold mb-2">Fecha de Nacimiento</label>
                        <input
                            type="date" name="FechaNacimiento" id="FechaNacimiento" required
                            value="{{ old('FechaNacimiento') }}"
                            x-model="createForm.FechaNacimiento"
                            @keydown.enter="focusNext($event)"
    @keydown.tab="focusNext($event)"
                            class="w-full border rounded px-3 py-2 @error('FechaNacimiento') border-red-500 @enderror"
                        />
                        @error('FechaNacimiento')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
<template x-if="createForm.FechaNacimiento && !esMayorDeEdad">
    <p class="text-red-500 text-sm mt-1">
        Debe tener al menos 18 años de edad.
    </p>
</template>                        
                    </div>

                    <div>
                        <label class="block text-gray-700 font-bold mb-2">Género</label>
                        <label class="inline-flex items-center mr-4">
                            <input type="radio" name="Genero" value="F" required
                                   {{ old('Genero') == 'F' ? 'checked' : '' }}
                                   x-model="createForm.Genero"
                                   class="form-radio text-pink-600" />
                            <span class="ml-2">Femenino</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="Genero" value="M" required
                                   {{ old('Genero') == 'M' ? 'checked' : '' }}
                                   x-model="createForm.Genero"
                                   class="form-radio text-blue-600" />
                            <span class="ml-2">Masculino</span>
                        </label>
                        @error('Genero')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="CorreoElectronico" class="block text-gray-700 font-bold mb-2">Correo Electrónico</label>
                        <input
    type="email" name="email" id="CorreoElectronico" required
    x-model="createForm.email"
    maxlength="100"
    pattern="^[^\s@]{4,}@[^\s@]{4,}\.[^\s@]+$"
    title="Debe tener al menos 4 caracteres antes y después de @ y un dominio válido"
    @input="createForm.CorreoElectronico = $event.target.value.trim()"
    @keydown.enter="focusNext($event)"
    @keydown.tab="focusNext($event)"
    class="w-full border rounded px-3 py-2"
    :class="{
        'border-red-500': createForm.CorreoElectronico && !/^[^\s@]{4,}@[^\s@]{4,}\.[^\s@]+$/.test(createForm.CorreoElectronico),
        'border-gray-300': !(createForm.CorreoElectronico && !/^[^\s@]{4,}@[^\s@]{4,}\.[^\s@]+$/.test(createForm.CorreoElectronico))
    }"
/>

<p
    x-show="createForm.CorreoElectronico && !/^[^\s@]{4,}@[^\s@]{4,}\.[^\s@]+$/.test(createForm.CorreoElectronico)"
    class="text-red-600 text-xs mt-1"
>
    El correo ingresado no cumple con los requisitos
</p>

<p
    x-show="createForm.CorreoElectronico && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(createForm.email)"
    class="text-red-600 text-xs mt-1"
>
    Ingresa un correo valido que lleve @ y dominio (ej. usuario@dominio.com).
</p>
@error('email')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror

                    </div>

                    {{-- Campos de Teléfonos --}}
                    <div class="border-t border-gray-300 pt-4">
                        <h4 class="text-md font-semibold mb-2 text-gray-700">Teléfonos</h4>

                        <template x-for="(telefono, index) in telefonos" :key="index">
                            
                            <div class="grid grid-cols-2 gap-2 mb-3">
                                <div>
                                    <label :for="'Tipo' + index" class="block text-gray-600 text-sm font-bold mb-1">Tipo</label>
                                    <select
                                        :name="'telefonos[' + index + '][Tipo]'"
                                        class="w-full border rounded px-2 py-1"
                                        x-model="telefono.Tipo"
                                    >
                                        <option value="Personal">Personal</option>
                                        <option value="Trabajo">Trabajo</option>
                                        <option value="Otro">Otro</option>
                                    </select>
                                </div>
                                <div>
    <label :for="'Numero' + index" class="block text-gray-600 text-sm font-bold mb-1">Número</label>
    <input
        type="text"
        :name="'telefonos[' + index + '][Numero]'"
        class="w-full border rounded px-2 py-1"
        x-model="telefono.Numero"
        maxlength="8"
        pattern="^\d{8}$"
        title="Número de 8 dígitos"
        @input="telefono.Numero = $event.target.value.replace(/\D/g,'').slice(0,8)"
        @keydown.enter="focusNext($event)"
    @keydown.tab="focusNext($event)"
    />

    <template x-if="telefonoErrors[index] && telefonoErrors[index].Numero">
        <p class="text-red-500 text-sm mt-1" x-text="telefonoErrors[index].Numero"></p>
    </template>

@foreach(old('telefonos', $telefonos ?? []) as $i => $tel)
    
        <!-- Mensaje de error solo -->
        @error("telefonos.$i.Numero")
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
@endforeach





</div>

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
                                :disabled="
    !/^[A-Za-z ]{2,}$/.test(createForm.Nombre) ||
    /(.)\1\1+/i.test(createForm.Nombre) ||
    !/^[A-Za-z ]{2,}$/.test(createForm.Apellido) ||
    /(.)\1\1+/i.test(createForm.Apellido) ||
    !createForm.FechaNacimiento ||
    !esMayorDeEdad ||
    !/^[FM]$/.test(createForm.Genero || '') ||
    !/^[^\s@]{4,}@[^\s@]{4,}\.[^\s@]+$/.test(createForm.email || '') ||
    telefonos.length === 0 ||
    telefonos.some(t => !/^\d{8}$/.test(t.Numero || ''))
"

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
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    @keydown.escape.window="closeEditModal()"
    x-cloak
>
    <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative max-h-[90vh] overflow-y-auto">
        <button
            @click="closeEditModal()"
            class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-2xl font-bold"
            title="Cerrar"
        >&times;</button>

        <div class="flex items-center gap-2 mb-4 text-blue-600 text-2xl">
            <i class="fas fa-user-edit"></i>
            <h3 class="font-semibold">Editar Persona</h3>
        </div>
<div x-show="editSuccessMessage"
     class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4"
     x-text="editSuccessMessage"
     x-transition></div>


       <form class="space-y-4" novalidate @submit.prevent="submitEditForm()">


            <!-- Nombre -->
            <div>
                <label class="block text-gray-700 font-bold mb-2">Nombre</label>
                <input type="text" x-model="editForm.Nombre"
                       class="w-full border rounded px-3 py-2"
                       :class="{ 'border-red-500': errorNombreRepetidoEdit || errors.Nombre }"
                       @input="
                           let valor = $event.target.value.replace(/[^A-Za-z ]/g,'').replace(/\s+/g,' ').trimStart();
                           if(/(.)\1\1+/i.test(valor)){ errorNombreRepetidoEdit = true; $event.target.value = editForm.Nombre; }
                           else { editForm.Nombre = valor; errorNombreRepetidoEdit = false; }
                           @keydown.enter="focusNext($event)"
    @keydown.tab="focusNext($event)"
                       "/>
                <template x-if="errorNombreRepetidoEdit">
                    <p class="text-red-500 text-sm mt-1">No se permiten más de 2 letras iguales seguidas.</p>
                </template>
                <template x-if="errors.Nombre">
                    <p class="text-red-500 text-sm mt-1" x-text="errors.Nombre[0]"></p>
                </template>
            </div>

            <!-- Apellido -->
            <div>
                <label class="block text-gray-700 font-bold mb-2">Apellido</label>
                <input type="text" x-model="editForm.Apellido"
                       class="w-full border rounded px-3 py-2"
                       :class="{ 'border-red-500': errorApellidoRepetidoEdit || errors.Apellido }"
                       @input="
                           let valor = $event.target.value.replace(/[^A-Za-z ]/g,'').replace(/\s+/g,' ').trimStart();
                           if(/(.)\1\1+/i.test(valor)){ errorApellidoRepetidoEdit = true; }
                           else { editForm.Apellido = valor; errorApellidoRepetidoEdit = false; }
                           @keydown.enter="focusNext($event)"
    @keydown.tab="focusNext($event)"
                       "/>
                <template x-if="errorApellidoRepetidoEdit">
                    <p class="text-red-500 text-sm mt-1">No se permiten más de 2 letras iguales seguidas.</p>
                </template>
                <template x-if="errors.Apellido">
                    <p class="text-red-500 text-sm mt-1" x-text="errors.Apellido[0]"></p>
                </template>
            </div>

            <!-- Fecha de nacimiento -->
            <div>
                <label class="block text-gray-700 font-bold mb-2">Fecha de Nacimiento</label>
                <input type="date" x-model="editForm.FechaNacimiento"
                       class="w-full border rounded px-3 py-2"
                       :class="{ 'border-red-500': editForm.FechaNacimiento && !esMayorDeEdadEdit || errors.FechaNacimiento }"
                       @input="esMayorDeEdadEdit = calcularMayorEdad(editForm.FechaNacimiento)"
                       @keydown.enter="focusNext($event)"
    @keydown.tab="focusNext($event)"/>
                <template x-if="editForm.FechaNacimiento && !esMayorDeEdadEdit">
                    <p class="text-red-500 text-sm mt-1">Debe tener al menos 18 años de edad.</p>
                </template>
                <template x-if="errors.FechaNacimiento">
                    <p class="text-red-500 text-sm mt-1" x-text="errors.FechaNacimiento[0]"></p>
                </template>
            </div>

            <!-- Género -->
            <div>
                <label class="block text-gray-700 font-bold mb-2">Género</label>
                <label class="inline-flex items-center mr-4">
                    <input type="radio" value="F" x-model="editForm.Genero" class="form-radio text-pink-600" />
                    <span class="ml-2">Femenino</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="radio" value="M" x-model="editForm.Genero" class="form-radio text-blue-600" />
                    <span class="ml-2">Masculino</span>
                </label>
            </div>

            <!-- Correo -->
            <div>
                <label class="block text-gray-700 font-bold mb-2">Correo Electrónico</label>
                <input type="email" x-model="editForm.email"
                       class="w-full border rounded px-3 py-2"
                       :class="{ 'border-red-500': editForm.email && !/^[^\s@]{4,}@[^\s@]{4,}\.[^\s@]+$/.test(editForm.email) || errors.email }"/>
                <template x-if="editForm.email && !/^[^\s@]{4,}@[^\s@]{4,}\.[^\s@]+$/.test(editForm.email)">
                    <p class="text-red-600 text-xs mt-1">El correo debe tener al menos 4 caracteres antes y después de la @ y un dominio válido.</p>
                </template>
                <template x-if="errors.email">
                    <p class="text-red-500 text-sm mt-1" x-text="errors.email[0]"></p>
                </template>
            </div>

            <!-- Teléfonos -->
            <div class="border-t border-gray-300 pt-4">
                <h4 class="text-md font-semibold mb-2 text-gray-700">Teléfonos</h4>

                <template x-for="(telefono, index) in editForm.telefonos" :key="index">
                    <div class="grid grid-cols-2 gap-2 mb-3">
                        <div>
                            <label class="block text-gray-600 text-sm font-bold mb-1">Tipo</label>
                            <select :name="'telefonos['+index+'][Tipo]'" class="w-full border rounded px-2 py-1" x-model="telefono.Tipo">
                                <option value="Personal">Personal</option>
                                <option value="Trabajo">Trabajo</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-600 text-sm font-bold mb-1">Número</label>
                            <input type="text" :name="'telefonos['+index+'][Numero]'" x-model="telefono.Numero"
    maxlength="8" pattern="^\d{8}$"
    class="w-full border rounded px-2 py-1"
    @input="telefono.Numero = telefono.Numero.replace(/\D/g, '').slice(0, 8); validarDuplicados()"
    @keydown.enter="focusNext($event)"
    @keydown.tab="focusNext($event)"/>
                            <template x-if="telefono.errorDuplicado">
                                <p class="text-red-500 text-sm mt-1">Este número ya existe.</p>
                            </template>
                            <template x-if="errors[telefonos.${index}.Numero]">
                    <p class="text-red-500 text-sm mt-1"
                       x-text="errors[telefonos.${index}.Numero][0]"></p>
                </template>
                        </div>
                        <div class="col-span-2 flex justify-end">
                            <button type="button" @click="editForm.telefonos.splice(index,1); validarDuplicados()" class="text-red-600 hover:underline text-sm">Eliminar</button>
                        </div>
                    </div>
                </template>

                <button type="button" @click="editForm.telefonos.push({Tipo:'Personal',Numero:''}); validarDuplicados()" class="text-sm text-blue-600 hover:underline mt-2">
                    + Agregar otro teléfono
                </button>
            </div>

            <!-- Botones -->
            <div class="flex justify-end gap-2 mt-4">
        <button type="button" @click="closeEditModal()" class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400">Cancelar</button>

        <button type="submit"
    class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
    :disabled="
        !/^[A-Za-z ]{2,}$/.test(editForm.Nombre || '') ||
        /(.)\1\1+/i.test(editForm.Nombre || '') ||
        !/^[A-Za-z ]{2,}$/.test(editForm.Apellido || '') ||
        /(.)\1\1+/i.test(editForm.Apellido || '') ||
        !editForm.FechaNacimiento ||
        !esMayorDeEdadEdit ||
        !/^[FM]$/.test(editForm.Genero || '') ||
        !/^[^\s@]{4,}@[^\s@]{4,}\.[^\s@]+$/.test(editForm.email || '') ||
        (editForm.telefonos || []).length === 0 ||
        (editForm.telefonos || []).some(t => !/^\d{8}$/.test(t.Numero || '')) ||
        (editForm.telefonos || []).some(t => t.errorDuplicado)
    ">
Actualizar
</button>
            </div>
        </form>
    </div>
</div>




    {{-- Alpine.js --}}
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <script>
   function personasModal() {

    function calcularEdad(fecha) {
        if (!fecha) return false;

        const nacimiento = new Date(fecha);
        const hoy = new Date();
        let edad = hoy.getFullYear() - nacimiento.getFullYear();
        const m = hoy.getMonth() - nacimiento.getMonth();

        if (m < 0 || (m === 0 && hoy.getDate() < nacimiento.getDate())) {
            edad--;
        }

        return edad >= 18;
    }

    return {
        search: @json(request('search')),
        isCreateModalOpen: false,
        isEditModalOpen: false,
        editSuccessMessage: '', // <-- Mensaje de éxito para editar


        errorNombreRepetido: false,
        errorNombreRepetidoEdit: false,
        errorApellidoRepetidoEdit: false,

        

        // Estado para crear
        createForm: {
            Nombre: @json(old('Nombre', '')),
            Apellido: @json(old('Apellido', '')),
            FechaNacimiento: @json(old('FechaNacimiento', '')),
            Genero: @json(old('Genero', '')),
            email: @json(old('email', '')),
        },

        // Teléfonos (crear)
        telefonos: @json(old('telefonos', [ ['Tipo' => 'Personal', 'Numero' => ''] ])),

        // Estado para editar
        editForm: {
            PersonaID: '',
            Nombre: '',
            Apellido: '',
            FechaNacimiento: '',
            Genero: '',
            email: '',
            telefonos: []
        },

        // Validación edad mínima
        get esMayorDeEdad() {
            return calcularEdad(this.createForm.FechaNacimiento);
        },

        get esMayorDeEdadEdit() {
            return calcularEdad(this.editForm.FechaNacimiento);
        },

        openCreateModal() {
            this.isCreateModalOpen = true;
        },

        closeCreateModal() {
            this.isCreateModalOpen = false;
        },

        openEditModal(persona) {
            this.editForm = {
                PersonaID: persona.PersonaID,
                Nombre: (persona.Nombre || ''),
                Apellido: (persona.Apellido || ''),
                FechaNacimiento: persona.FechaNacimiento || '',
                Genero: (persona.Genero || '').trim().toUpperCase(),
                email: persona.email || '',
                telefonos: persona.telefonos ? JSON.parse(JSON.stringify(persona.telefonos)) : []
            };

// 🔹 Limpiar errores del backend
    this.errors = {};

    // 🔹 Limpiar errores locales
    this.errorNombreRepetidoEdit = false;
    this.errorApellidoRepetidoEdit = false;
    this.editForm.telefonos.forEach(tel => tel.errorDuplicado = false);



            this.isEditModalOpen = true;
        },

        closeEditModal() {
            this.isEditModalOpen = false;
        },


async submitEditForm() {
    // Definimos una variable para la URL de la petición.
    const url = /persona/${this.editForm.PersonaID}; 
    
    try {
        // Prepara los datos para enviar (incluye el _method y el token CSRF para Laravel)
        const dataToSend = {
            ...this.editForm,
            _method: 'PUT', // Usamos PUT para coincidir con tu Route::put()
            _token: document.querySelector('meta[name="csrf-token"]').content
        };

        // Reseteamos errores de validación antes de la nueva petición.
        this.errors = {}; // Asume que tienes una variable 'errors' en tu x-data para mostrar errores
        
        const response = await fetch(url, {
            method: 'POST', 
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify(dataToSend)
        });

        // ======================================
        // MANEJO DE RESPUESTAS
        // ======================================

        if (response.ok) {
    // 1. Cerrar modal
    this.closeEditModal();

    // 2. Usar sesión Laravel para mensaje
    window.location.href = window.location.pathname + "?success=Registro actualizado con éxito";





        } else if (response.status === 422) {
            // ERROR DE VALIDACIÓN (Status 422 Unprocessable Entity):
            const errorResult = await response.json();
            console.error('⚠ Error de validación del servidor:', errorResult);
            
            // Asigna los errores a la variable 'errors' para mostrarlos en el formulario.
            this.errors = errorResult.errors || {}; 
            
        } else {
            // OTROS ERRORES DEL SERVIDOR (404, 500, etc.):
            const errorResult = await response.json();
            console.error(❌ Error ${response.status} del servidor:, errorResult);
            
            // Aquí podrías mostrar un mensaje de error genérico al usuario
        }

    } catch (error) {
        // ERROR DE RED O CONEXIÓN:
        console.error('❌ Error al intentar enviar la petición:', error);
    } 
    // Nota: El 'finally' no es estrictamente necesario si solo reseteas el estado de carga.
    /* finally {
        this.isProcessing = false;
    } */
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
            return /persona/${this.editForm.PersonaID};
        },

focusNext(event) {
    const tag = event.target.tagName.toLowerCase();
    const type = event.target.type;

    // Solo interceptar inputs de texto, email, date, number
    if (tag === 'input' && ['text','email','date','number'].includes(type)) {
        event.preventDefault(); // evita submit prematuro
        const form = event.target.form;
        const index = Array.prototype.indexOf.call(form.elements, event.target);
        const next = form.elements[index + 1];

        // Mover foco al siguiente input, solo si existe
        if (next) next.focus();
    }
    // Si es un botón submit, no hacemos preventDefault y deja funcionar normalmente
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
        id="toast-persona"
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
            const toast = document.getElementById('toast-persona');
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