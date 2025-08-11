<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold flex items-center gap-2">
            <i class="fas fa-building"></i> Empresas
        </h2>
    </x-slot>

    @php
        $permisos = \App\Helpers\PermisosHelper::class;
    @endphp

    <div x-data="empresasModal()" class="p-4">

        {{-- Búsqueda y botón Nuevo --}}
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="relative max-w-xs w-full">
                    <input
                        type="text"
                        x-model="search"
                        @input.debounce.="window.location.href = '?search=' + encodeURIComponent(search)"
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

                <a href="{{ route('empresa.exportarPDF', ['search' => request('search')]) }}"
                   class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md shadow whitespace-nowrap">
                    <i class="fas fa-file-pdf"></i> Exportar PDF
                </a>
            </div>

            @if($permisos::tienePermiso('Empresas', 'crear'))
                <button
                    @click="openCreateModal()"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow whitespace-nowrap"
                >
                    <i class="fas fa-plus"></i> Nueva empresa
                </button>
            @endif
        </div>

        {{-- Tabla de empresas --}}
        <div class="overflow-x-auto bg-white rounded-lg shadow mt-4">
            <table class="min-w-full text-sm text-gray-800">
                <thead class="bg-orange-500 text-white text-sm uppercase">
                    <tr>
                        <th class="px-4 py-3 text-center">Empresa ID</th>
                        <th class="px-4 py-3 text-left">Nombre</th>
                        <th class="px-4 py-3 text-left">Teléfono</th>
                        <th class="px-4 py-3 text-left">Website</th>
                        <th class="px-4 py-3 text-left">Dirección</th>
                        <th class="px-4 py-3 text-left">Descripción</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($empresas as $empresa)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-2 text-center">{{ $empresa->EmpresaID }}</td>
                            <td class="px-4 py-2">{{ $empresa->NombreEmpresa }}</td>
                            <td class="px-4 py-2">{{ $empresa->Telefono ?? '—' }}</td>
                            <td class="px-4 py-2">{{ $empresa->Website ?? '—' }}</td>
                            <td class="px-4 py-2">{{ $empresa->Direccion ?? '—' }}</td>
                            <td class="px-4 py-2">{{ $empresa->Descripcion ?? '—' }}</td>
                            <td class="px-4 py-2 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    @if($permisos::tienePermiso('Empresas', 'editar'))
                                        <button
                                            @click="openEditModal({
                                                EmpresaID: '{{ $empresa->EmpresaID }}',
                                                NombreEmpresa: '{{ addslashes($empresa->NombreEmpresa) }}',
                                                Telefono: '{{ addslashes($empresa->Telefono ?? '') }}',
                                                Website: '{{ addslashes($empresa->Website ?? '') }}',
                                                Direccion: '{{ addslashes($empresa->Direccion ?? '') }}',
                                                Descripcion: '{{ addslashes($empresa->Descripcion ?? '') }}'
                                            })"
                                            class="bg-yellow-400 hover:bg-yellow-500 text-white p-2 rounded-full"
                                            title="Editar"
                                        >
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    @endif

                                    @if($permisos::tienePermiso('Empresas', 'eliminar'))
                                        <form action="{{ route('empresa.destroy', $empresa->EmpresaID) }}" method="POST" onsubmit="return confirm('¿Seguro de eliminar esta empresa?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white p-2 rounded-full" title="Eliminar">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-4 text-gray-500">No se encontraron empresas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div class="mt-4">
            {{ $empresas->appends(['search' => request('search')])->links() }}
        </div>

        {{-- Modal Crear --}}
        <div
            x-show="isCreateModalOpen"
            x-transition
            style="display: none;"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
            @keydown.escape.window="closeCreateModal()"
        >
            <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative max-h-[90vh] overflow-y-auto">
                <button
                    @click="closeCreateModal()"
                    class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-2xl font-bold"
                    title="Cerrar"
                >&times;</button>

                <div class="flex items-center gap-2 mb-4 text-blue-600 text-2xl">
                    <i class="fas fa-building"></i>
                    <h3 class="font-semibold">Nueva Empresa</h3>
                </div>

                <form method="POST" action="{{ route('empresa.store') }}" class="space-y-4" novalidate>
                    @csrf

                    <div>
                        <label for="NombreEmpresa" class="block text-gray-700 font-bold mb-2">Nombre</label>
                        <input type="text" name="NombreEmpresa" id="NombreEmpresa" required
                               class="w-full border rounded px-3 py-2"
                               x-bind:value="newEmpresa.NombreEmpresa" />
                        @error('NombreEmpresa')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="Telefono" class="block text-gray-700 font-bold mb-2">Teléfono</label>
                        <input type="text" name="Telefono" id="Telefono"
                               class="w-full border rounded px-3 py-2"
                               x-bind:value="newEmpresa.Telefono" />
                        @error('Telefono')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="Website" class="block text-gray-700 font-bold mb-2">Website</label>
                        <input type="text" name="Website" id="Website"
                               class="w-full border rounded px-3 py-2"
                               x-bind:value="newEmpresa.Website" />
                        @error('Website')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="Direccion" class="block text-gray-700 font-bold mb-2">Dirección</label>
                        <input type="text" name="Direccion" id="Direccion"
                               class="w-full border rounded px-3 py-2"
                               x-bind:value="newEmpresa.Direccion" />
                        @error('Direccion')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="Descripcion" class="block text-gray-700 font-bold mb-2">Descripción</label>
                        <textarea name="Descripcion" id="Descripcion" rows="3"
                                  class="w-full border rounded px-3 py-2"
                                  x-text="newEmpresa.Descripcion"></textarea>
                        @error('Descripcion')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-2">
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
            style="display: none;"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
            @keydown.escape.window="closeEditModal()"
        >
            <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative max-h-[90vh] overflow-y-auto">
                <button
                    @click="closeEditModal()"
                    class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-2xl font-bold"
                    title="Cerrar"
                >&times;</button>

                <div class="flex items-center gap-2 mb-4 text-yellow-500 text-2xl">
                    <i class="fas fa-building"></i>
                    <h3 x-text="'Editar Empresa #' + editEmpresa.EmpresaID" class="font-semibold"></h3>
                </div>

                <form method="POST" :action="`{{ url('empresa') }}/${editEmpresa.EmpresaID}`" class="space-y-4" novalidate>
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="NombreEmpresaEdit" class="block text-gray-700 font-bold mb-2">Nombre</label>
                        <input type="text" name="NombreEmpresa" id="NombreEmpresaEdit" required
                               class="w-full border rounded px-3 py-2"
                               x-model="editEmpresa.NombreEmpresa" />
                        @error('NombreEmpresa')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="TelefonoEdit" class="block text-gray-700 font-bold mb-2">Teléfono</label>
                        <input type="text" name="Telefono" id="TelefonoEdit"
                               class="w-full border rounded px-3 py-2"
                               x-model="editEmpresa.Telefono" />
                        @error('Telefono')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="WebsiteEdit" class="block text-gray-700 font-bold mb-2">Website</label>
                        <input type="text" name="Website" id="WebsiteEdit"
                               class="w-full border rounded px-3 py-2"
                               x-model="editEmpresa.Website" />
                        @error('Website')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="DireccionEdit" class="block text-gray-700 font-bold mb-2">Dirección</label>
                        <input type="text" name="Direccion" id="DireccionEdit"
                               class="w-full border rounded px-3 py-2"
                               x-model="editEmpresa.Direccion" />
                        @error('Direccion')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="DescripcionEdit" class="block text-gray-700 font-bold mb-2">Descripción</label>
                        <textarea name="Descripcion" id="DescripcionEdit" rows="3"
                                  class="w-full border rounded px-3 py-2"
                                  x-model="editEmpresa.Descripcion"></textarea>
                        @error('Descripcion')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-2">
                        <button type="button" @click="closeEditModal()"
                                class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400">Cancelar
                        </button>
                        <button type="submit"
                                class="px-4 py-2 rounded bg-yellow-500 text-white hover:bg-yellow-600">Actualizar
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    {{-- Alpine.js --}}
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <script>
        function empresasModal() {
            return {
                search: '{{ request('search') }}',
                isCreateModalOpen: {{ $errors->any() ? 'true' : 'false' }},
                isEditModalOpen: false,
                newEmpresa: {
                    NombreEmpresa: '',
                    Telefono: '',
                    Website: '',
                    Direccion: '',
                    Descripcion: ''
                },
                editEmpresa: {
                    EmpresaID: '',
                    NombreEmpresa: '',
                    Telefono: '',
                    Website: '',
                    Direccion: '',
                    Descripcion: ''
                },

                openCreateModal() {
                    this.isCreateModalOpen = true;
                    // reset campos
                    this.newEmpresa = {
                        NombreEmpresa: '',
                        Telefono: '',
                        Website: '',
                        Direccion: '',
                        Descripcion: ''
                    };
                },
                closeCreateModal() {
                    this.isCreateModalOpen = false;
                },

                openEditModal(empresa) {
                    this.editEmpresa = {
                        EmpresaID: parseInt(empresa.EmpresaID),
                        NombreEmpresa: empresa.NombreEmpresa ?? '',
                        Telefono: empresa.Telefono ?? '',
                        Website: empresa.Website ?? '',
                        Direccion: empresa.Direccion ?? '',
                        Descripcion: empresa.Descripcion ?? ''
                    };
                    this.isEditModalOpen = true;
                },
                closeEditModal() {
                    this.isEditModalOpen = false;
                    this.editEmpresa = {
                        EmpresaID: '',
                        NombreEmpresa: '',
                        Telefono: '',
                        Website: '',
                        Direccion: '',
                        Descripcion: ''
                    };
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