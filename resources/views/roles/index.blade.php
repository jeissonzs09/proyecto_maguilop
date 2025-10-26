<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold flex items-center gap-2">
            <i class="fas fa-user-shield"></i> Gestión de Roles
        </h2>
    </x-slot>

    @php
        $permisos = \App\Helpers\PermisosHelper::class;
    @endphp

    <div class="p-4">

       {{-- Barra de búsqueda + Botón nuevo rol en la misma fila --}}
<div class="flex justify-between items-center mb-4 flex-wrap gap-3">
    {{-- Barra de búsqueda --}}
    <div class="relative max-w-xs w-full sm:w-64">
        <input
            type="text"
            x-data="{ search: '{{ request('search') }}' }"
            x-model="search"
            @input.debounce.500="window.location.href = '?search=' + encodeURIComponent(search)"
            placeholder="Buscar rol..."
            class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:outline-none text-sm"
        />
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m1.44-5.4a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>
    </div>

    {{-- Botón nuevo rol --}}
    @if($permisos::tienePermiso('Roles', 'crear'))
        <a href="{{ route('roles.create') }}"
           class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow">
            <i class="fas fa-plus-circle"></i> Nuevo rol
        </a>
    @endif
</div>

        {{-- Tabla de roles --}}
        <div class="overflow-x-auto bg-white rounded-lg shadow mt-4">
            <table class="min-w-full text-sm text-gray-800">
                <thead class="bg-orange-500 text-white text-base ">
                    <tr>
                        <th class="px-4 py-3 text-left">ID</th>
                        <th class="px-4 py-3 text-left">Descripción</th>
                        <th class="px-4 py-3 text-left">Estado</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($roles as $rol)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-2">{{ $rol->ID_Rol }}</td>
                            <td class="px-4 py-2">{{ $rol->Descripcion }}</td>
                            <td class="px-4 py-2">{{ $rol->Estado }}</td>
                            <td class="px-4 py-2 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    {{-- Editar --}}
                                    @if($permisos::tienePermiso('Roles', 'editar'))
                                        <a href="{{ route('roles.edit', $rol->ID_Rol) }}"
                                           class="bg-yellow-400 hover:bg-yellow-500 text-white p-2 rounded-full"
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif

                                    {{-- Eliminar --}}
                                    @if($permisos::tienePermiso('Roles', 'eliminar'))
                                        <form action="{{ route('roles.destroy', $rol->ID_Rol) }}" method="POST"
                                              onsubmit="return confirm('¿Estás seguro de eliminar este rol?')"
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="bg-red-600 hover:bg-red-700 text-white p-2 rounded-full"
                                                    title="Eliminar">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

<div class="mt-4 flex items-center justify-between flex-wrap gap-3">
    {{-- Formulario para indicar cantidad de productos por página --}}
    <form method="GET" class="flex items-center gap-2" id="perPageForm">
        {{-- Mantener búsqueda si la hay --}}
        @if(request()->filled('search'))
            <input type="hidden" name="search" value="{{ request('search') }}">
        @endif

        <label for="per_page" class="text-sm text-gray-700">Mostrar:</label>
        <input
            type="number"
            name="per_page"
            id="per_page"
            value="{{ request('per_page', 10) }}"
            min="1"
            max="500" {{-- opcional --}}
            class="w-20 border-gray-300 rounded-md shadow-sm text-sm text-center"
        >
        <span class="text-sm text-gray-700">productos por página</span>
    </form>

    {{-- Links de paginación --}}
    {{ $roles->links() }}
</div>

{{-- Script para enviar el formulario automáticamente --}}
<script>
document.getElementById('per_page').addEventListener('change', function() {
    document.getElementById('perPageForm').submit();
});
</script>

{{-- Toast estilo alerta roja cuando no se encuentra el rol --}}
@if(request()->filled('search') && $roles->isEmpty())
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
        <div class="bg-red-600 text-white px-5 py-5 rounded-full shadow-2xl flex items-center space-x-6">
            
            {{-- Ícono con círculo transparente, borde blanco grueso y X blanca --}}
            <span class="flex items-center justify-center w-10 h-10 border-4 border-white rounded-full bg-transparent">
                <svg class="w-5 h-5" fill="none" stroke="white" stroke-width="5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </span>

            {{-- Texto mensaje --}}
            <span class="text-lg font-semibold">El rol no fue encontrado</span>
        </div>
    </div>
@endif

    </div>
</x-app-layout>