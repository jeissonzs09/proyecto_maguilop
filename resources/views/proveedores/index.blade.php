<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold flex items-center gap-2">
            <i class="fas fa-industry"></i> Proveedores
        </h2>
    </x-slot>

    @php
        $permisos = \App\Helpers\PermisosHelper::class;
    @endphp

    <div class="p-4">
        {{-- Mensajes --}}
        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded shadow text-sm">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded shadow text-sm">
                <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
            </div>
        @endif

        {{-- Botones --}}
        <div class="flex items-center justify-between mb-4 mt-4">
            <div class="flex items-center gap-3">
                {{-- Buscador --}}
                <div class="relative max-w-xs w-full">
                    <input
                        type="text"
                        x-data="{ search: '{{ request('search') }}' }"
                        x-model="search"
                        @input.debounce.500="window.location.href = '?search=' + encodeURIComponent(search)"
                        placeholder="Buscar proveedores..."
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
                <a href="{{ route('proveedores.exportarPDF', ['search' => request('search')]) }}"
                   class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md shadow whitespace-nowrap">
                    <i class="fas fa-file-pdf"></i> Exportar PDF
                </a>
            </div>

            {{-- Botón Nuevo Proveedor alineado a la derecha --}}
            @if($permisos::tienePermiso('Proveedores', 'crear'))
                <a href="{{ route('proveedores.create') }}"
                   class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow font-semibold">
                    <i class="fas fa-plus"></i> Nuevo Proveedor
                </a>
            @endif
        </div>

        {{-- Tabla de proveedores --}}
        <div class="overflow-x-auto bg-white rounded-lg shadow mt-4">
            <table id="empresa-table" class="min-w-full text-sm text-gray-800">
                <thead class="bg-orange-500 text-white uppercase text-sm">
                    <tr>
                        <th class="px-4 py-3 text-center">ID</th>
                        <th class="px-4 py-3 text-center">Persona</th>
                        <th class="px-4 py-3 text-center">Empresa</th>
                        <th class="px-4 py-3 text-center">RTN</th>
                        <th class="px-4 py-3 text-left">Descripción</th>
                        <th class="px-4 py-3 text-left">Sitio Web</th>
                        <th class="px-4 py-3 text-left">Ubicación</th>
                        <th class="px-4 py-3 text-left">Teléfono</th>
                        <th class="px-4 py-3 text-left">Correo</th>
                        <th class="px-4 py-3 text-left">Tipo</th>
                        <th class="px-4 py-3 text-center">Registro</th>
                        <th class="px-4 py-3 text-center">Estado</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($proveedores as $proveedor)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 text-center">{{ $proveedor->ProveedorID }}</td>
                            <td class="px-4 py-2 text-center">{{ $proveedor->persona->NombreCompleto ?? '—' }}</td>
                            <td class="px-4 py-2 text-center">{{ $proveedor->empresa->NombreEmpresa ?? '—' }}</td>
                            <td class="px-4 py-2 text-center">{{ $proveedor->RTN }}</td>
                            <td class="px-4 py-2">{{ $proveedor->Descripcion }}</td>
                            <td class="px-4 py-2">{{ $proveedor->URL_Website }}</td>
                            <td class="px-4 py-2">{{ $proveedor->Ubicacion }}</td>
                            <td class="px-4 py-2">{{ $proveedor->Telefono }}</td>
                            <td class="px-4 py-2">{{ $proveedor->CorreoElectronico }}</td>
                            <td class="px-4 py-2">{{ $proveedor->TipoProveedor }}</td>
                            <td class="px-4 py-2 text-center">{{ $proveedor->FechaRegistro }}</td>
                            <td class="px-4 py-2 text-center">{{ $proveedor->Estado }}</td>
                            <td class="px-4 py-2 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    @if($permisos::tienePermiso('Proveedores', 'editar'))
                                        <a href="{{ route('proveedores.edit', $proveedor->ProveedorID) }}"
                                           class="bg-yellow-400 hover:bg-yellow-500 text-white p-2 rounded-full"
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif

                                    @if($permisos::tienePermiso('Proveedores', 'eliminar'))
                                        <form action="{{ route('proveedores.destroy', $proveedor->ProveedorID) }}" method="POST"
                                              onsubmit="return confirm('¿Estás seguro de eliminar este proveedor?')">
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

        {{-- Paginación --}}
        <div class="mt-4">
            {{ $proveedores->appends(['search' => request('search')])->links() }}
        </div>
    </div>
</x-app-layout>