<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold flex items-center gap-2">
            <i class="fas fa-database"></i> Nuevo Backup
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto mt-6 bg-white p-6 rounded-lg shadow">
        <form action="{{ route('backups.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="UsuarioID" class="block text-sm font-semibold mb-1">Usuario ID</label>
                <input type="number" name="UsuarioID" id="UsuarioID" placeholder="Ej: 1"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring" required>
            </div>

            <div class="mb-4">
                <label for="FechaBackup" class="block text-sm font-semibold mb-1">Fecha de Backup</label>
                <input type="datetime-local" name="FechaBackup" id="FechaBackup"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring" required>
            </div>

            <div class="mb-4">
                <label for="NombreArchivo" class="block text-sm font-semibold mb-1">Nombre del Archivo</label>
                <input type="text" name="NombreArchivo" id="NombreArchivo" placeholder="Ej: backup_2023.sql"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring" required>
            </div>

            <div class="mb-4">
                <label for="RutaArchivo" class="block text-sm font-semibold mb-1">Ruta del Archivo</label>
                <input type="text" name="RutaArchivo" id="RutaArchivo" placeholder="Ej: /backups/backup_2023.sql"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring" required>
            </div>

            <div class="mb-4">
                <label for="TamanoMB" class="block text-sm font-semibold mb-1">Tamaño (MB)</label>
                <input type="number" step="0.01" name="TamanoMB" id="TamanoMB" placeholder="Ej: 250.00"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring">
            </div>

            <div class="mb-4">
                <label for="Descripcion" class="block text-sm font-semibold mb-1">Descripción</label>
                <textarea name="Descripcion" id="Descripcion" rows="3"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring"
                    placeholder="Descripción del backup"></textarea>
            </div>

            <div class="flex justify-between mt-6">
                <a href="{{ route('backups.index') }}"
                   class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded inline-flex items-center gap-2">
                    <i class="fas fa-times"></i> Cancelar
                </a>
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-flex items-center gap-2">
                    <i class="fas fa-save"></i> Guardar Backup
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
