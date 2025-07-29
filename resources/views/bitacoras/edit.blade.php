<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold flex items-center gap-2">
            <i class="fas fa-edit"></i> Editar Bitácora #{{ $bitacora->BitacoraID }}
        </h2>
    </x-slot>

    <div class="p-6 max-w-3xl mx-auto bg-white shadow-md rounded-md">
        <form action="{{ route('bitacoras.update', $bitacora->BitacoraID) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="UsuarioID" class="block text-sm font-medium text-gray-700">Usuario ID</label>
                <input type="number" name="UsuarioID" id="UsuarioID" value="{{ old('UsuarioID', $bitacora->UsuarioID) }}" required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:ring focus:border-blue-500" />
            </div>

            <div>
                <label for="Accion" class="block text-sm font-medium text-gray-700">Acción</label>
                <input type="text" name="Accion" id="Accion" value="{{ old('Accion', $bitacora->Accion) }}" required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:ring focus:border-blue-500" />
            </div>

            <div>
                <label for="TablaAfectada" class="block text-sm font-medium text-gray-700">Tabla Afectada</label>
                <input type="text" name="TablaAfectada" id="TablaAfectada" value="{{ old('TablaAfectada', $bitacora->TablaAfectada) }}" required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:ring focus:border-blue-500" />
            </div>

            <div>
                <label for="FechaAccion" class="block text-sm font-medium text-gray-700">Fecha de Acción</label>
                <input type="datetime-local" name="FechaAccion" id="FechaAccion"
                    value="{{ old('FechaAccion', \Carbon\Carbon::parse($bitacora->FechaAccion)->format('Y-m-d\TH:i')) }}"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:ring focus:border-blue-500" />
            </div>

            <div>
                <label for="Descripcion" class="block text-sm font-medium text-gray-700">Descripción</label>
                <textarea name="Descripcion" id="Descripcion" rows="3"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:ring focus:border-blue-500">{{ old('Descripcion', $bitacora->Descripcion) }}</textarea>
            </div>

            <div>
                <label for="DatosPrevios" class="block text-sm font-medium text-gray-700">Datos Previos</label>
                <textarea name="DatosPrevios" id="DatosPrevios" rows="3"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:ring focus:border-blue-500">{{ old('DatosPrevios', $bitacora->DatosPrevios) }}</textarea>
            </div>

            <div>
                <label for="DatosNuevos" class="block text-sm font-medium text-gray-700">Datos Nuevos</label>
                <textarea name="DatosNuevos" id="DatosNuevos" rows="3"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:ring focus:border-blue-500">{{ old('DatosNuevos', $bitacora->DatosNuevos) }}</textarea>
            </div>

            <div>
                <label for="Modulo" class="block text-sm font-medium text-gray-700">Módulo</label>
                <input type="text" name="Modulo" id="Modulo" value="{{ old('Modulo', $bitacora->Modulo) }}"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:ring focus:border-blue-500" />
            </div>

            <div>
                <label for="Resultado" class="block text-sm font-medium text-gray-700">Resultado</label>
                <input type="text" name="Resultado" id="Resultado" value="{{ old('Resultado', $bitacora->Resultado) }}"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:ring focus:border-blue-500" />
            </div>

            <div class="mt-6 flex justify-between">
                <a href="{{ route('bitacoras.index') }}"
                   class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded shadow inline-flex items-center gap-2">
                    <i class="fas fa-times"></i> Cancelar
                </a>
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded shadow inline-flex items-center gap-2">
                    <i class="fas fa-save"></i> Actualizar
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
