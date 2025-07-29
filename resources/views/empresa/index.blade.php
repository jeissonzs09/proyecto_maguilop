<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold flex items-center gap-2">
            <i class="fas fa-building"></i> Empresas
        </h2>
    </x-slot>

    @php
        $permisos = \App\Helpers\PermisosHelper::class;
    @endphp

    <div class="p-4">
        {{-- Mensaje de éxito --}}
        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded shadow text-sm">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            </div>
        @endif

        {{-- Mensaje de error --}}
        @if (session('error'))
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded shadow text-sm">
                <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
            </div>
        @endif

        {{-- Botones --}}
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                {{-- Buscador --}}
                <div class="relative max-w-xs w-full">
                    <input
                        type="text"
                        x-data="{ search: '{{ request('search') }}' }"
                        x-model="search"
                        @input.debounce.500="window.location.href = '?search=' + encodeURIComponent(search)"
                        placeholder="Buscar empresa..."
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

                {{-- Botón Exportar PDF alineado a la par del buscador --}}
                <a href="{{ route('empresa.exportarPDF', ['search' => request('search')]) }}"
                   class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md shadow whitespace-nowrap">
                    <i class="fas fa-file-pdf"></i> Exportar PDF
                </a>
            </div>

            {{-- Botón Nueva Empresa alineado a la derecha --}}
            @if($permisos::tienePermiso('Empresas', 'crear'))
                <a href="{{ route('empresa.create') }}"
                   class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow">
                    <i class="fas fa-plus"></i> Nueva empresa
                </a>
            @endif
        </div>

        <div class="overflow-x-auto bg-white rounded-lg shadow mt-4">
            <table class="min-w-full text-sm text-gray-800">
                <thead class="bg-orange-500 text-white text-sm uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">ID</th>
                        <th class="px-4 py-3 text-left">Nombre</th>
                        <th class="px-4 py-3 text-left">Teléfono</th>
                        <th class="px-4 py-3 text-left">Website</th>
                        <th class="px-4 py-3 text-left">Dirección</th>
                        <th class="px-4 py-3 text-left">Descripción</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($empresas as $empresa)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-2">{{ $empresa->EmpresaID }}</td>
                            <td class="px-4 py-2">{{ $empresa->NombreEmpresa }}</td>
                            <td class="px-4 py-2">{{ $empresa->Telefono ?? '—' }}</td>
                            <td class="px-4 py-2">{{ $empresa->Website ?? '—' }}</td>
                            <td class="px-4 py-2">{{ $empresa->Direccion ?? '—' }}</td>
                            <td class="px-4 py-2">{{ $empresa->Descripcion ?? '—' }}</td>
                            <td class="px-4 py-2 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    {{-- Editar --}}
                                    @if($permisos::tienePermiso('Empresas', 'editar'))
                                        <a href="{{ route('empresa.edit', $empresa->EmpresaID) }}"
                                           class="bg-yellow-400 hover:bg-yellow-500 text-white p-2 rounded-full"
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif

                                    {{-- Eliminar --}}
                                    @if($permisos::tienePermiso('Empresas', 'eliminar'))
                                        <form action="{{ route('empresa.destroy', $empresa->EmpresaID) }}" method="POST"
                                              onsubmit="return confirm('¿Deseas eliminar esta empresa?')">
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
    </div>
    
</x-app-layout>