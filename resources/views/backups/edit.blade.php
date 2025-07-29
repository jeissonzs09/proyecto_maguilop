<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold flex items-center gap-2">
            <i class="fas fa-database"></i> Editar Backup #{{ $backup->BackupID }}
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto mt-6 bg-white p-6 rounded-lg shadow">
        <form action="{{ route('backups.update', $backup->BackupID) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="UsuarioID" class="block text-sm font-semibold mb-1">Usuario ID</label>
                <input type="number" name="UsuarioID" id="UsuarioID" value="{{ old('UsuarioID', $backup->UsuarioID) }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring" required>
                @error('UsuarioID') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="FechaBackup" class="block text-sm font-semibold mb-1">Fecha de Backup</label>
                <input type="datetime-local" name="FechaBackup" id="FechaBackup"
                       value="{{ old('FechaBackup', \Carbon\Carbon::parse($backup->FechaBackup)->format('Y-m-d\TH:i')) }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring" required>
                @error('FechaBackup') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="NombreArchivo" class="block text-sm font-semibold mb-1">Nombre del Archivo</label>
                <input type="text" name="NombreArchivo" id="NombreArchivo" value="{{ old('NombreArchivo', $backup->NombreArchivo) }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring" required>
                @error('NombreArchivo') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="RutaArchivo" class="block text-sm font-semibold mb-1">Ruta del Archivo</label>
                <input type="text" name="RutaArchivo" id="RutaArchivo" value="{{ old('RutaArchivo', $backup->RutaArchivo) }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring" required>
                @error('RutaArchivo') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="TamanoMB" class="block text-sm font-semibold mb-1">Tamaño (MB)</label>
                <input type="number" step="0.01" min="0" name="TamanoMB" id="TamanoMB"
                       value="{{ old('TamanoMB', $backup->TamanoMB) }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring">
                @error('TamanoMB') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="Descripcion" class="block text-sm font-semibold mb-1">Descripción</label>
                <textarea name="Descripcion" id="Descripcion" rows="3"
                          class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring"
                          placeholder="Descripción del backup">{{ old('Descripcion', $backup->Descripcion) }}</textarea>
                @error('Descripcion') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-between mt-6">
                <a href="{{ route('backups.index') }}"
                   class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded inline-flex items-center gap-2">
                    <i class="fas fa-times"></i> Cancelar
                </a>
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-flex items-center gap-2">
                    <i class="fas fa-save"></i> Actualizar Backup
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
