<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold flex items-center gap-2">
            <i class="fas fa-database"></i> Backups
        </h2>
    </x-slot>

    @php
        $permisos = \App\Helpers\PermisosHelper::class;
    @endphp

    <div class="p-4">

        @if(session('success'))
            <div class="mb-4 bg-green-100 text-green-700 px-4 py-3 rounded-md shadow">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 bg-red-100 text-red-700 px-4 py-3 rounded-md shadow">
                {{ session('error') }}
            </div>
        @endif

        {{-- Formulario para crear backup --}}
        @if($permisos::tienePermiso('Backups', 'crear'))
            <form method="POST" action="{{ route('backups.store') }}" class="mb-6 flex flex-col sm:flex-row gap-3 items-start sm:items-end">
                @csrf
                <div>
                    <label for="descripcion" class="block text-sm font-semibold">Descripción del backup</label>
                    <input type="text" name="descripcion" id="descripcion" class="border border-gray-300 rounded px-3 py-2 shadow-sm w-full" required>
                </div>
                <div>
                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow w-full sm:w-auto">
                        <i class="fas fa-save mr-1"></i> Nuevo Backup
                    </button>
                </div>
            </form>
        @endif

        {{-- Tabla de backups --}}
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full text-sm text-gray-800">
                <thead class="bg-orange-500 text-white uppercase text-sm">
                    <tr>
                        <th class="px-4 py-3 text-left">ID</th>
                        <th class="px-4 py-3 text-left">Usuario</th>
                        <th class="px-4 py-3 text-left">Fecha</th>
                        <th class="px-4 py-3 text-left">Descripción</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($backups as $backup)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-2">{{ $backup->BackupID }}</td>
                            <td class="px-4 py-2">{{ $backup->usuario }}</td>
                            <td class="px-4 py-2">{{ \Carbon\Carbon::parse($backup->FechaBackup)->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-2">{{ $backup->Descripcion }}</td>
                            <td class="px-4 py-2 text-center">
                                <div class="flex justify-center gap-2">
                                    {{-- Restaurar --}}
                                    @if($permisos::tienePermiso('Backups', 'restaurar'))
                                        <form action="{{ route('backups.restore', $backup->BackupID) }}" method="POST" onsubmit="return confirm('¿Restaurar este backup?')">
                                            @csrf
                                            <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white p-2 rounded-full" title="Restaurar">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Descargar --}}
                                    <a href="{{ asset('storage/backups/' . $backup->NombreArchivo) }}"
                                       download class="bg-green-600 hover:bg-green-700 text-white p-2 rounded-full"
                                       title="Descargar">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-3 text-center text-gray-500">No hay backups registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</x-app-layout>




