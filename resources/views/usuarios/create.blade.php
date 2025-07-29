<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
            <i class="fas fa-user-plus"></i> Nuevo Usuario
        </h2>
    </x-slot>

    <div class="p-6 max-w-xl mx-auto bg-white rounded shadow">
        <form method="POST" action="{{ route('usuarios.store') }}">
            @csrf

            {{-- Nombre de Usuario --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Nombre de Usuario</label>
                <input type="text" name="NombreUsuario" value="{{ old('NombreUsuario') }}" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" />
                @error('NombreUsuario')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Rol --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Rol</label>
                <select name="TipoUsuario" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <option value="">Seleccione un rol</option>
                    @foreach($roles as $id => $descripcion)
                        <option value="{{ $descripcion }}" {{ old('TipoUsuario') == $descripcion ? 'selected' : '' }}>
                            {{ $descripcion }}
                        </option>
                    @endforeach
                </select>
                @error('TipoUsuario')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Correo Electrónico --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                <input type="email" name="correo" value="{{ old('correo') }}" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" />
                @error('correo')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Contraseña --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Contraseña</label>
                <input type="password" name="password" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" />
                @error('password')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Empleado --}}
            <div class="mb-4">
                <label for="EmpleadoID" class="block text-sm font-medium text-gray-700">Empleado</label>
                <select id="EmpleadoID" name="EmpleadoID"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <option value="">Seleccione un empleado</option>
                    @foreach($empleados as $empleado)
                        <option value="{{ $empleado->EmpleadoID }}" {{ old('EmpleadoID') == $empleado->EmpleadoID ? 'selected' : '' }}>
                            {{ $empleado->nombre_completo }}
                        </option>
                    @endforeach
                </select>
                @error('EmpleadoID')
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
                    <i class="fas fa-save"></i> Guardar usuario
                </button>
            </div>
        </form>
    </div>
</x-app-layout>


