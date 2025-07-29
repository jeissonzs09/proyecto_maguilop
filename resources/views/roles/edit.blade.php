<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold flex items-center gap-2">
            <i class="fas fa-edit"></i> Editar Rol
        </h2>
    </x-slot>

    <div class="max-w-xl mx-auto mt-6 bg-white p-6 rounded-lg shadow">
        <form action="{{ route('roles.update', $rol->ID_Rol) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Descripción --}}
            <div class="mb-4">
                <label for="Descripcion" class="block text-sm font-medium text-gray-700">Descripción del Rol</label>
                <input type="text" name="Descripcion" id="Descripcion"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-orange-500 focus:border-orange-500"
                       value="{{ old('Descripcion', $rol->Descripcion) }}" required>
                @error('Descripcion')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Estado --}}
            <div class="mb-4">
                <label for="Estado" class="block text-sm font-medium text-gray-700">Estado</label>
                <select name="Estado" id="Estado"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-orange-500 focus:border-orange-500" required>
                    <option value="Activo" {{ $rol->Estado == 'Activo' ? 'selected' : '' }}>Activo</option>
                    <option value="Inactivo" {{ $rol->Estado == 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
                </select>
                @error('Estado')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Botones --}}
            <div class="flex justify-between mt-6">
                <a href="{{ route('roles.index') }}"
                   class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md shadow">
                    <i class="fas fa-times"></i> Cancelar
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow">
                    <i class="fas fa-save"></i> Actualizar
                </button>
            </div>
        </form>
    </div>
</x-app-layout>

