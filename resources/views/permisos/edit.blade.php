<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold">✏️ Editar Permiso</h2>
    </x-slot>

    <div class="max-w-xl mx-auto mt-6 bg-white p-6 rounded shadow">
        <form action="{{ route('permisos.update', $permiso->PermisoID) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Nombre del permiso --}}
            <div class="mb-4">
                <label for="NombrePermiso" class="block font-semibold mb-1">Nombre del Permiso</label>
                <input type="text" name="NombrePermiso" id="NombrePermiso"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring"
                       value="{{ old('NombrePermiso', $permiso->NombrePermiso) }}" required>
            </div>

            {{-- Descripción --}}
            <div class="mb-4">
                <label for="Descripcion" class="block font-semibold mb-1">Descripción</label>
                <textarea name="Descripcion" id="Descripcion" rows="3"
                          class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring">{{ old('Descripcion', $permiso->Descripcion) }}</textarea>
            </div>

            {{-- Botones --}}
            <div class="flex justify-between mt-6">
                <a href="{{ route('permisos.index') }}"
                   style="background-color: #dc2626; color: white; padding: 10px 20px; border-radius: 8px;">
                    ← Cancelar
                </a>
                <button type="submit"
                        style="background-color: #2563eb; color: white; padding: 10px 20px; border-radius: 8px;">
                    💾 Actualizar
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
