<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
            <i class="fas fa-pen"></i> Editar Usuario
        </h2>
    </x-slot>

    <div class="max-w-xl mx-auto bg-white shadow-md rounded-lg p-6 mt-6">
        {{-- Mostrar errores de validación --}}
        @if ($errors->any())
            <x-alert type="error">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-alert>
        @endif

        {{-- Mostrar mensaje de éxito --}}
        @if (session('success'))
            <x-alert type="success">
                {{ session('success') }}
            </x-alert>
        @endif

        <form method="POST" action="{{ route('usuarios.update', $usuario->UsuarioID) }}">
            @csrf
            @method('PUT')

            {{-- Nombre de usuario --}}
            <div class="mb-4">
                <label for="nombre_usuario" class="block text-sm font-medium text-gray-700">Nombre de usuario</label>
                <input type="text" id="nombre_usuario" name="nombre_usuario"
                       value="{{ old('nombre_usuario', $usuario->NombreUsuario) }}"
                       minlength="3" maxlength="30" 
                       pattern="^[A-Z0-9_]+$"
                       title="Solo letras mayúsculas, números y guiones bajos (3-30 caracteres)"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 uppercase"
                       oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9_]/g, '')">
                <p class="text-xs text-gray-500 mt-1">Solo letras mayúsculas, números y guiones bajos (3-30 caracteres)</p>
                @error('nombre_usuario')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Correo electrónico --}}
            <div class="mb-4">
                <label for="correo" class="block text-sm font-medium text-gray-700">Correo electrónico</label>
                <input type="email" id="correo" name="correo"
                       value="{{ old('correo', $usuario->CorreoElectronico) }}"
                       minlength="5" maxlength="100"
                       pattern="^[^@\s]+@[^@\s]+\.[^@\s]+$"
                       title="Ingrese un correo válido sin espacios"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 lowercase"
                       oninput="this.value = this.value.toLowerCase().replace(/\s/g, '')">
                <p class="text-xs text-gray-500 mt-1">Correo válido sin espacios (5-100 caracteres)</p>
                @error('correo')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Rol dinámico --}}
            <div class="mb-4">
                <label for="rol" class="block text-sm font-medium text-gray-700">Rol</label>
                <select name="rol" id="rol"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <option value="">Seleccione un rol</option>
                    @foreach($roles as $descripcion)
                        <option value="{{ $descripcion }}" {{ $usuario->TipoUsuario == $descripcion ? 'selected' : '' }}>
                            {{ $descripcion }}
                        </option>
                    @endforeach
                </select>
                @error('rol')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Empleado --}}
            <div class="mb-4">
                <label for="empleado" class="block text-sm font-medium text-gray-700">Empleado vinculado</label>
                <select name="empleado" id="empleado"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <option value="">Seleccione un empleado</option>
                    @foreach($empleados as $empleado)
                        <option value="{{ $empleado->EmpleadoID }}" {{ $empleado->EmpleadoID == $usuario->EmpleadoID ? 'selected' : '' }}>
                            {{ $empleado->NombreCompleto }}
                        </option>
                    @endforeach
                </select>
                @error('empleado')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Fecha de Creación --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Fecha de Creación</label>
                <input type="datetime-local" name="FechaCreacion" 
                       value="{{ old('FechaCreacion', $usuario->FechaCreacion ? \Carbon\Carbon::parse($usuario->FechaCreacion)->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}" 
                       readonly
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100 cursor-not-allowed" />
                <p class="text-xs text-gray-500 mt-1">Fecha automática del sistema (no editable)</p>
            </div>

            {{-- Fecha de Vencimiento --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Fecha de Vencimiento</label>
                <input type="date" name="FechaVencimiento" 
                       value="{{ old('FechaVencimiento', $usuario->FechaVencimiento ? \Carbon\Carbon::parse($usuario->FechaVencimiento)->format('Y-m-d') : now()->addDays(365)->format('Y-m-d')) }}" 
                       min="{{ now()->format('Y-m-d') }}"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" />
                <p class="text-xs text-gray-500 mt-1">Fecha de vencimiento del usuario (mínimo: hoy)</p>
                @error('FechaVencimiento')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Botones --}}
            <div class="flex justify-between mt-6">
                <a href="{{ route('usuarios.index') }}"
                   class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded shadow">
                    <i class="fas fa-times"></i> Cancelar
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded shadow">
                    <i class="fas fa-save"></i> Guardar cambios
                </button>
            </div>
        </form>
    </div>
</x-app-layout>

