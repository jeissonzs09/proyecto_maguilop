<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold">Nueva Bitácora</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto mt-6 bg-white p-6 rounded shadow">
        <form action="{{ route('bitacoras.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="UsuarioID" class="block text-sm font-medium text-gray-700">Usuario ID</label>
                <input type="number" name="UsuarioID" id="UsuarioID"
                    value="{{ old('UsuarioID') }}"
                    class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 shadow-sm focus:outline-none focus:ring focus:border-blue-500"
                    required>
            </div>

            <div class="mb-4">
                <label for="Accion" class="block text-sm font-medium text-gray-700">Acción</label>
                <input type="text" name="Accion" id="Accion" value="{{ old('Accion') }}"
                    class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 shadow-sm focus:outline-none focus:ring focus:border-blue-500"
                    required>
            </div>

            <div class="mb-4">
                <label for="TablaAfectada" class="block text-sm font-medium text-gray-700">Tabla Afectada</label>
                <input type="text" name="TablaAfectada" id="TablaAfectada" value="{{ old('TablaAfectada') }}"
                    class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 shadow-sm focus:outline-none focus:ring focus:border-blue-500"
                    required>
            </div>

            <div class="mb-4">
                <label for="FechaAccion" class="block text-sm font-medium text-gray-700">Fecha de Acción</label>
                <input type="datetime-local" name="FechaAccion" id="FechaAccion" value="{{ old('FechaAccion') }}"
                    class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 shadow-sm focus:outline-none focus:ring focus:border-blue-500">
            </div>

            <div class="mb-4">
                <label for="Descripcion" class="block text-sm font-medium text-gray-700">Descripción</label>
                <textarea name="Descripcion" id="Descripcion" rows="3"
                    class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 shadow-sm focus:outline-none focus:ring focus:border-blue-500"
                    required>{{ old('Descripcion') }}</textarea>
            </div>

            <div class="mb-4">
                <label for="DatosPrevios" class="block text-sm font-medium text-gray-700">Datos Previos</label>
                <textarea name="DatosPrevios" id="DatosPrevios" rows="3"
                    class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 shadow-sm focus:outline-none focus:ring focus:border-blue-500">{{ old('DatosPrevios') }}</textarea>
            </div>

            <div class="mb-4">
                <label for="DatosNuevos" class="block text-sm font-medium text-gray-700">Datos Nuevos</label>
                <textarea name="DatosNuevos" id="DatosNuevos" rows="3"
                    class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 shadow-sm focus:outline-none focus:ring focus:border-blue-500">{{ old('DatosNuevos') }}</textarea>
            </div>

            <div class="mb-4">
                <label for="Modulo" class="block text-sm font-medium text-gray-700">Módulo</label>
                <input type="text" name="Modulo" id="Modulo" value="{{ old('Modulo') }}"
                    class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 shadow-sm focus:outline-none focus:ring focus:border-blue-500">
            </div>

            <div class="mb-4">
                <label for="Resultado" class="block text-sm font-medium text-gray-700">Resultado</label>
                <input type="text" name="Resultado" id="Resultado" value="{{ old('Resultado') }}"
                    class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 shadow-sm focus:outline-none focus:ring focus:border-blue-500">
            </div>

            <div class="flex justify-between mt-6">
                <a href="{{ route('bitacoras.index') }}"
                    class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded shadow">
                    Cancelar
                </a>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded shadow">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
