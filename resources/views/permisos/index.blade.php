<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold">⚙️ Permisos</h2>
    </x-slot>

    <div class="p-4">
        {{-- Botón para crear nuevo permiso --}}
        <a href="{{ route('permisos.create') }}"
           style="background-color: #2563eb; color: white; padding: 10px 20px; border-radius: 8px; font-weight: bold; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            ➕ Nuevo permiso
        </a>

        {{-- Tabla de permisos --}}
        <table class="table-auto w-full mt-4 border rounded shadow">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="border px-4 py-2 text-center">ID</th>
                    <th class="border px-4 py-2 text-center">Nombre</th>
                    <th class="border px-4 py-2 text-center">Descripción</th>
                    <th class="border px-4 py-2 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($permisos as $permiso)
                <tr class="hover:bg-gray-50">
                    <td class="border px-4 py-2 text-center">{{ $permiso->PermisoID }}</td>
                    <td class="border px-4 py-2 text-center">{{ $permiso->NombrePermiso }}</td>
                    <td class="border px-4 py-2 text-center">{{ $permiso->Descripcion }}</td>
                    <td class="px-4 py-2 text-center space-x-2">
                        {{-- Botón Editar --}}
                        <a href="{{ route('permisos.edit', $permiso->PermisoID) }}"
                           style="background-color: #f59e0b; color: white; padding: 8px; border-radius: 50%; display: inline-block;"
                           title="Editar">
                            ✏️
                        </a>

                        {{-- Botón Eliminar --}}
                        <form action="{{ route('permisos.destroy', $permiso->PermisoID) }}" method="POST" class="inline-block"
                              onsubmit="return confirm('¿Estás seguro de eliminar este permiso?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    style="background-color: #dc2626; color: white; padding: 8px; border-radius: 50%; display: inline-block;"
                                    title="Eliminar">
                                🗑️
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
